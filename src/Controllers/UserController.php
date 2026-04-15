<?php

namespace App\Controllers;

use App\Config\PasswordReset;
use App\Models\User;
use App\Services\CompanyPlan;

class UserController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT u.*,
                (SELECT pr.created_at FROM password_resets pr WHERE pr.email = u.email ORDER BY pr.created_at DESC LIMIT 1) AS invite_token_at
            FROM users u
            WHERE u.company_id = :company_id
            ORDER BY u.created_at DESC
        ");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ttlHours = PasswordReset::TOKEN_TTL_HOURS;
        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->enrichUserInviteRow($row, $ttlHours);
        }

        $this->view('users/index', ['users' => $users, 'invite_ttl_hours' => $ttlHours]);
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function enrichUserInviteRow(array $row, int $ttlHours): array
    {
        $pending = array_key_exists('invite_pending', $row) && (int) $row['invite_pending'] === 1;
        $row['invite_pending'] = $pending;
        $row['invite_status_label'] = $pending ? 'Pending' : 'Active';
        $row['invite_detail'] = '';
        $row['invite_can_resend'] = false;

        if (!$pending) {
            return $row;
        }

        $tokenAt = $row['invite_token_at'] ?? null;
        if ($tokenAt === null || $tokenAt === '') {
            $row['invite_detail'] = 'No active invite link — resend to generate a new one.';
            $row['invite_can_resend'] = true;
            return $row;
        }

        $created = strtotime((string) $tokenAt);
        $expires = $created + ($ttlHours * 3600);
        $now = time();
        if ($now > $expires) {
            $row['invite_detail'] = 'Invite link expired — resend a new invite.';
            $row['invite_can_resend'] = true;
        } else {
            $row['invite_detail'] = 'Invite expires ' . date('M j, Y g:i A', $expires) . ' (about ' . max(1, (int) ceil(($expires - $now) / 3600)) . 'h left).';
            $row['invite_can_resend'] = true;
        }

        return $row;
    }

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }
        $this->enforceSubscription(); // Adding users is a write action

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $plan = CompanyPlan::current();
        $maxU = CompanyPlan::maxUsers($plan);
        $uc = $conn ? CompanyPlan::userCount($conn, (int) $_SESSION['company_id']) : 0;
        $this->view('users/create', [
            'user_slots_remaining' => $maxU !== null ? max(0, $maxU - $uc) : null,
            'plan_max_users' => $maxU,
        ]);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }
        $this->enforceSubscription();

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $plan = CompanyPlan::current();
        $maxU = CompanyPlan::maxUsers($plan);
        $uc = $conn ? CompanyPlan::userCount($conn, (int) $_SESSION['company_id']) : 0;
        $userCtx = [
            'user_slots_remaining' => $maxU !== null ? max(0, $maxU - $uc) : null,
            'plan_max_users' => $maxU,
        ];
        if ($conn !== null && $maxU !== null && $uc >= $maxU) {
            $this->view('users/create', array_merge($userCtx, [
                'error' => "Your plan allows up to {$maxU} team member(s). Upgrade to add more users.",
                'user_slots_remaining' => 0,
            ]));
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'evaluator';

        if (empty($name) || empty($email) || empty($role)) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Name, email, and role are required.']));
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Please enter a valid email address.']));
            return;
        }
        $allowedRoles = ['company_admin', 'evaluator', 'viewer'];
        if (!in_array($role, $allowedRoles, true)) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Invalid role selected.']));
            return;
        }

        if ($conn === null) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Database unavailable.']));
            return;
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Email already registered.']));
            return;
        }

        $tempPassword = bin2hex(random_bytes(16));
        $hashed_password = password_hash($tempPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare(
            "INSERT INTO users (company_id, name, email, password, role, invite_pending) VALUES (:company_id, :name, :email, :password, :role, 1)"
        );
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $this->ensurePasswordResetTable($conn);
            $token = bin2hex(random_bytes(32));
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
            $stmt->execute(['email' => $email, 'token' => $token]);

            $setPasswordUrl = $this->buildResetUrl($token);
            \App\Services\EmailService::send(
                $email,
                "You're invited to " . \App\Config\Settings::get('site_name', 'SupplierEval'),
                "Hello {$name}, you have been added to the platform as a " . ucfirst($role) . ". Please set your password to activate your account.",
                "Set Your Password",
                $setPasswordUrl
            );

            $this->redirect('/users?invited=' . rawurlencode($email));
        } else {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Failed to create user.']));
        }
    }

    public function resendInvite()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }
        $this->enforceSubscription();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1 || $id === (int) $_SESSION['user_id']) {
            $this->redirect('/users');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        if ($conn === null) {
            $this->redirect('/users?resend=error');
            return;
        }

        $stmt = $conn->prepare(
            "SELECT id, company_id, name, email, role, invite_pending FROM users WHERE id = :id AND company_id = :cid LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'cid' => $_SESSION['company_id']]);
        $u = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$u || empty($u['invite_pending'])) {
            $this->redirect('/users?resend=invalid');
            return;
        }

        $this->ensurePasswordResetTable($conn);
        $token = bin2hex(random_bytes(32));
        $email = $u['email'];
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute(['email' => $email, 'token' => $token]);

        $setPasswordUrl = $this->buildResetUrl($token);
        \App\Services\EmailService::send(
            $email,
            "You're invited to " . \App\Config\Settings::get('site_name', 'SupplierEval'),
            "Hello {$u['name']}, please set your password to activate your account.",
            "Set Your Password",
            $setPasswordUrl
        );

        $this->redirect('/users?resent=' . rawurlencode($email));
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }
        $this->enforceSubscription();

        $id = $_POST['id'] ?? null;

        if ($id == $_SESSION['user_id']) {
            $this->redirect('/users');
            return;
        }

        if ($id) {
            $db = new \App\Config\Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id AND company_id = :company_id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':company_id', $_SESSION['company_id']);
            $stmt->execute();
        }
        $this->redirect('/users');
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
}
