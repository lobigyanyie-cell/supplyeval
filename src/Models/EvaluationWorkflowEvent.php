<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class EvaluationWorkflowEvent
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function record(int $evaluationId, int $companyId, ?int $userId, string $action, ?array $meta = null): void
    {
        if ($this->conn === null) {
            return;
        }
        $metaStr = $meta !== null ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;
        $stmt = $this->conn->prepare(
            'INSERT INTO evaluation_workflow_events (evaluation_id, company_id, user_id, action, meta)
             VALUES (:eid, :cid, :uid, :action, :meta)'
        );
        $stmt->execute([
            'eid' => $evaluationId,
            'cid' => $companyId,
            'uid' => $userId,
            'action' => $action,
            'meta' => $metaStr,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listByEvaluation(int $evaluationId, int $companyId): array
    {
        if ($this->conn === null) {
            return [];
        }
        $stmt = $this->conn->prepare(
            'SELECT w.*, u.name AS actor_name
             FROM evaluation_workflow_events w
             LEFT JOIN users u ON w.user_id = u.id
             WHERE w.evaluation_id = :eid AND w.company_id = :cid
             ORDER BY w.created_at ASC'
        );
        $stmt->execute(['eid' => $evaluationId, 'cid' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Timeline for a supplier (all workflow events linked to their evaluations).
     *
     * @return list<array<string, mixed>>
     */
    public function listBySupplier(int $supplierId, int $companyId, int $limit = 150): array
    {
        if ($this->conn === null) {
            return [];
        }
        $limit = max(1, min(500, $limit));
        $stmt = $this->conn->prepare(
            "SELECT w.*, u.name AS actor_name, e.status AS eval_status, e.total_score, e.created_at AS eval_created_at
             FROM evaluation_workflow_events w
             JOIN evaluations e ON w.evaluation_id = e.id
             LEFT JOIN users u ON w.user_id = u.id
             WHERE e.supplier_id = :sid AND e.company_id = :cid
             ORDER BY w.created_at DESC
             LIMIT {$limit}"
        );
        $stmt->execute(['sid' => $supplierId, 'cid' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
