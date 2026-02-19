<?php
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

echo "Starting payment migration...\n";

// 1. Add subscription_ends_at column to companies
try {
    $stmt = $conn->query("SHOW COLUMNS FROM companies LIKE 'subscription_ends_at'");
    if ($stmt->fetch()) {
        echo "Column 'subscription_ends_at' already exists in 'companies'.\n";
    } else {
        $conn->exec("ALTER TABLE companies ADD COLUMN subscription_ends_at DATETIME NULL AFTER trial_ends_at");
        echo "Added 'subscription_ends_at' column to 'companies'.\n";
    }
} catch (PDOException $e) {
    echo "Error modifying companies table: " . $e->getMessage() . "\n";
}

// 2. Create transactions table
try {
    $sql = "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        currency VARCHAR(3) DEFAULT 'USD',
        status VARCHAR(20) DEFAULT 'succeeded',
        payment_method VARCHAR(50) DEFAULT 'credit_card',
        transaction_id VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "Created 'transactions' table.\n";
} catch (PDOException $e) {
    echo "Error creating transactions table: " . $e->getMessage() . "\n";
}

echo "Migration completed.\n";
?>