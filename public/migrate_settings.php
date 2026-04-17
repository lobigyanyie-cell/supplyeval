<?php

/**
 * Idempotent migration: creates `settings` table and seeds default keys (including evaluation service).
 * Run once: php public/migrate_settings.php
 */

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo "Database connection failed.\n";
    exit(1);
}

echo "Ensuring settings table exists...\n";

$conn->exec("
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(191) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
");

$defaults = [
    'site_name' => 'SupplierEval',
    'support_email' => '',
    'currency' => 'GHS',
    'ghs_per_usd' => '11.05',
    'premium_price' => '350',
    'trial_days' => '14',
    'paystack_public_key' => '',
    'paystack_secret_key' => '',
    'brevo_api_key' => '',
    'sendgrid_api_key' => '',
    'smtp_from' => 'noreply@suppliereval.com',
    'evaluation_service_url' => '',
    'evaluation_service_fallback' => '0',
];

$stmt = $conn->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (:k, :v)");
foreach ($defaults as $k => $v) {
    $stmt->execute(['k' => $k, 'v' => $v]);
}

echo "Settings migration completed.\n";
