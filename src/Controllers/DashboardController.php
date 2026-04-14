<?php

namespace App\Controllers;

use App\Config\Database;
use App\Config\Settings;
use PDO;
use App\Services\AuditLogger;

class DashboardController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $role = $_SESSION['role'];
        $company_id = $_SESSION['company_id'];

        // Determine which dashboard to show based on role
        if ($role === 'system_admin') {
            // Fetch System Stats
            $db = new Database();
            $conn = $db->getConnection();

            // Total Companies
            $stmt = $conn->query("SELECT COUNT(*) as count FROM companies");
            $total_companies = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total Users
            $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
            $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Recent Companies
            $stmt = $conn->query("SELECT * FROM companies ORDER BY created_at DESC LIMIT 5");
            $recent_companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Total Revenue
            $stmt = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE status = 'succeeded'");
            $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Recent Transactions
            $stmt = $conn->query("SELECT t.*, c.name as company_name 
                                  FROM transactions t 
                                  JOIN companies c ON t.company_id = c.id 
                                  ORDER BY t.created_at DESC LIMIT 5");
            $recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Revenue Trends (Last 6 Months)
            $stmt = $conn->query("SELECT DATE_FORMAT(MIN(created_at), '%b %Y') as month, SUM(amount) as total 
                                  FROM transactions 
                                  WHERE status = 'succeeded' 
                                  GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                                  ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC LIMIT 6");
            $revenue_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tenant Distribution
            $stmt = $conn->query("SELECT subscription_status, COUNT(*) as count FROM companies GROUP BY subscription_status");
            $tenant_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Growth Calculation (This Month vs Last Month)
            $this_month = date('Y-m');
            $last_month = date('Y-m', strtotime('first day of last month'));

            $stmt = $conn->prepare("SELECT SUM(amount) as total FROM transactions WHERE status = 'succeeded' AND DATE_FORMAT(created_at, '%Y-%m') = :m");
            $stmt->execute(['m' => $this_month]);
            $current_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            $stmt->execute(['m' => $last_month]);
            $previous_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            $growth = ($previous_revenue > 0) ? (($current_revenue - $previous_revenue) / $previous_revenue) * 100 : 0;

            $this->view('dashboard/system_admin', [
                'total_companies' => $total_companies,
                'total_users' => $total_users,
                'recent_companies' => $recent_companies,
                'total_revenue' => $total_revenue,
                'recent_transactions' => $recent_transactions,
                'revenue_trends' => $revenue_trends,
                'tenant_stats' => $tenant_stats,
                'growth' => $growth
            ]);
        } else {
            // Show Company Dashboard (Admin/Evaluator/Viewer)
            $db = new Database();
            $conn = $db->getConnection();

            // Total Suppliers
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM suppliers WHERE company_id = :cid");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $total_suppliers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total Evaluations (submitted only; drafts excluded)
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM evaluations WHERE company_id = :cid AND status = 'submitted'");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $total_evaluations = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Average Score
            $stmt = $conn->prepare("SELECT AVG(total_score) as avg_score FROM evaluations WHERE company_id = :cid AND status = 'submitted'");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $avg_score = $stmt->fetch(PDO::FETCH_ASSOC)['avg_score'];

            // Recent Evals
            $stmt = $conn->prepare("SELECT e.*, s.name as supplier_name, u.name as evaluator_name 
                                    FROM evaluations e 
                                    JOIN suppliers s ON e.supplier_id = s.id 
                                    JOIN users u ON e.evaluator_id = u.id 
                                    WHERE e.company_id = :cid AND e.status = 'submitted'
                                    ORDER BY e.created_at DESC LIMIT 5");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $recent_evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 1. Performance Trends (Last 6 Months Average)
            $stmt = $conn->prepare("SELECT DATE_FORMAT(MIN(created_at), '%b %Y') as month, ROUND(AVG(total_score), 1) as avg
                                    FROM evaluations 
                                    WHERE company_id = :cid AND status = 'submitted'
                                    GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                                    ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC LIMIT 6");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $performance_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Supplier Distribution by Performance Tier
            // Logic: Count suppliers based on their CURRENT average score
            $stmt = $conn->prepare("
                SELECT tier, COUNT(*) as count FROM (
                    SELECT 
                        CASE 
                            WHEN AVG(e.total_score) >= 80 THEN 'Excellent'
                            WHEN AVG(e.total_score) >= 50 THEN 'Good'
                            ELSE 'At Risk'
                        END as tier
                    FROM suppliers s
                    JOIN evaluations e ON s.id = e.supplier_id AND e.status = 'submitted'
                    WHERE s.company_id = :cid
                    GROUP BY s.id
                ) t GROUP BY tier
            ");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
            $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Suppliers Due for Evaluation
            $supplierModel = new \App\Models\Supplier();
            $stmt = $supplierModel->getDueForEvaluation($company_id);
            $overdue_suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->view('dashboard/company_dashboard', [
                'role' => $role,
                'total_suppliers' => $total_suppliers,
                'total_evaluations' => $total_evaluations,
                'avg_score' => $avg_score,
                'recent_evaluations' => $recent_evaluations,
                'performance_trends' => $performance_trends,
                'distribution' => $distribution,
                'overdue_suppliers' => $overdue_suppliers
            ]);
        }
    }

    public function companies()
    {
        $this->verifySystemAdmin();

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        $stmt = $conn->query("SELECT * FROM companies ORDER BY created_at DESC");
        $companies = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('dashboard/system/companies', ['companies' => $companies]);
    }

    public function users()
    {
        $this->verifySystemAdmin();

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        $stmt = $conn->query("SELECT u.*, c.name as company_name FROM users u LEFT JOIN companies c ON u.company_id = c.id ORDER BY u.created_at DESC");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('dashboard/system/users', ['users' => $users]);
    }

    public function globalTransactions()
    {
        $this->verifySystemAdmin();

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        $stmt = $conn->query("SELECT t.*, c.name as company_name 
                              FROM transactions t 
                              JOIN companies c ON t.company_id = c.id 
                              ORDER BY t.created_at DESC");
        $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('dashboard/system/transactions', ['transactions' => $transactions]);
    }

    public function showSettings()
    {
        $this->verifySystemAdmin();

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM settings");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->view('dashboard/system/settings', ['settings' => $settings]);
    }

    public function saveSettings()
    {
        $this->verifySystemAdmin();
        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        foreach ($_POST as $key => $value) {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = :val WHERE setting_key = :key");
            $stmt->bindParam(':val', $value);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
        }

        Settings::clearCache();

        AuditLogger::log("System Settings Updated");

        $this->redirect('/admin/settings?saved=true');
    }

    public function sendTestEmail()
    {
        $this->verifySystemAdmin();

        $to = trim($_POST['test_email_to'] ?? '');
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/settings?test=invalid_email');
            return;
        }

        $ok = \App\Services\EmailService::send(
            $to,
            'SupplierEval test email',
            'This is a test email from your platform settings. If you can read this, your email provider is configured correctly.',
            'Open Platform Settings',
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/saas/admin/settings'
        );

        if ($ok) {
            AuditLogger::log('Email Test Sent', 'To: ' . $to);
            $this->redirect('/admin/settings?test=sent&to=' . rawurlencode($to));
            return;
        }

        $reason = \App\Services\EmailService::getLastError();
        $this->redirect('/admin/settings?test=failed&reason=' . rawurlencode($reason));
    }
    public function suspendCompany()
    {
        $this->verifySystemAdmin();
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? 'suspended'; // active or suspended

        if ($id) {
            $db = new \App\Config\Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("UPDATE companies SET account_status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            AuditLogger::log("Company Status Changed", "ID: $id, Status: $status");
        }
        $this->redirect('/admin/companies');
    }

    public function deleteCompany()
    {
        $this->verifySystemAdmin();
        $id = $_POST['id'] ?? null;

        if ($id) {
            $db = new \App\Config\Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("DELETE FROM companies WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            AuditLogger::log("Company Deleted", "ID: $id");
        }
        $this->redirect('/admin/companies');
    }

    public function deleteUser()
    {
        $this->verifySystemAdmin();
        $id = $_POST['id'] ?? null;

        if ($id) {
            $db = new \App\Config\Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            AuditLogger::log("User Deleted", "ID: $id");
        }
        $this->redirect('/admin/users');
    }

    public function auditLogs()
    {
        $role = $_SESSION['role'];
        $company_id = $_SESSION['company_id'];

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        if ($role === 'system_admin') {
            // System admin sees everything
            $stmt = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 100");
        } else {
            // Company admin sees only their company's logs
            $stmt = $conn->prepare("SELECT * FROM audit_logs WHERE company_id = :cid ORDER BY created_at DESC LIMIT 100");
            $stmt->bindParam(':cid', $company_id);
            $stmt->execute();
        }

        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('dashboard/system/audit_logs', ['logs' => $logs]);
    }

    private function verifySystemAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'system_admin') {
            $this->redirect('/login');
            exit;
        }
    }
}
