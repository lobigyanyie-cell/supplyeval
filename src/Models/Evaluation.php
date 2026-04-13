<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Evaluation
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';

    private $conn;
    private $table_name = 'evaluations';

    public $id;
    public $company_id;
    public $supplier_id;
    public $evaluator_id;
    public $total_score;
    public $comments;
    /** @var self::STATUS_*|string */
    public $status = self::STATUS_SUBMITTED;
    /** @var list<array{criteria_id: int, score: float|int}> */
    public $scores = [];

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create(): bool
    {
        if ($this->conn === null) {
            return false;
        }
        try {
            $this->conn->beginTransaction();

            $query = 'INSERT INTO ' . $this->table_name . '
                    (company_id, supplier_id, evaluator_id, total_score, comments, status)
                    VALUES (:company_id, :supplier_id, :evaluator_id, :total_score, :comments, :status)';

            $stmt = $this->conn->prepare($query);

            $this->comments = htmlspecialchars(strip_tags((string) $this->comments));
            $status = $this->status === self::STATUS_DRAFT ? self::STATUS_DRAFT : self::STATUS_SUBMITTED;

            $stmt->bindParam(':company_id', $this->company_id);
            $stmt->bindParam(':supplier_id', $this->supplier_id);
            $stmt->bindParam(':evaluator_id', $this->evaluator_id);
            $stmt->bindParam(':total_score', $this->total_score);
            $stmt->bindParam(':comments', $this->comments);
            $stmt->bindParam(':status', $status);

            if (!$stmt->execute()) {
                throw new \Exception('Failed to save evaluation.');
            }

            $this->id = (int) $this->conn->lastInsertId();

            $this->replaceScoreRows($this->id);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Update an existing draft (same evaluator, same company). Optionally set status to submitted.
     */
    public function updateDraft(int $evaluationId, int $companyId, int $evaluatorId, string $newStatus): bool
    {
        if ($this->conn === null) {
            return false;
        }
        $newStatus = $newStatus === self::STATUS_SUBMITTED ? self::STATUS_SUBMITTED : self::STATUS_DRAFT;

        try {
            $this->conn->beginTransaction();

            $check = $this->conn->prepare(
                'SELECT id FROM ' . $this->table_name . '
                 WHERE id = :id AND company_id = :cid AND evaluator_id = :eid AND status = :draft
                 LIMIT 1 FOR UPDATE'
            );
            $draft = self::STATUS_DRAFT;
            $check->execute([
                'id' => $evaluationId,
                'cid' => $companyId,
                'eid' => $evaluatorId,
                'draft' => $draft,
            ]);
            if (!$check->fetch(PDO::FETCH_ASSOC)) {
                $this->conn->rollBack();
                return false;
            }

            $this->comments = htmlspecialchars(strip_tags((string) $this->comments));

            if ($newStatus === self::STATUS_SUBMITTED) {
                $upd = $this->conn->prepare(
                    'UPDATE ' . $this->table_name . '
                     SET total_score = :ts, comments = :c, status = :st, evaluation_date = NOW()
                     WHERE id = :id AND company_id = :cid'
                );
            } else {
                $upd = $this->conn->prepare(
                    'UPDATE ' . $this->table_name . '
                     SET total_score = :ts, comments = :c, status = :st
                     WHERE id = :id AND company_id = :cid'
                );
            }
            $upd->execute([
                'ts' => $this->total_score,
                'c' => $this->comments,
                'st' => $newStatus,
                'id' => $evaluationId,
                'cid' => $companyId,
            ]);

            $del = $this->conn->prepare('DELETE FROM evaluation_scores WHERE evaluation_id = :eid');
            $del->execute(['eid' => $evaluationId]);

            $this->replaceScoreRows($evaluationId);

            $this->id = $evaluationId;
            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    private function replaceScoreRows(int $evaluationId): void
    {
        $queryDetails = 'INSERT INTO evaluation_scores (evaluation_id, criteria_id, score) VALUES (:evaluation_id, :criteria_id, :score)';
        $stmtDetails = $this->conn->prepare($queryDetails);

        foreach ($this->scores as $item) {
            $stmtDetails->bindValue(':evaluation_id', $evaluationId, PDO::PARAM_INT);
            $stmtDetails->bindValue(':criteria_id', (int) $item['criteria_id'], PDO::PARAM_INT);
            $stmtDetails->bindValue(':score', $item['score']);
            if (!$stmtDetails->execute()) {
                throw new \Exception('Failed to save evaluation details.');
            }
        }
    }

    /**
     * @return ?array<string, mixed>
     */
    public function findDraftForEvaluator(int $companyId, int $supplierId, int $evaluatorId): ?array
    {
        if ($this->conn === null) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT * FROM ' . $this->table_name . '
             WHERE company_id = :cid AND supplier_id = :sid AND evaluator_id = :eid AND status = :draft
             ORDER BY id DESC LIMIT 1'
        );
        $draft = self::STATUS_DRAFT;
        $stmt->execute([
            'cid' => $companyId,
            'sid' => $supplierId,
            'eid' => $evaluatorId,
            'draft' => $draft,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array<int, float>
     */
    public function getScoresMapForEvaluation(int $evaluationId): array
    {
        if ($this->conn === null) {
            return [];
        }
        $stmt = $this->conn->prepare(
            'SELECT criteria_id, score FROM evaluation_scores WHERE evaluation_id = :eid'
        );
        $stmt->execute(['eid' => $evaluationId]);
        $map = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $map[(int) $row['criteria_id']] = (float) $row['score'];
        }
        return $map;
    }

    /**
     * @return ?array<string, mixed>
     */
    public function readOneForCompany(int $evaluationId, int $companyId): ?array
    {
        if ($this->conn === null) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT e.*, s.name AS supplier_name
             FROM ' . $this->table_name . ' e
             JOIN suppliers s ON e.supplier_id = s.id
             WHERE e.id = :id AND e.company_id = :cid LIMIT 1'
        );
        $stmt->execute(['id' => $evaluationId, 'cid' => $companyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getBySupplier($supplier_id)
    {
        $query = 'SELECT e.*, u.name as evaluator_name 
                  FROM ' . $this->table_name . ' e 
                  LEFT JOIN users u ON e.evaluator_id = u.id
                  WHERE e.supplier_id = :supplier_id AND e.status = :submitted
                  ORDER BY e.created_at DESC';
        $stmt = $this->conn->prepare($query);
        $sub = self::STATUS_SUBMITTED;
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':submitted', $sub);
        $stmt->execute();
        return $stmt;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getTrend($supplier_id, $limit = 6): array
    {
        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 6;
        }
        $query = 'SELECT total_score, DATE(created_at) as date 
                  FROM ' . $this->table_name . ' 
                  WHERE supplier_id = :supplier_id AND status = :submitted
                  ORDER BY created_at DESC 
                  LIMIT ' . $limit;
        $stmt = $this->conn->prepare($query);
        $sub = self::STATUS_SUBMITTED;
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':submitted', $sub);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_reverse($rows);
    }

    public function getCriteriaBreakdown($supplier_id): array
    {
        $query = 'SELECT c.id as criteria_id, c.name as criteria_name, AVG(es.score) as avg_score, c.weight, c.max_score
                  FROM evaluation_scores es
                  JOIN evaluations e ON es.evaluation_id = e.id
                  JOIN criteria c ON es.criteria_id = c.id
                  WHERE e.supplier_id = :supplier_id AND e.status = :submitted
                  GROUP BY c.id, c.name, c.weight, c.max_score';
        $stmt = $this->conn->prepare($query);
        $sub = self::STATUS_SUBMITTED;
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':submitted', $sub);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompanyAverage($company_id)
    {
        $query = 'SELECT AVG(total_score) as avg_score FROM ' . $this->table_name . '
                  WHERE company_id = :company_id AND status = :submitted';
        $stmt = $this->conn->prepare($query);
        $sub = self::STATUS_SUBMITTED;
        $stmt->bindParam(':company_id', $company_id);
        $stmt->bindParam(':submitted', $sub);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_score'] ?? 0;
    }

    /**
     * Raw scores from the latest submitted evaluation for a supplier.
     *
     * @return list<array{criteria_name: string, score: float, weight: float, max_score: int}>
     */
    public function getLatestSubmittedScoreLines(int $supplierId, int $companyId): array
    {
        if ($this->conn === null) {
            return [];
        }
        $sub = self::STATUS_SUBMITTED;
        $stmt = $this->conn->prepare(
            "SELECT c.name AS criteria_name, es.score, c.weight, c.max_score
             FROM evaluation_scores es
             JOIN criteria c ON es.criteria_id = c.id
             WHERE es.evaluation_id = (
                 SELECT id FROM {$this->table_name}
                 WHERE supplier_id = :sid AND company_id = :cid AND status = :submitted
                 ORDER BY created_at DESC, id DESC
                 LIMIT 1
             )
             ORDER BY c.name"
        );
        $stmt->execute(['sid' => $supplierId, 'cid' => $companyId, 'submitted' => $sub]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
