<?php

/**
 * Idempotent: creates password_resets if it is missing.
 * Run: php public/migrate_password_resets.php
 */

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo "Database connection failed.\n";
    exit(1);
}

$conn->exec("
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

echo "password_resets is ready.\n";
