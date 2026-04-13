<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Models\Evaluation;
use App\Services\AuditLogger;

class SupplierController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        // $this->checkSubscription(); // Removed to allow read-only access

        $supplier = new Supplier();
        $stmt = $supplier->readAll($_SESSION['company_id']);
        $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('suppliers/index', ['suppliers' => $suppliers]);
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription(); // Block write access
        $this->view('suppliers/create');
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription(); // Block write access

        $supplier = new Supplier();
        $supplier->company_id = $_SESSION['company_id'];
        $supplier->name = $_POST['name'] ?? '';
        $supplier->contact_person = $_POST['contact_person'] ?? '';
        $supplier->email = $_POST['email'] ?? '';
        $supplier->phone = $_POST['phone'] ?? '';
        $supplier->address = $_POST['address'] ?? '';
        $supplier->reevaluation_days = isset($_POST['reevaluation_days']) ? max(1, (int) $_POST['reevaluation_days']) : 90;

        if ($supplier->create()) {
            AuditLogger::log("Supplier Created", "Name: " . $supplier->name);
            $this->redirect('/suppliers');
        } else {
            $this->view('suppliers/create', ['error' => 'Unable to create supplier.']);
        }
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/suppliers');
            return;
        }

        $supplierModel = new Supplier();
        $supplierModel->id = $id;
        $supplierModel->company_id = $_SESSION['company_id'];
        $supplierDetails = $supplierModel->readOne();

        if (!$supplierDetails) {
            $this->redirect('/suppliers');
            return;
        }

        $evaluationModel = new Evaluation();

        // 1. Fetch Trend Data (Last 10 evaluations)
        $trend = $evaluationModel->getTrend($id, 10);

        // 2. Fetch Criteria Breakdown
        $breakdown = $evaluationModel->getCriteriaBreakdown($id);

        // 3. Fetch Company Average for Benchmarking
        $companyAvg = $evaluationModel->getCompanyAverage($_SESSION['company_id']);

        // 4. Fetch Recent Evaluations
        $stmt = $evaluationModel->getBySupplier($id);
        $recentEvaluations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('suppliers/profile', [
            'supplier' => $supplierDetails,
            'trend' => $trend,
            'breakdown' => $breakdown,
            'companyAvg' => $companyAvg,
            'recentEvaluations' => $recentEvaluations
        ]);
    }

    public function scorecard()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/suppliers');
            return;
        }

        $supplierModel = new Supplier();
        $supplierModel->id = $id;
        $supplierModel->company_id = $_SESSION['company_id'];
        $supplierDetails = $supplierModel->readOne();

        if (!$supplierDetails) {
            $this->redirect('/suppliers');
            return;
        }

        $evaluationModel = new Evaluation();
        $trend = $evaluationModel->getTrend($id, 12);
        $breakdown = $evaluationModel->getCriteriaBreakdown($id);
        $companyAvg = $evaluationModel->getCompanyAverage($_SESSION['company_id']);
        $stmt = $evaluationModel->getBySupplier($id);
        $evaluationHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $latestSnapshot = $evaluationModel->getLatestSubmittedScoreLines((int) $id, (int) $_SESSION['company_id']);

        $workflow = new \App\Models\EvaluationWorkflowEvent();
        $workflowTimeline = $workflow->listBySupplier((int) $id, (int) $_SESSION['company_id'], 200);

        $this->view('suppliers/scorecard', [
            'supplier' => $supplierDetails,
            'trend' => $trend,
            'breakdown' => $breakdown,
            'companyAvg' => $companyAvg,
            'evaluationHistory' => $evaluationHistory,
            'latestSnapshot' => $latestSnapshot,
            'workflowTimeline' => $workflowTimeline,
        ]);
    }

    public function rankings()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        // Get ranked suppliers with their average scores
        $stmt = $conn->prepare("
            SELECT 
                s.id,
                s.name,
                s.contact_person,
                COUNT(DISTINCT e.id) as evaluation_count,
                ROUND(AVG(e.total_score), 2) as avg_score,
                MAX(e.created_at) as latest_evaluation,
                (SELECT total_score FROM evaluations WHERE supplier_id = s.id AND status = 'submitted' ORDER BY created_at DESC LIMIT 1) as latest_score
            FROM suppliers s
            LEFT JOIN evaluations e ON s.id = e.supplier_id AND e.status = 'submitted'
            WHERE s.company_id = :company_id
            GROUP BY s.id
            ORDER BY avg_score DESC, s.name ASC
        ");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $rankedSuppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get criteria for breakdown
        $stmt = $conn->prepare("SELECT id, name, weight, max_score FROM criteria WHERE company_id = :company_id ORDER BY name");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // For each supplier, get their performance breakdown by criteria
        $supplierBreakdowns = [];
        foreach ($rankedSuppliers as &$supplier) {
            $supplier['risk_flags'] = []; // Initialize

            if ($supplier['evaluation_count'] > 0) {
                $stmt = $conn->prepare("
                    SELECT 
                        c.id as criteria_id,
                        c.name as criterion_name,
                        c.weight,
                        c.max_score,
                        ROUND(AVG(es.score), 2) as avg_score
                    FROM evaluation_scores es
                    JOIN criteria c ON es.criteria_id = c.id
                    JOIN evaluations e ON es.evaluation_id = e.id
                    WHERE e.supplier_id = :supplier_id AND e.company_id = :company_id AND e.status = 'submitted'
                    GROUP BY c.id
                    ORDER BY c.name
                ");
                $stmt->bindParam(':supplier_id', $supplier['id']);
                $stmt->bindParam(':company_id', $_SESSION['company_id']);
                $stmt->execute();
                $breakdown = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $supplierBreakdowns[$supplier['id']] = $breakdown;

                // Risk Flag Logic
                foreach ($breakdown as $row) {
                    $score = $row['avg_score'];
                    $name = strtolower($row['criterion_name']);

                    if ($score < 5) { // Below 50%
                        if (strpos($name, 'financial') !== false) {
                            $supplier['risk_flags'][] = 'Low Financial Strength';
                        } elseif (strpos($name, 'delivery') !== false) {
                            $supplier['risk_flags'][] = 'Poor Delivery Time';
                        } elseif (strpos($name, 'quality') !== false) {
                            $supplier['risk_flags'][] = 'Quality Issues';
                        } elseif (strpos($name, 'price') !== false || strpos($name, 'cost') !== false) {
                            $supplier['risk_flags'][] = 'Poor Price Competitiveness';
                        }
                    }
                }
            }
        }

        $this->view('suppliers/rankings', [
            'rankedSuppliers' => $rankedSuppliers,
            'criteria' => $criteria,
            'supplierBreakdowns' => $supplierBreakdowns
        ]);
    }

    public function exportRankings()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        // Get Company Name for Audit Trail
        $stmt = $conn->prepare("SELECT name FROM companies WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['company_id']);
        $stmt->execute();
        $company = $stmt->fetch(\PDO::FETCH_ASSOC);
        $companyName = $company['name'] ?? 'Unknown Company';

        // Get ranked suppliers with their average scores and latest score for trend
        $stmt = $conn->prepare("
            SELECT 
                s.id,
                s.name,
                s.contact_person,
                s.email,
                COUNT(DISTINCT e.id) as evaluation_count,
                ROUND(AVG(e.total_score), 2) as avg_score,
                (SELECT total_score FROM evaluations WHERE supplier_id = s.id AND status = 'submitted' ORDER BY created_at DESC LIMIT 1) as latest_score,
                MAX(e.created_at) as latest_evaluation
            FROM suppliers s
            LEFT JOIN evaluations e ON s.id = e.supplier_id AND e.status = 'submitted'
            WHERE s.company_id = :company_id
            GROUP BY s.id
            ORDER BY avg_score DESC, s.name ASC
        ");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $rankedSuppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get criteria for breakdown
        $stmt = $conn->prepare("SELECT id, name FROM criteria WHERE company_id = :company_id ORDER BY name");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $filename = "supplier_rankings_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Audit Meta Rows
        fputcsv($output, ['SUPPLIER PERFORMANCE AUDIT REPORT']);
        fputcsv($output, ['GENERATED BY', $_SESSION['name'] ?? 'System Admin']);
        fputcsv($output, ['COMPANY', $companyName]);
        fputcsv($output, ['TIMESTAMP', date('Y-m-d H:i:s')]);
        fputcsv($output, []); // Empty spacing row

        // Build header row
        $headers = ['Rank', 'Supplier Name', 'Contact Person', 'Email', 'Total Score', 'Grade', 'Evaluations'];
        foreach ($criteria as $criterion) {
            $headers[] = $criterion['name'] . ' Score';
        }
        fputcsv($output, $headers);

        // Write data rows
        $rank = 1;
        foreach ($rankedSuppliers as $supplier) {
            $score = $supplier['avg_score'] ?? 0;
            $grade = $score >= 90 ? 'A+' : ($score >= 80 ? 'A' : ($score >= 70 ? 'B+' : ($score >= 60 ? 'B' : 'C')));

            $row = [
                $rank++,
                $supplier['name'],
                $supplier['contact_person'] ?? '',
                $supplier['email'] ?? '',
                $score,
                $supplier['evaluation_count'] > 0 ? $grade : 'N/A',
                $supplier['evaluation_count']
            ];

            // Get criteria scores for this supplier
            if ($supplier['evaluation_count'] > 0) {
                $stmt = $conn->prepare("
                    SELECT 
                        c.id,
                        ROUND(AVG(es.score), 2) as avg_score
                    FROM evaluation_scores es
                    JOIN criteria c ON es.criteria_id = c.id
                    JOIN evaluations e ON es.evaluation_id = e.id
                    WHERE e.supplier_id = :supplier_id AND e.company_id = :company_id AND e.status = 'submitted'
                    GROUP BY c.id
                    ORDER BY c.name
                ");
                $stmt->bindParam(':supplier_id', $supplier['id']);
                $stmt->bindParam(':company_id', $_SESSION['company_id']);
                $stmt->execute();
                $scores = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $scoreMap = [];
                foreach ($scores as $s) {
                    $scoreMap[$s['id']] = $s['avg_score'];
                }

                foreach ($criteria as $criterion) {
                    $row[] = $scoreMap[$criterion['id']] ?? 'N/A';
                }
            } else {
                foreach ($criteria as $criterion) {
                    $row[] = 'N/A';
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function export()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        // Exporting might be considered a premium feature, let's block it for expired subs too
        $this->enforceSubscription();

        $supplier = new Supplier();
        $stmt = $supplier->readAll($_SESSION['company_id']);
        $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $filename = "suppliers_export_" . date('Ymd') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Average Score', 'Contact Person', 'Email', 'Phone', 'Address']);

        foreach ($suppliers as $row) {
            fputcsv($output, [
                $row['name'],
                $row['avg_score'] ?? 'N/A',
                $row['contact_person'],
                $row['email'],
                $row['phone'],
                $row['address']
            ]);
        }
        fclose($output);
        exit;
    }

    public function report()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $db = new \App\Config\Database();
        $conn = $db->getConnection();

        // 1. Fetch Ranked Suppliers
        $stmt = $conn->prepare("
            SELECT 
                s.*,
                ROUND(AVG(e.total_score), 1) as avg_score,
                (SELECT total_score FROM evaluations WHERE supplier_id = s.id AND status = 'submitted' ORDER BY created_at DESC LIMIT 1) as latest_score,
                COUNT(e.id) as evaluation_count
            FROM suppliers s
            LEFT JOIN evaluations e ON s.id = e.supplier_id AND e.status = 'submitted'
            WHERE s.company_id = :company_id
            GROUP BY s.id
            ORDER BY avg_score DESC, evaluation_count DESC
        ");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $rankedSuppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Risk Flag Logic for Report
        $supplierScores = [];
        foreach ($rankedSuppliers as &$supplier) {
            $supplierScores[$supplier['id']] = [];
            $supplier['risk_flags'] = [];
            if ($supplier['evaluation_count'] > 0) {
                // Fetch individual criteria scores for this supplier
                $stmt = $conn->prepare("
                    SELECT c.id as criteria_id, c.name, AVG(es.score) as avg_score
                    FROM evaluation_scores es
                    JOIN criteria c ON es.criteria_id = c.id
                    JOIN evaluations e ON es.evaluation_id = e.id
                    WHERE e.supplier_id = :sid AND e.company_id = :cid AND e.status = 'submitted'
                    GROUP BY c.id, c.name
                ");
                $stmt->execute(['sid' => $supplier['id'], 'cid' => $_SESSION['company_id']]);
                $breakdown = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($breakdown as $row) {
                    $score = $row['avg_score'];
                    $supplierScores[$supplier['id']][$row['criteria_id']] = round($score, 1);

                    $name = strtolower($row['name']);
                    if ($score < 5) {
                        if (strpos($name, 'financial') !== false)
                            $supplier['risk_flags'][] = 'Low Financial Strength';
                        elseif (strpos($name, 'delivery') !== false)
                            $supplier['risk_flags'][] = 'Poor Delivery Time';
                        elseif (strpos($name, 'quality') !== false)
                            $supplier['risk_flags'][] = 'Quality Issues';
                        elseif (strpos($name, 'price') !== false || strpos($name, 'cost') !== false)
                            $supplier['risk_flags'][] = 'Poor Price Competitiveness';
                    }
                }
            }
        }

        // 2. Fetch Criteria for breakdown
        $stmt = $conn->prepare("SELECT id, name FROM criteria WHERE company_id = :company_id ORDER BY name");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $criteria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Fetch Company Info for Header
        $stmt = $conn->prepare("SELECT * FROM companies WHERE id = :company_id");
        $stmt->bindParam(':company_id', $_SESSION['company_id']);
        $stmt->execute();
        $company = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('suppliers/report', [
            'rankedSuppliers' => $rankedSuppliers,
            'criteria' => $criteria,
            'company' => $company,
            'supplierScores' => $supplierScores
        ]);
    }

    public function import()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->view('suppliers/import');
    }

    public function downloadTemplate()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $filename = "supplier_import_template.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Contact Person', 'Email', 'Phone', 'Address']);
        fclose($output);
        exit;
    }

    public function processImport()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $this->enforceSubscription();

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->view('suppliers/import', ['error' => 'Please select a valid CSV file.']);
            return;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        // Parse headers dynamically
        $headers = fgetcsv($handle);
        if (!$headers) {
            $this->view('suppliers/import', ['error' => 'The CSV file appears to be empty.']);
            return;
        }

        // Create a map of header name to index (case-insensitive)
        $headerMap = [];
        foreach ($headers as $index => $label) {
            $label = strtolower(trim($label));
            $headerMap[$label] = $index;
        }

        // Define fuzzy aliases for columns
        $aliases = [
            'name' => ['name', 'supplier name', 'company', 'company name', 'business name', 'supplier'],
            'email' => ['email', 'email address', 'e-mail'],
            'contact' => ['contact person', 'contact', 'representative', 'contact name', 'person'],
            'phone' => ['phone', 'telephone', 'mobile', 'contact number', 'phone number', 'tel'],
            'address' => ['address', 'location', 'office address', 'physical address']
        ];

        $indices = [];
        foreach ($aliases as $key => $variants) {
            foreach ($variants as $variant) {
                if (isset($headerMap[$variant])) {
                    $indices[$key] = $headerMap[$variant];
                    break;
                }
            }
        }

        // Required fields check (Name and Email are mandatory)
        if (!isset($indices['name']) || !isset($indices['email'])) {
            fclose($handle);
            $missing = !isset($indices['name']) ? 'Name (or Supplier Name)' : 'Email';
            $this->view('suppliers/import', [
                'error' => "Required column '$missing' not found in your CSV. Please check your headers."
            ]);
            return;
        }

        $importedCount = 0;
        $skippedCount = 0;
        $company_id = $_SESSION['company_id'];

        $db = new \App\Config\Database();
        $conn = $db->getConnection();
        $conn->beginTransaction();

        try {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (empty(array_filter($data)))
                    continue;

                $name = trim($data[$indices['name']] ?? '');
                $email = trim($data[$indices['email']] ?? '');
                $contact = isset($indices['contact']) ? trim($data[$indices['contact']] ?? '') : '';
                $phone = isset($indices['phone']) ? trim($data[$indices['phone']] ?? '') : '';
                $address = isset($indices['address']) ? trim($data[$indices['address']] ?? '') : '';

                // Validation
                if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skippedCount++;
                    continue;
                }

                // Check for duplicate within company
                $stmt = $conn->prepare("SELECT id FROM suppliers WHERE email = :email AND company_id = :cid");
                $stmt->execute(['email' => $email, 'cid' => $company_id]);
                if ($stmt->fetch()) {
                    $skippedCount++;
                    continue;
                }

                $supplier = new Supplier();
                $supplier->company_id = $company_id;
                $supplier->name = $name;
                $supplier->contact_person = $contact;
                $supplier->email = $email;
                $supplier->phone = $phone;
                $supplier->address = $address;

                if ($supplier->create()) {
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $conn->commit();
            fclose($handle);

            $msg = "Successfully imported $importedCount suppliers.";
            if ($skippedCount > 0) {
                $msg .= " (Skipped $skippedCount rows due to duplicates or invalid data).";
            }

            AuditLogger::log("Bulk Import Completed", "Imported: $importedCount, Skipped: $skippedCount");

            $this->view('suppliers/import', ['success' => $msg]);

        } catch (\Exception $e) {
            $conn->rollBack();
            if ($handle)
                fclose($handle);
            $this->view('suppliers/import', ['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $id = $_GET['id'] ?? null;
        $supplierModel = new Supplier();
        $supplierModel->id = $id;
        $supplierModel->company_id = $_SESSION['company_id'];
        $supplier = $supplierModel->readOne();

        if (!$supplier) {
            $this->redirect('/suppliers');
            return;
        }

        $this->view('suppliers/edit', ['supplier' => $supplier]);
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $this->enforceSubscription();

        $id = $_POST['id'] ?? null;
        $supplier = new Supplier();
        $supplier->id = $id;
        $supplier->company_id = $_SESSION['company_id'];

        if (!$supplier->readOne()) {
            $this->redirect('/suppliers');
            return;
        }

        $supplier->name = $_POST['name'] ?? '';
        $supplier->contact_person = $_POST['contact_person'] ?? '';
        $supplier->email = $_POST['email'] ?? '';
        $supplier->phone = $_POST['phone'] ?? '';
        $supplier->address = $_POST['address'] ?? '';
        $supplier->reevaluation_days = isset($_POST['reevaluation_days']) ? max(1, (int) $_POST['reevaluation_days']) : 90;

        if ($supplier->update()) {
            AuditLogger::log("Supplier Updated", "ID: $id, Name: " . $supplier->name);
            $this->redirect('/suppliers');
        } else {
            $this->view('suppliers/edit', ['supplier' => (array) $supplier, 'error' => 'Unable to update supplier.']);
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        // Only Admin can delete
        if ($_SESSION['role'] !== 'company_admin') {
            $this->redirect('/suppliers');
            return;
        }

        $id = $_POST['id'] ?? null;
        $supplier = new Supplier();
        $supplier->id = $id;
        $supplier->company_id = $_SESSION['company_id'];

        $supplier->delete();
        $this->redirect('/suppliers');
    }
}
