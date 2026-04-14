<?php

namespace App\Controllers;

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

        // Fetch users for this company
        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE company_id = :company_id ORDER BY created_at DESC");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('users/index', ['users' => $users]);
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
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'evaluator'; // Default to evaluator for invited users

        if (empty($name) || empty($email) || empty($password)) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'All fields are required.']));
            return;
        }

        if ($conn === null) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Database unavailable.']));
            return;
        }

        // Check if email exists
        $userModel = new User();
        // userModel->emailExists($email) method needed? 
        // User model in this project might just be basic. Let's do a direct check or use model if available.
        // Looking at User.php earlier, it has create() but maybe not exists check public.
        // Let's rely on database unique constraint or check manually.

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Email already registered.']));
            return;
        }

        // Create User
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (company_id, name, email, password, role) VALUES (:company_id, :name, :email, :password, :role)");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            // Send Welcome/Invitation Email
            $loginUrl = "http://" . $_SERVER['HTTP_HOST'] . "/saas/login";
            \App\Services\EmailService::send(
                $email,
                "You've been invited to " . \App\Config\Settings::get('site_name', 'SupplierEval'),
                "Hello {$name}, you have been added to the platform as a " . ucfirst($role) . ". You can now log in using your email and the password provided by your administrator.",
                "Log In to Your Account",
                $loginUrl
            );

            $this->redirect('/users');
        } else {
            $this->view('users/create', array_merge($userCtx, ['error' => 'Failed to create user.']));
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_admin') {
            $this->redirect('/dashboard');
            return;
        }
        // Deleting users? Maybe allowed even if expired, but let's enforce sub for consistency/security
        $this->enforceSubscription();

        $id = $_POST['id'] ?? null;

        // Prevent deleting self
        if ($id == $_SESSION['user_id']) {
            $this->redirect('/users'); // Silently fail or show error
            return;
        }

        if ($id) {
            $db = new \App\Config\Database();
            $conn = $db->getConnection();
            // Ensure we delete only users from OUR company
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id AND company_id = :company_id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':company_id', $_SESSION['company_id']);
            $stmt->execute();
        }
        $this->redirect('/users');
    }
}
