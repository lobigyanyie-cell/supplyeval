<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Services\AuditLogger;

class AuthController extends Controller
{

    public function showRegister()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        // Basic validation
        $company_name = $_POST['company_name'] ?? '';
        $user_name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($company_name) || empty($user_name) || empty($email) || empty($password)) {
            $this->view('auth/register', ['error' => 'All fields are required.']);
            return;
        }

        $company = new Company();
        if ($company->exists($email)) { // checking if company email exists, logic might need adjustment if company email != admin email
            // simplified: using admin email as company identifier for duplicate check for now
            // Or we could check duplicate company names.
        }

        $user = new User();
        if (!$user->isConnected()) {
            $this->view('auth/register', ['error' => 'Cannot connect to the database. On Railway, link MySQL and reference MYSQL_URL or MYSQLHOST (see RAILWAY.md).']);
            return;
        }
        if ($user->emailExists($email)) {
            $this->view('auth/register', ['error' => 'Email already registered.']);
            return;
        }

        // Start Transaction
        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->view('auth/register', ['error' => 'Cannot connect to the database.']);
            return;
        }
        $conn->beginTransaction();

        try {
            // 1. Create Company
            $company->name = $company_name;
            $company->email = $email;
            $company->subscription_status = 'trial';
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
            $this->view('auth/register', ['error' => 'Registration failed: ' . $e->getMessage()]);
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
            $this->view('auth/login', [
                'error' => 'Cannot connect to the database. On Railway: Web service → Variables → add reference to MYSQL_URL from your MySQL service (or MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE), redeploy, and import your SQL into that database (name in MYSQL_URL / MYSQLDATABASE).',
            ]);
            return;
        }
        $loggedInUser = $user->login($email, $password);

        if ($loggedInUser) {
            // Check if company is suspended
            if ($loggedInUser['company_id']) {
                $db = new \App\Config\Database();
                $conn = $db->getConnection();
                if ($conn === null) {
                    $this->view('auth/login', ['error' => 'Database unavailable.']);
                    return;
                }
                $stmt = $conn->prepare("SELECT account_status FROM companies WHERE id = :id");
                $stmt->bindParam(':id', $loggedInUser['company_id']);
                $stmt->execute();
                $company = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($company && $company['account_status'] === 'suspended') {
                    $this->view('auth/login', ['error' => 'Your account has been suspended. Please contact support.']);
                    return;
                }
            }

            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['company_id'] = $loggedInUser['company_id'];
            $_SESSION['role'] = $loggedInUser['role'];
            $_SESSION['name'] = $loggedInUser['name'];
            $_SESSION['email'] = $loggedInUser['email'];

            AuditLogger::log("Login Success");
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
        $email = $_POST['email'] ?? '';
        if (empty($email)) {
            $this->view('auth/forgot_password', ['error' => 'Email is required.']);
            return;
        }

        $user = new User();
        if (!$user->isConnected()) {
            $this->view('auth/forgot_password', ['error' => 'Cannot connect to the database.']);
            return;
        }
        if (!$user->emailExists($email)) {
            // Show success anyway to prevent email harvesting
            $this->view('auth/forgot_password', ['success' => 'If this email is registered, you will receive a reset link shortly.']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->view('auth/forgot_password', ['error' => 'Cannot connect to the database.']);
            return;
        }

        // Store token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute(['email' => $email, 'token' => $token]);

        // Send Email
        $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/saas/reset-password?token=$token";

        $subject = "Reset Your Password";
        $body = "We received a request to reset your password. If you didn't make this request, you can safely ignore this email.";


        \App\Services\EmailService::send($email, $subject, $body, "Reset Password", $resetUrl);

        AuditLogger::log("Password Reset Requested", "Email: $email");

        $this->view('auth/forgot_password', ['success' => 'If this email is registered, you will receive a reset link shortly.']);
    }

    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirect('/login');
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

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        // Validate token (1 hour expiry)
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 1");
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

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

            AuditLogger::log("Password Changed", "Email: $email");

            $this->view('auth/login', ['success' => 'Password reset successfully. You can now log in.']);
        } else {
            $this->view('auth/reset_password', ['token' => $token, 'error' => 'Failed to reset password.']);
        }
    }
}
