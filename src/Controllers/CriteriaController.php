<?php

namespace App\Controllers;

use App\Models\Criteria;
use App\Services\AuditLogger;

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

        $this->view('criteria/create');
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $criteria = new Criteria();
        $weight = (float) ($_POST['weight'] ?? 0);
        $totalWeight = $criteria->getTotalWeight($_SESSION['company_id']);

        if (($totalWeight + $weight) > 100) {
            $error = "Total weight cannot exceed 100%. (Current: {$totalWeight}%, adding: {$weight}%)";
            $this->view('criteria/create', ['error' => $error]);
            return;
        }

        $criteria->company_id = $_SESSION['company_id'];
        $criteria->name = $_POST['name'] ?? '';
        $criteria->weight = $weight;
        $criteria->max_score = $_POST['max_score'] ?? 10;

        if ($criteria->create()) {
            AuditLogger::log("Criteria Created", "Name: " . $criteria->name . ", Weight: " . $criteria->weight . "%");
            $this->redirect('/criteria');
        } else {
            $this->view('criteria/create', ['error' => 'Failed to create criteria.']);
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

        $criteria->name = $_POST['name'] ?? '';
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
