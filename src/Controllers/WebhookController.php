<?php

namespace App\Controllers;

use App\Config\Database;
use App\Config\Paystack;
use App\Services\CompanyPlan;

class WebhookController extends Controller
{
    public function handle()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        // Retrieve the request's body
        $input = @file_get_contents("php://input");
        $secretKey = Paystack::getSecretKey();

        // Verify the signature
        if (!isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) || ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, $secretKey))) {
            exit;
        }

        http_response_code(200);
        $event = json_decode($input);

        if ($event->event === 'charge.success') {
            $reference = $event->data->reference;
            $company_id = $event->data->metadata->company_id ?? null;
            $amount = $event->data->amount / 100;

            if ($company_id) {
                $db = new Database();
                $conn = $db->getConnection();

                // Check if already processed to avoid duplicates
                $stmt = $conn->prepare("SELECT id FROM transactions WHERE transaction_id = :tx_id");
                $stmt->bindParam(':tx_id', $reference);
                $stmt->execute();

                if (!$stmt->fetch()) {
                    // Update Company Subscription
                    $new_end_date = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $stmt = $conn->prepare("UPDATE companies SET subscription_status = 'active', subscription_ends_at = :end_date WHERE id = :id");
                    $stmt->bindParam(":end_date", $new_end_date);
                    $stmt->bindParam(":id", $company_id);
                    $stmt->execute();

                    // Log Transaction
                    $stmt = $conn->prepare("INSERT INTO transactions (company_id, amount, status, transaction_id) VALUES (:cid, :amount, 'succeeded', :tx_id)");
                    $stmt->bindParam(':cid', $company_id);
                    $stmt->bindParam(':amount', $amount);
                    $stmt->bindParam(':tx_id', $reference);
                    $stmt->execute();

                    CompanyPlan::applyPaidUpgrade($conn, (int) $company_id);
                }
            }
        }

        exit;
    }
}
