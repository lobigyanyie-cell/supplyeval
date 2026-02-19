<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Company
{
    private $conn;
    private $table_name = "companies";

    public $id;
    public $name;
    public $email;
    public $subscription_status;
    public $trial_ends_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                (name, email, subscription_status, trial_ends_at) 
                VALUES (:name, :email, :subscription_status, :trial_ends_at)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":subscription_status", $this->subscription_status);
        $stmt->bindParam(":trial_ends_at", $this->trial_ends_at);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function exists($email)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
