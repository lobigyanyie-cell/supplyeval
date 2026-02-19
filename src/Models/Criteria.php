<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Criteria
{
    private $conn;
    private $table_name = "criteria";

    public $id;
    public $company_id;
    public $name;
    public $weight;
    public $max_score;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                (company_id, name, weight, max_score) 
                VALUES (:company_id, :name, :weight, :max_score)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->weight = htmlspecialchars(strip_tags($this->weight));
        $this->max_score = htmlspecialchars(strip_tags($this->max_score));

        // Bind
        $stmt->bindParam(":company_id", $this->company_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":max_score", $this->max_score);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($company_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE company_id = :company_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":company_id", $company_id);
        $stmt->execute();
        return $stmt;
    }

    public function getTotalWeight($company_id, $exclude_id = null)
    {
        $query = "SELECT SUM(weight) as total FROM " . $this->table_name . " WHERE company_id = :company_id";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":company_id", $company_id);
        if ($exclude_id) {
            $stmt->bindParam(":exclude_id", $exclude_id);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($row['total'] ?? 0);
    }

    public function find($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->company_id = $row['company_id'];
            $this->name = $row['name'];
            $this->weight = $row['weight'];
            $this->max_score = $row['max_score'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                SET name = :name, weight = :weight, max_score = :max_score 
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->weight = htmlspecialchars(strip_tags($this->weight));
        $this->max_score = htmlspecialchars(strip_tags($this->max_score));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":max_score", $this->max_score);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
