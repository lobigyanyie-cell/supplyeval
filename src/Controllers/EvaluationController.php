<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Models\Criteria;
use App\Models\Evaluation;
use App\Services\AuditLogger;
use App\Services\EvaluationScoringService;

class EvaluationController extends Controller
{

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription(); // Block write access

        $supplier_id = $_GET['supplier_id'] ?? null;
        if (!$supplier_id) {
            $this->redirect('/suppliers');
            return;
        }

        $supplierModel = new Supplier();
        $supplierModel->id = $supplier_id;
        $supplierModel->company_id = $_SESSION['company_id'];
        $supplier = $supplierModel->readOne();

        if (!$supplier) {
            echo "Supplier not found or access denied.";
            return;
        }

        $criteriaModel = new Criteria();
        $stmt = $criteriaModel->readAll($_SESSION['company_id']);
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('evaluations/create', [
            'supplier' => $supplier,
            'criteria' => $criteria
        ]);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription(); // Block write access

        $supplier_id = $_POST['supplier_id'];
        $scores_input = $_POST['scores'] ?? []; // Array of criteria_id => stored_score
        $comments = $_POST['comments'] ?? '';

        $criteriaModel = new Criteria();
        $stmt = $criteriaModel->readAll($_SESSION['company_id']);
        $allCriteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        try {
            $scoring = (new EvaluationScoringService())->score($allCriteria, $scores_input);
        } catch (\RuntimeException $e) {
            http_response_code(503);
            echo htmlspecialchars($e->getMessage());
            return;
        }

        $total_score = $scoring['total_score'];
        $evaluation_scores = $scoring['evaluation_scores'];
        $low_score_alert = $scoring['low_score_alert'];

        $evaluation = new Evaluation();
        $evaluation->company_id = $_SESSION['company_id'];
        $evaluation->supplier_id = $supplier_id;
        $evaluation->evaluator_id = $_SESSION['user_id'];
        $evaluation->total_score = $total_score;
        $evaluation->comments = $comments;
        $evaluation->scores = $evaluation_scores;

        if ($evaluation->create()) {
            if ($low_score_alert) {
                // Fetch supplier name
                $supplierModel = new \App\Models\Supplier();
                $supplierModel->id = $supplier_id;
                $supplierModel->company_id = $_SESSION['company_id'];
                $supplierData = $supplierModel->readOne();
                $supplierName = $supplierData['name'] ?? 'Unknown Supplier';

                // Fetch Company Admin Email
                $userModel = new \App\Models\User();
                $adminEmail = $userModel->getCompanyAdminEmail($_SESSION['company_id']);

                if ($adminEmail) {
                    \App\Services\EmailService::send(
                        $adminEmail,
                        "Low Score Alert: " . $supplierName,
                        "The supplier <strong>{$supplierName}</strong> has received a low performance score of <strong>" . number_format($total_score, 1) . "</strong>. <br><br>This is below the 50% threshold. Immediate re-evaluation or corrective action is recommended.",
                        "View Portfolio",
                        "http://" . $_SERVER['HTTP_HOST'] . "/saas/suppliers/profile?id=" . $supplier_id
                    );
                }
                AuditLogger::log("Evaluation Submitted", "Supplier ID: $supplier_id, Score: " . number_format($total_score, 1));
                $this->redirect('/suppliers/rankings?success=true&alert_triggered=true');
                return;
            }

            AuditLogger::log("Evaluation Submitted", "Supplier ID: $supplier_id, Score: " . number_format($total_score, 1));
            $this->redirect('/suppliers/rankings?success=true');
        } else {
            echo "Failed";
        }
    }
}
