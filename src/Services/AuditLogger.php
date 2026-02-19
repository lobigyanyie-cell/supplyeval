<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class AuditLogger
{
    /**
     * Log a user activity to the database.
     * 
     * @param string $action The action being performed (e.g., 'Supplier Created')
     * @param string|array|null $details Additional context about the action
     */
    public static function log($action, $details = null)
    {
        // Don't log if no user session exists (unless it's a login attempt)
        if (!isset($_SESSION['user_id']) && strpos($action, 'Login') === false) {
            return;
        }

        $db = new Database();
        $conn = $db->getConnection();

        $company_id = $_SESSION['company_id'] ?? 0;
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? 'System';

        // Handle IP address
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Prepare details string
        if (is_array($details)) {
            $details = json_encode($details);
        }

        $query = "INSERT INTO audit_logs (company_id, user_id, user_name, action, details, ip_address) 
                  VALUES (:company_id, :user_id, :user_name, :action, :details, :ip_address)";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            'company_id' => $company_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip
        ]);
    }
}
