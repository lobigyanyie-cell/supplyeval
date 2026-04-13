<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Models\Criteria;
use App\Models\Evaluation;
use App\Models\EvaluationWorkflowEvent;
use App\Services\AuditLogger;
use App\Services\EvaluationScoringService;

class EvaluationController extends Controller
{
    private function requireCanEvaluate(): void
    {
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['company_admin', 'evaluator'], true)) {
            $this->redirect('/dashboard');
        }
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->requireCanEvaluate();
        $this->enforceSubscription();

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
            echo 'Supplier not found or access denied.';
            return;
        }

        $criteriaModel = new Criteria();
        $stmt = $criteriaModel->readAll($_SESSION['company_id']);
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $evaluationModel = new Evaluation();
        $draft = $evaluationModel->findDraftForEvaluator(
            (int) $_SESSION['company_id'],
            (int) $supplier_id,
            (int) $_SESSION['user_id']
        );
        $draft_scores = [];
        $draft_evaluation_id = null;
        $draft_comments = '';
        if ($draft) {
            $draft_evaluation_id = (int) $draft['id'];
            $draft_comments = (string) $draft['comments'];
            $draft_scores = $evaluationModel->getScoresMapForEvaluation($draft_evaluation_id);
        }

        $this->view('evaluations/create', [
            'supplier' => $supplier,
            'criteria' => $criteria,
            'draft_evaluation_id' => $draft_evaluation_id,
            'draft_scores' => $draft_scores,
            'draft_comments' => $draft_comments,
        ]);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->requireCanEvaluate();
        $this->enforceSubscription();

        $supplier_id = (int) ($_POST['supplier_id'] ?? 0);
        $evaluation_id = (int) ($_POST['evaluation_id'] ?? 0);
        $workflow_action = $_POST['workflow_action'] ?? 'submit';
        if (!in_array($workflow_action, ['draft', 'submit'], true)) {
            $workflow_action = 'submit';
        }

        $scores_input = $_POST['scores'] ?? [];

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
        $evaluation->comments = $_POST['comments'] ?? '';
        $evaluation->scores = $evaluation_scores;

        $workflow = new EvaluationWorkflowEvent();
        $companyId = (int) $_SESSION['company_id'];
        $userId = (int) $_SESSION['user_id'];

        if ($workflow_action === 'draft') {
            $evaluation->status = Evaluation::STATUS_DRAFT;
            if ($evaluation_id > 0) {
                $ok = $evaluation->updateDraft($evaluation_id, $companyId, $userId, Evaluation::STATUS_DRAFT);
                if ($ok) {
                    $workflow->record($evaluation_id, $companyId, $userId, 'draft_saved', [
                        'total_score' => $total_score,
                    ]);
                    AuditLogger::log('Evaluation Draft Saved', [
                        'evaluation_id' => $evaluation_id,
                        'supplier_id' => $supplier_id,
                    ]);
                    $this->redirect('/evaluations/create?supplier_id=' . $supplier_id . '&draft_saved=1');
                    return;
                }
                echo 'Could not update draft.';
                return;
            }

            if ($evaluation->create()) {
                $newId = (int) $evaluation->id;
                $workflow->record($newId, $companyId, $userId, 'draft_created', ['total_score' => $total_score]);
                AuditLogger::log('Evaluation Draft Created', [
                    'evaluation_id' => $newId,
                    'supplier_id' => $supplier_id,
                ]);
                $this->redirect('/evaluations/create?supplier_id=' . $supplier_id . '&draft_saved=1');
                return;
            }
            echo 'Failed';
            return;
        }

        // Submit
        $evaluation->status = Evaluation::STATUS_SUBMITTED;
        if ($evaluation_id > 0) {
            $ok = $evaluation->updateDraft($evaluation_id, $companyId, $userId, Evaluation::STATUS_SUBMITTED);
            if (!$ok) {
                echo 'Could not submit evaluation (invalid draft or not yours).';
                return;
            }
            $finalId = $evaluation_id;
        } else {
            if (!$evaluation->create()) {
                echo 'Failed';
                return;
            }
            $finalId = (int) $evaluation->id;
        }

        $workflow->record($finalId, $companyId, $userId, 'submitted', [
            'total_score' => $total_score,
            'low_score_alert' => $low_score_alert,
        ]);

        if ($low_score_alert) {
            $supplierModel = new \App\Models\Supplier();
            $supplierModel->id = $supplier_id;
            $supplierModel->company_id = $_SESSION['company_id'];
            $supplierData = $supplierModel->readOne();
            $supplierName = $supplierData['name'] ?? 'Unknown Supplier';

            $userModel = new \App\Models\User();
            $adminEmail = $userModel->getCompanyAdminEmail($_SESSION['company_id']);

            if ($adminEmail) {
                $host = $_SERVER['HTTP_HOST'] ?? '';
                $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $scorecardUrl = $proto . '://' . $host . '/saas/suppliers/scorecard?id=' . $supplier_id;

                \App\Services\EmailService::send(
                    $adminEmail,
                    'Low Score Alert: ' . $supplierName,
                    'The supplier <strong>' . $supplierName . '</strong> has received a low performance score of <strong>' . number_format($total_score, 1) . '</strong>. <br><br>This is below the 50% threshold. Immediate re-evaluation or corrective action is recommended.',
                    'View scorecard',
                    $scorecardUrl
                );
            }
            AuditLogger::log('Evaluation Submitted', 'Supplier ID: ' . $supplier_id . ', Score: ' . number_format($total_score, 1));
            $this->redirect('/suppliers/rankings?success=true&alert_triggered=true');
            return;
        }

        AuditLogger::log('Evaluation Submitted', 'Supplier ID: ' . $supplier_id . ', Score: ' . number_format($total_score, 1));
        $this->redirect('/suppliers/rankings?success=true');
    }

    public function audit()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $eid = (int) ($_GET['id'] ?? 0);
        if ($eid < 1) {
            $this->redirect('/suppliers');
            return;
        }

        $evaluationModel = new Evaluation();
        $evalRow = $evaluationModel->readOneForCompany($eid, (int) $_SESSION['company_id']);
        if (!$evalRow) {
            $this->redirect('/suppliers');
            return;
        }

        $workflow = new EvaluationWorkflowEvent();
        $events = $workflow->listByEvaluation($eid, (int) $_SESSION['company_id']);

        $this->view('evaluations/audit', [
            'evaluation' => $evalRow,
            'events' => $events,
        ]);
    }
}
