<?php

namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View not found: " . $viewFile;
        }
    }

    protected function redirect($url)
    {
        $basePath = '/saas'; // Standardized base path
        header("Location: " . $basePath . $url);
        exit;
    }

    protected function isSubscriptionActive()
    {
        if (!isset($_SESSION['company_id'])) {
            return false;
        }

        // Cache result in property or session to avoid DB call on every method if possible,
        // but for now DB is safer for real-time status updates (like if admin activates them).
        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT subscription_status, trial_ends_at, account_status FROM companies WHERE id = :id");
        $stmt->bindParam(":id", $_SESSION['company_id']);
        $stmt->execute();
        $company = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$company) {
            return false;
        }

        // 1. Check Account Status (Access Level)
        // This should be handled by AuthController mostly, but good to double check.
        if (($company['account_status'] ?? 'active') === 'suspended') {
            return false;
        }

        // 2. Check Subscription Status (Billing Level)
        $is_trial = $company['subscription_status'] === 'trial';
        $trial_ended = strtotime($company['trial_ends_at']) < time();
        $is_inactive = $company['subscription_status'] === 'inactive';

        if (($is_trial && $trial_ended) || $is_inactive) {
            return false; // Expired
        }

        return true; // Active
    }

    protected function enforceSubscription()
    {
        if (!$this->isSubscriptionActive()) {
            // Check if it's an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('HTTP/1.1 403 Forbidden');
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Subscription expired. Read-only access mode.']);
                exit;
            }

            // Standard Request
            $this->view('errors/subscription_expired');
            exit;
        }
    }
}
