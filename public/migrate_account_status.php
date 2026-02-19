<?php
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

try {
    // Check if column exists
    $check = $conn->query("SHOW COLUMNS FROM companies LIKE 'account_status'");
    if ($check->rowCount() == 0) {
        $sql = "ALTER TABLE companies ADD COLUMN account_status ENUM('active', 'suspended') DEFAULT 'active' AFTER subscription_status";
        $conn->exec($sql);
        echo "Successfully added 'account_status' column to companies table.\n";
    } else {
        echo "'account_status' column already exists.\n";
    }

    // Update existing records to have 'active' account status if not set (though default handles new ones)
    $conn->exec("UPDATE companies SET account_status = 'active' WHERE account_status IS NULL");
    echo "Updated existing companies to 'active' status.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>