<?php

namespace App\Controllers;

use App\Config\PasswordReset;
use App\Models\User;
use App\Models\Company;
use App\Services\AuditLogger;
use App\Services\CompanyPlan;

class AuthController extends Controller
{

    public function showRegister()
    {
        $selected_plan = CompanyPlan::normalize($_GET['plan'] ?? 'starter');
        $this->view('auth/register', ['selected_plan' => $selected_plan]);
    }

    public function register()
    {
        // Basic validation
        $company_name = $_POST['company_name'] ?? '';
        $user_name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $plan = CompanyPlan::normalize($_POST['plan'] ?? 'starter');

        if (empty($company_name) || empty($user_name) || empty($email) || empty($password)) {
            $this->view('auth/register', [
                'error' => 'All fields are required.',
                'selected_plan' => $plan,
            ]);
            return;
        }

        $company = new Company();
        if ($company->exists($email)) { // checking if company email exists, logic might need adjustment if company email != admin email
            // simplified: using admin email as company identifier for duplicate check for now
            // Or we could check duplicate company names.
        }

        $user = new User();
        if ($user->emailExists($email)) {
            $this->view('auth/register', [
                'error' => 'Email already registered.',
                'selected_plan' => $plan,
            ]);
            return;
        }

        // Start Transaction
        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $conn->beginTransaction();

        try {
            // 1. Create Company
            $company->name = $company_name;
            $company->email = $email;
            $company->subscription_status = 'trial';
            $company->plan = $plan;
            $company->trial_ends_at = date('Y-m-d H:i:s', strtotime('+3 months'));

            if (!$company->create()) {
                throw new \Exception("Failed to create company.");
            }

            // 2. Create Admin User
            $user->company_id = $company->id;
            $user->name = $user_name;
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->role = 'company_admin';

            if (!$user->create()) {
                throw new \Exception("Failed to create user.");
            }

            $conn->commit();

            AuditLogger::log("Account Registered", "Company: $company_name, Email: $email");

            // Auto-login
            $_SESSION['user_id'] = $user->id; // We'd need to get the last insert ID from User model or query it
            // User model create() returns boolean, doesn't set ID. 
            // For now, let's redirect to login.

            $this->redirect('/login');

        } catch (\Exception $e) {
            $conn->rollBack();
            // Log the error for debugging
            error_log("Registration Error: " . $e->getMessage());
            $this->view('auth/register', [
                'error' => 'Registration failed: ' . $e->getMessage(),
                'selected_plan' => $plan,
            ]);
        }
    }

    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->view('auth/login', ['error' => 'Email and password are required.']);
            return;
        }

        $user = new User();
        if (!$user->isConnected()) {
            $this->view('auth/login', ['error' => 'Database is unavailable. Link MySQL variables on Railway and redeploy.']);
            return;
        }
        $loggedInUser = $user->login($email, $password);

        if ($loggedInUser) {
            if (!empty($loggedInUser['invite_pending'])) {
                $this->view('auth/login', [
                    'error' => 'Please set your password using the link in your invitation email before signing in.',
                ]);
                return;
            }
            // Check if company is suspended
            if ($loggedInUser['company_id']) {
                $db = new \App\Config\Database();
                $conn = $db->getConnection();
                $stmt = $conn->prepare("SELECT account_status, plan FROM companies WHERE id = :id");
                $stmt->bindParam(':id', $loggedInUser['company_id']);
                $stmt->execute();
                $company = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($company && $company['account_status'] === 'suspended') {
                    $this->view('auth/login', ['error' => 'Your account has been suspended. Please contact support.']);
                    return;
                }
                if ($company) {
                    $_SESSION['company_plan'] = CompanyPlan::normalize($company['plan'] ?? 'professional');
                }
            }

            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['company_id'] = $loggedInUser['company_id'];
            $_SESSION['role'] = $loggedInUser['role'];
            $_SESSION['name'] = $loggedInUser['name'];
            $_SESSION['email'] = $loggedInUser['email'];
            if (empty($loggedInUser['company_id'])) {
                unset($_SESSION['company_plan']);
            }

            AuditLogger::log("Login Success", "Email: {$loggedInUser['email']}, Role: {$loggedInUser['role']}");
            $this->redirect('/dashboard');
        } else {
            AuditLogger::log("Login Failed", "Email attempt: $email");
            $this->view('auth/login', ['error' => 'Invalid credentials.']);
        }
    }

    public function logout()
    {
        AuditLogger::log("Logout");
        session_destroy();
        $this->redirect('/');
    }

    public function showForgotPassword()
    {
        $this->view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $this->view('auth/forgot_password', ['error' => 'Email is required.']);
            return;
        }

        $user = new User();
        if (!$user->emailExists($email)) {
            // Show success anyway to prevent email harvesting
            $this->view('auth/forgot_password', ['success' => 'If this email is registered, you will receive a reset link shortly.']);
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->view('auth/forgot_password', ['error' => 'Database unavailable. Please try again later.']);
            return;
        }

        $this->ensurePasswordResetTable($conn);

        $token = bin2hex(random_bytes(32));
        $resetUrl = $this->buildResetUrl($token);

        try {
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $email]);

            $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
            $stmt->execute(['email' => $email, 'token' => $token]);

            $subject = "Reset Your Password";
            $body = "We received a request to reset your password. If you didn't make this request, you can safely ignore this email.";
            $emailSent = \App\Services\EmailService::send($email, $subject, $body, "Reset Password", $resetUrl);

            AuditLogger::log("Password Reset Requested", "Email: $email");

            $viewData = [
                'success' => 'If this email is registered, you will receive a reset link shortly.',
            ];
            if (!$emailSent) {
                $viewData['delivery_warning'] = 'Email delivery is not configured on this server yet. Use the reset link below.';
                $viewData['reset_url'] = $resetUrl;
            }

            $this->view('auth/forgot_password', $viewData);
        } catch (\Throwable $e) {
            error_log("Password reset request failed: " . $e->getMessage());
            $this->view('auth/forgot_password', [
                'error' => 'Unable to create a reset link right now. Please try again shortly.',
            ]);
        }
    }

    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/login');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->view('auth/forgot_password', ['error' => 'Database unavailable. Please try again later.']);
            return;
        }

        $this->ensurePasswordResetTable($conn);
        if (!$this->findValidResetByToken($conn, $token)) {
            $this->view('auth/forgot_password', ['error' => 'That reset link is invalid or has expired. Request a new one.']);
            return;
        }

        $this->view('auth/reset_password', ['token' => $token]);
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($password) || $password !== $confirm) {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Passwords must match and cannot be empty.']);
            return;
        }
        if (strlen($password) < 8) {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Password must be at least 8 characters long.']);
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Database unavailable. Please try again later.']);
            return;
        }

        $this->ensurePasswordResetTable($conn);

        $reset = $this->findValidResetByToken($conn, $token);

        if (!$reset) {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Invalid or expired token. Please request a new link.']);
            return;
        }

        $email = $reset['email'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
        if ($stmt->execute(['password' => $hashedPassword, 'email' => $email])) {
            // Delete token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $email]);

            try {
                $stmt = $conn->prepare('UPDATE users SET invite_pending = 0 WHERE email = :email');
                $stmt->execute(['email' => $email]);
            } catch (\Throwable $e) {
                // Column may be missing on unmigrated DBs
            }

            AuditLogger::log("Password Changed", "Email: $email");

            $this->view('auth/login', ['success' => 'Password reset successfully. You can now log in.']);
        } else {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Failed to reset password.']);
        }
    }

    private function buildResetUrl(string $token): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = getenv('SAAS_BASE_PATH');
        if ($basePath === false || $basePath === '') {
            $basePath = '/saas';
        }

        return $scheme . '://' . $host . rtrim($basePath, '/') . '/reset-password?token=' . urlencode($token);
    }

    private function ensurePasswordResetTable(\PDO $conn): void
    {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(64) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function findValidResetByToken(\PDO $conn, string $token): ?array
    {
        $h = (int) PasswordReset::TOKEN_TTL_HOURS;
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND created_at >= DATE_SUB(NOW(), INTERVAL {$h} HOUR) LIMIT 1");
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $reset ?: null;
    }
}
