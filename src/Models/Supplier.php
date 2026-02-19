<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Supplier
{
    private $conn;
    private $table_name = "suppliers";

    public $id;
    public $company_id;
    public $name;
    public $contact_person;
    public $email;
    public $phone;
    public $address;
    public $reevaluation_days;
    public $last_notified_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                (company_id, name, contact_person, email, phone, address, reevaluation_days) 
                VALUES (:company_id, :name, :contact_person, :email, :phone, :address, :reevaluation_days)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind
        $stmt->bindParam(":company_id", $this->company_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":contact_person", $this->contact_person);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":reevaluation_days", $this->reevaluation_days);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($company_id)
    {
        $query = "SELECT s.*, 
                         AVG(e.total_score) as avg_score,
                         MAX(e.created_at) as last_eval_date,
                         DATEDIFF(NOW(), COALESCE(MAX(e.created_at), s.created_at)) as days_since_eval
                  FROM " . $this->table_name . " s 
                  LEFT JOIN evaluations e ON s.id = e.supplier_id
                  WHERE s.company_id = :company_id 
                  GROUP BY s.id
                  ORDER BY avg_score DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":company_id", $company_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND company_id = :company_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":company_id", $this->company_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                SET name = :name, contact_person = :contact_person, email = :email, phone = :phone, address = :address, reevaluation_days = :reevaluation_days 
                WHERE id = :id AND company_id = :company_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":contact_person", $this->contact_person);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":reevaluation_days", $this->reevaluation_days);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":company_id", $this->company_id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND company_id = :company_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":company_id", $this->company_id);
        return $stmt->execute();
    }
    public function getDueForEvaluation($company_id)
    {
        $query = "SELECT s.*, 
                         MAX(e.created_at) as last_eval,
                         DATEDIFF(NOW(), COALESCE(MAX(e.created_at), s.created_at)) as days_since_eval,
                         (SELECT total_score FROM evaluations WHERE supplier_id = s.id ORDER BY created_at DESC LIMIT 1) as latest_score
                  FROM " . $this->table_name . " s
                  LEFT JOIN evaluations e ON s.id = e.supplier_id
                  WHERE s.company_id = :company_id
                  GROUP BY s.id
                  HAVING (s.reevaluation_days > 0 AND days_since_eval >= s.reevaluation_days) OR latest_score < 50";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":company_id", $company_id);
        $stmt->execute();
        return $stmt;
    }
}
