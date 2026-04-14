<?php

/**
 * Idempotent: adds companies.plan and grandfathers existing rows to professional.
 * Run: php public/migrate_company_plan.php
 */

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo "Database connection failed.\n";
    exit(1);
}

$check = $conn->query("SHOW COLUMNS FROM companies LIKE 'plan'");
if ($check && $check->rowCount() === 0) {
    echo "Adding companies.plan ...\n";
    $conn->exec(
        "ALTER TABLE companies ADD COLUMN plan ENUM('starter','professional','enterprise') NOT NULL DEFAULT 'professional' AFTER subscription_status"
    );
} else {
    echo "companies.plan already present.\n";
}

$conn->exec("UPDATE companies SET plan = 'professional' WHERE plan = '' OR plan IS NULL");
echo "Done.\n";
