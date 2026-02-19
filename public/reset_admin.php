<?php
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = new Database();
$conn = $db->getConnection();

$email = 'admin@saas.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Update
        $update = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
        $update->bindParam(":password", $hash);
        $update->bindParam(":email", $email);
        $update->execute();
        echo "Password updated successfully for $email. New password: $password";
    } else {
        // Create if missing
        $insert = $conn->prepare("INSERT INTO users (company_id, name, email, password, role) VALUES (NULL, 'System Administrator', :email, :password, 'system_admin')");
        $insert->bindParam(":email", $email);
        $insert->bindParam(":password", $hash);
        $insert->execute();
        echo "User created successfully. Email: $email, Password: $password";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
