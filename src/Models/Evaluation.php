<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Evaluation
{
    private $conn;
    private $table_name = "evaluations";

    public $id;
    public $company_id;
    public $supplier_id;
    public $evaluator_id;
    public $total_score;
    public $comments;
    public $scores = []; // Array of ['criteria_id' => x, 'score' => y]

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insert into evaluations table
            $query = "INSERT INTO " . $this->table_name . " 
                    (company_id, supplier_id, evaluator_id, total_score, comments) 
                    VALUES (:company_id, :supplier_id, :evaluator_id, :total_score, :comments)";

            $stmt = $this->conn->prepare($query);

            // Sanitize
            $this->comments = htmlspecialchars(strip_tags($this->comments));

            // Bind
            $stmt->bindParam(":company_id", $this->company_id);
            $stmt->bindParam(":supplier_id", $this->supplier_id);
            $stmt->bindParam(":evaluator_id", $this->evaluator_id);
            $stmt->bindParam(":total_score", $this->total_score);
            $stmt->bindParam(":comments", $this->comments);

            if (!$stmt->execute()) {
                throw new \Exception("Failed to save evaluation.");
            }

            $this->id = $this->conn->lastInsertId();

            // 2. Insert into evaluation_scores table
            $queryDetails = "INSERT INTO evaluation_scores (evaluation_id, criteria_id, score) VALUES (:evaluation_id, :criteria_id, :score)";
            $stmtDetails = $this->conn->prepare($queryDetails);

            foreach ($this->scores as $item) {
                $stmtDetails->bindParam(":evaluation_id", $this->id);
                $stmtDetails->bindParam(":criteria_id", $item['criteria_id']);
                $stmtDetails->bindParam(":score", $item['score']);
                if (!$stmtDetails->execute()) {
                    throw new \Exception("Failed to save evaluation details.");
                }
            }

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getBySupplier($supplier_id)
    {
        $query = "SELECT e.*, u.name as evaluator_name 
                  FROM " . $this->table_name . " e 
                  LEFT JOIN users u ON e.evaluator_id = u.id
                  WHERE e.supplier_id = :supplier_id 
                  ORDER BY e.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":supplier_id", $supplier_id);
        $stmt->execute();
        return $stmt;
    }

    public function getTrend($supplier_id, $limit = 6)
    {
        $query = "SELECT total_score, DATE(created_at) as date 
                  FROM " . $this->table_name . " 
                  WHERE supplier_id = :supplier_id 
                  ORDER BY created_at ASC 
                  LIMIT " . (int) $limit;
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":supplier_id", $supplier_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCriteriaBreakdown($supplier_id)
    {
        $query = "SELECT c.name as criteria_name, AVG(es.score) as avg_score, c.weight, c.max_score
                  FROM evaluation_scores es
                  JOIN evaluations e ON es.evaluation_id = e.id
                  JOIN criteria c ON es.criteria_id = c.id
                  WHERE e.supplier_id = :supplier_id
                  GROUP BY es.criteria_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":supplier_id", $supplier_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompanyAverage($company_id)
    {
        $query = "SELECT AVG(total_score) as avg_score FROM " . $this->table_name . " WHERE company_id = :company_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":company_id", $company_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_score'] ?? 0;
    }
}
