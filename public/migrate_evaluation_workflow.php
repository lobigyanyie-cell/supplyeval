<?php

/**
 * Idempotent migration: evaluations.status + evaluation_workflow_events.
 * Run: php public/migrate_evaluation_workflow.php
 */

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo "Database connection failed.\n";
    exit(1);
}

$check = $conn->query("SHOW COLUMNS FROM evaluations LIKE 'status'");
if ($check && $check->rowCount() === 0) {
    echo "Adding evaluations.status ...\n";
    $conn->exec(
        "ALTER TABLE evaluations ADD COLUMN status ENUM('draft','submitted') NOT NULL DEFAULT 'submitted' AFTER comments"
    );
    $conn->exec("UPDATE evaluations SET status = 'submitted' WHERE id > 0 AND (status IS NULL OR status = '')");
} else {
    echo "evaluations.status already present.\n";
}

echo "Creating evaluation_workflow_events if missing ...\n";
$conn->exec("
CREATE TABLE IF NOT EXISTS evaluation_workflow_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    company_id INT NOT NULL,
    user_id INT NULL,
    action VARCHAR(64) NOT NULL,
    meta TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ewe_eval (evaluation_id),
    INDEX idx_ewe_company (company_id)
)
");

// Best-effort FKs (skip if MySQL version / permissions differ)
try {
    $conn->exec("
        ALTER TABLE evaluation_workflow_events
        ADD CONSTRAINT fk_ewe_eval FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
    ");
} catch (Throwable $e) {
    echo "(FK fk_ewe_eval skipped or exists)\n";
}
try {
    $conn->exec("
        ALTER TABLE evaluation_workflow_events
        ADD CONSTRAINT fk_ewe_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
    ");
} catch (Throwable $e) {
    echo "(FK fk_ewe_company skipped or exists)\n";
}
try {
    $conn->exec("
        ALTER TABLE evaluation_workflow_events
        ADD CONSTRAINT fk_ewe_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ");
} catch (Throwable $e) {
    echo "(FK fk_ewe_user skipped or exists)\n";
}

echo "Done.\n";
