<?php

/**
 * Idempotent: adds users.invite_pending if missing.
 * Run: php public/migrate_invite_pending.php
 */

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo "Database connection failed.\n";
    exit(1);
}

$check = $conn->query("SHOW COLUMNS FROM users LIKE 'invite_pending'");
if ($check && $check->rowCount() === 0) {
    echo "Adding users.invite_pending ...\n";
    $conn->exec(
        "ALTER TABLE users ADD COLUMN invite_pending TINYINT(1) NOT NULL DEFAULT 0 AFTER role"
    );
} else {
    echo "users.invite_pending already present.\n";
}

echo "Done.\n";
