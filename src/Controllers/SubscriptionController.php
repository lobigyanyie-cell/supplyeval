<?php

namespace App\Controllers;

use App\Config\Database;
use App\Services\AuditLogger;
use App\Services\CompanyPlan;

class SubscriptionController extends Controller
{

    public function showUpgrade()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $this->view('subscription/upgrade');
    }

    public function process()
    {
        if (!isset($_SESSION['company_id'])) {
            $this->redirect('/login');
            return;
        }

        $reference = $_GET['reference'] ?? null;

        if (!$reference) {
            $this->view('subscription/upgrade', ['error' => 'No transaction reference found.']);
            return;
        }

        $secretKey = \App\Config\Paystack::getSecretKey();

        // 1. Verify Transaction with Paystack API
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Bearer " . $secretKey,
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->view('subscription/upgrade', ['error' => 'Payment verification failed: ' . $err]);
            return;
        }

        $result = json_decode($response);

        if ($result && $result->status && $result->data->status === 'success') {
            $company_id = $_SESSION['company_id'];
            $amount = $result->data->amount / 100; // Convert kobo to standard unit

            $db = new Database();
            $conn = $db->getConnection();

            // 1. Update Company Subscription
            $new_end_date = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $conn->prepare("UPDATE companies SET subscription_status = 'active', subscription_ends_at = :end_date WHERE id = :id");
            $stmt->bindParam(":end_date", $new_end_date);
            $stmt->bindParam(":id", $company_id);
            $stmt->execute();

            CompanyPlan::applyPaidUpgrade($conn, (int) $company_id);

            // 2. Log Transaction
            $stmt = $conn->prepare("INSERT INTO transactions (company_id, amount, status, transaction_id) VALUES (:cid, :amount, 'succeeded', :tx_id)");
            $stmt->bindParam(':cid', $company_id);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':tx_id', $reference);
            $stmt->execute();

            // Update Session
            $_SESSION['subscription_status'] = 'active';

            AuditLogger::log("Subscription Upgraded", "Reference: $reference, Amount: GHS $amount");

            // Success Redirect
            $this->redirect('/dashboard?payment=success');
        } else {
            $msg = $result->message ?? 'Payment verification failed.';
            $this->view('subscription/upgrade', ['error' => $msg]);
        }
    }

    public function history()
    {
        if (!isset($_SESSION['company_id'])) {
            $this->redirect('/login');
            return;
        }

        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM transactions WHERE company_id = :cid ORDER BY created_at DESC");
        $stmt->bindParam(':cid', $_SESSION['company_id']);
        $stmt->execute();
        $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('subscription/history', ['transactions' => $transactions]);
    }

    public function viewInvoice()
    {
        if (!isset($_SESSION['company_id'])) {
            $this->redirect('/login');
            return;
        }

        $tx_id = $_GET['id'] ?? null;
        if (!$tx_id) {
            $this->redirect('/subscription/history');
            return;
        }

        $db = new Database();
        $conn = $db->getConnection();

        // Fetch transaction
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE id = :id AND company_id = :cid");
        $stmt->bindParam(':id', $tx_id);
        $stmt->bindParam(':cid', $_SESSION['company_id']);
        $stmt->execute();
        $transaction = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$transaction) {
            $this->redirect('/subscription/history');
            return;
        }

        // Fetch company info
        $stmt = $conn->prepare("SELECT * FROM companies WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['company_id']);
        $stmt->execute();
        $company = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('subscription/invoice', [
            'transaction' => $transaction,
            'company' => $company
        ]);
    }
}
