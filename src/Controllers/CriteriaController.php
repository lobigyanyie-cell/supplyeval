<?php

namespace App\Controllers;

use App\Models\Criteria;
use App\Services\AuditLogger;
use App\Services\CompanyPlan;

class CriteriaController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        // Removed checkSubscription() - viewing criteria is read-only access

        $criteriaModel = new Criteria();
        $stmt = $criteriaModel->readAll($_SESSION['company_id']);
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('criteria/index', ['criteria' => $criteria]);
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription(); // Block access for expired subscriptions

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $plan = CompanyPlan::current();
        $maxC = CompanyPlan::maxCriteria($plan);
        $cc = $conn ? CompanyPlan::criteriaCount($conn, (int) $_SESSION['company_id']) : 0;
        $this->view('criteria/create', [
            'criteria_slots_remaining' => $maxC !== null ? max(0, $maxC - $cc) : null,
            'plan_max_criteria' => $maxC,
        ]);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $plan = CompanyPlan::current();
        $maxC = CompanyPlan::maxCriteria($plan);
        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $cc = $conn ? CompanyPlan::criteriaCount($conn, (int) $_SESSION['company_id']) : 0;
        $planCtx = [
            'criteria_slots_remaining' => $maxC !== null ? max(0, $maxC - $cc) : null,
            'plan_max_criteria' => $maxC,
        ];
        if ($conn !== null && $maxC !== null && $cc >= $maxC) {
            $this->view('criteria/create', array_merge($planCtx, [
                'error' => "Your plan allows up to {$maxC} criteria. Upgrade to Professional for unlimited criteria.",
                'criteria_slots_remaining' => 0,
            ]));
            return;
        }

        $criteria = new Criteria();
        $weight = (float) ($_POST['weight'] ?? 0);
        $totalWeight = $criteria->getTotalWeight($_SESSION['company_id']);

        if (($totalWeight + $weight) > 100) {
            $error = "Total weight cannot exceed 100%. (Current: {$totalWeight}%, adding: {$weight}%)";
            $this->view('criteria/create', array_merge($planCtx, ['error' => $error, 'old' => $_POST]));
            return;
        }

        $criteria->company_id = $_SESSION['company_id'];
        $criteria->name = trim($_POST['name'] ?? '');
        if ($criteria->name === '') {
            $this->view('criteria/create', array_merge($planCtx, ['error' => 'Criteria name is required.', 'old' => $_POST]));
            return;
        }
        if ($criteria->nameExistsForCompany($_SESSION['company_id'], $criteria->name)) {
            $this->view('criteria/create', array_merge($planCtx, [
                'error' => 'A criterion with this name already exists. Use a different name.',
                'old' => $_POST,
            ]));
            return;
        }
        $criteria->weight = $weight;
        $criteria->max_score = $_POST['max_score'] ?? 10;

        if ($criteria->create()) {
            AuditLogger::log("Criteria Created", "Name: " . $criteria->name . ", Weight: " . $criteria->weight . "%");
            $this->redirect('/criteria');
        } else {
            $this->view('criteria/create', array_merge($planCtx, ['error' => 'Failed to create criteria.']));
        }
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $id = $_GET['id'] ?? null;
        $criteria = new Criteria();
        if ($criteria->find($id) && $criteria->company_id == $_SESSION['company_id']) {
            $this->view('criteria/edit', ['criteria' => (array) $criteria]);
        } else {
            $this->redirect('/criteria');
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $id = $_POST['id'] ?? null;
        $criteria = new Criteria();
        if (!$criteria->find($id) || $criteria->company_id != $_SESSION['company_id']) {
            $this->redirect('/criteria');
            return;
        }

        $weight = (float) ($_POST['weight'] ?? 0);
        $totalWeightOther = $criteria->getTotalWeight($_SESSION['company_id'], $id);

        if (($totalWeightOther + $weight) > 100) {
            $error = "Total weight cannot exceed 100%. (Other: {$totalWeightOther}%, updating to: {$weight}%)";
            $this->view('criteria/edit', ['criteria' => array_merge((array) $criteria, $_POST), 'error' => $error]);
            return;
        }

        $criteria->name = trim($_POST['name'] ?? '');
        if ($criteria->name === '') {
            $this->view('criteria/edit', [
                'criteria' => array_merge((array) $criteria, $_POST),
                'error' => 'Criteria name is required.',
            ]);
            return;
        }
        if ($criteria->nameExistsForCompany($_SESSION['company_id'], $criteria->name, $id)) {
            $this->view('criteria/edit', [
                'criteria' => array_merge((array) $criteria, $_POST),
                'error' => 'A criterion with this name already exists. Use a different name.',
            ]);
            return;
        }
        $criteria->weight = $weight;
        $criteria->max_score = $_POST['max_score'] ?? 10;

        if ($criteria->update()) {
            AuditLogger::log("Criteria Updated", "ID: $id, Name: " . $criteria->name . ", Weight: " . $criteria->weight . "%");
            $this->redirect('/criteria');
        } else {
            $this->view('criteria/edit', ['criteria' => (array) $criteria, 'error' => 'Failed to update criteria.']);
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $id = $_POST['id'] ?? null;
        $criteria = new Criteria();
        // Check ownership before deleting
        if ($criteria->find($id) && $criteria->company_id == $_SESSION['company_id']) {
            AuditLogger::log("Criteria Deleted", "ID: $id, Name: " . $criteria->name);
            $criteria->delete($id);
        }
        $this->redirect('/criteria');
    }
}
