<?php

namespace App\Services;

use App\Config\Settings;

class EmailService
{
    private static string $lastError = '';

    private static function readBrevoKeyFromEnv(): string
    {
        return trim((string) (getenv('BREVO_API_KEY') ?: ''));
    }

    private static function readBrevoKeyFromDb(): string
    {
        return trim((string) Settings::get('brevo_api_key', ''));
    }

    public static function getLastError(): string
    {
        return self::$lastError;
    }

    /**
     * Send a professional HTML email via Brevo/SendGrid with mail() fallback.
     */
    public static function send($to, $subject, $messageBody, $ctaText = null, $ctaUrl = null)
    {
        self::$lastError = '';
        $brevoApiKey = self::readBrevoKeyFromEnv();
        if ($brevoApiKey === '') {
            $brevoApiKey = self::readBrevoKeyFromDb();
        }
        $sendgridApiKey = trim((string) (getenv('SENDGRID_API_KEY') ?: Settings::get('sendgrid_api_key', '')));
        $fromEmail = trim((string) Settings::get('smtp_from', 'noreply@suppliereval.com'));
        $siteName = Settings::get('site_name', 'SupplierEval');
        $htmlContent = self::getTemplate($subject, $messageBody, $ctaText, $ctaUrl);
        

        if (!empty($brevoApiKey) && self::sendViaBrevo($brevoApiKey, $to, $subject, $htmlContent, $fromEmail, $siteName)) {
            return true;
        }

        if (!empty($sendgridApiKey) && self::sendViaSendGrid($sendgridApiKey, $to, $subject, $htmlContent, $fromEmail, $siteName)) {
            return true;
        }

        // Final fallback to basic mail()
        if (empty($brevoApiKey) && empty($sendgridApiKey)) {
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
                'From: ' . $siteName . ' <' . $fromEmail . '>',
                'X-Mailer: PHP/' . phpversion()
            ];
            $ok = mail($to, $subject, $htmlContent, implode("\r\n", $headers));
            if (!$ok) {
                self::$lastError = 'PHP mail() failed.';
            }
            return $ok;
        }

        if (self::$lastError === '') {
            self::$lastError = 'No email provider accepted the message.';
        }
        return false;
    }

    private static function sendViaSendGrid(string $apiKey, string $to, string $subject, string $htmlContent, string $fromEmail, string $siteName): bool
    {
        $data = [
            'personalizations' => [
                [
                    'to' => [['email' => $to]]
                ]
            ],
            'from' => [
                'email' => $fromEmail,
                'name' => $siteName
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $htmlContent
                ]
            ]
        ];

        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false) {
            self::$lastError = 'SendGrid cURL error: ' . curl_error($ch);
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            self::$lastError = 'SendGrid error (' . $httpCode . '): ' . self::normalizeError($response);
        }
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private static function sendViaBrevo(string $apiKey, string $to, string $subject, string $htmlContent, string $fromEmail, string $siteName): bool
    {
        $data = [
            'sender' => [
                'name' => $siteName,
                'email' => $fromEmail,
            ],
            'to' => [
                ['email' => $to],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false) {
            self::$lastError = 'Brevo cURL error: ' . curl_error($ch);
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            self::$lastError = 'Brevo error (' . $httpCode . '): ' . self::normalizeError($response);
        }
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private static function getTemplate($title, $body, $ctaText, $ctaUrl)
    {
        $siteName = Settings::get('site_name', 'SupplierEval');
        $primaryColor = '#4f46e5'; // Indigo-600

        $ctaHtml = "";
        if ($ctaText && $ctaUrl) {
            $ctaHtml = "
                <tr>
                    <td align='center' style='padding: 30px 0;'>
                        <a href='{$ctaUrl}' style='background-color: {$primaryColor}; color: #ffffff; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: bold; display: inline-block;'>
                            {$ctaText}
                        </a>
                    </td>
                </tr>
            ";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
                .header { background: #0f172a; padding: 40px; text-align: center; }
                .content { padding: 40px; color: #334155; line-height: 1.6; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; background: #f8fafc; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div style='color: white; font-size: 28px; font-weight: 900; letter-spacing: -1.5px;'>S</div>
                    <div style='color: #6366f1; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-top: 8px;'>System Notification</div>
                </div>
                <div class='content'>
                    <h1 style='font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0; letter-spacing: -0.5px;'>{$title}</h1>
                    <p style='font-size: 16px; color: #475569;'>{$body}</p>
                    <table width='100%' cellspacing='0' cellpadding='0'>
                        {$ctaHtml}
                    </table>
                    <div style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;'>
                        <p style='font-size: 13px; color: #94a3b8;'>
                            Questions? Reach out to our team at " . Settings::get('support_email', 'support@example.com') . "
                        </p>
                    </div>
                </div>
                <div class='footer'>
                    &copy; " . date('Y') . " {$siteName}. Highly Secured SaaS Environment.
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private static function normalizeError(string $response): string
    {
        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            if (!empty($decoded['message']) && is_string($decoded['message'])) {
                return $decoded['message'];
            }
            if (!empty($decoded['errors']) && is_array($decoded['errors'])) {
                $first = $decoded['errors'][0] ?? null;
                if (is_array($first) && !empty($first['message']) && is_string($first['message'])) {
                    return $first['message'];
                }
            }
        }
        $trimmed = trim($response);
        return $trimmed === '' ? 'Unknown provider error' : $trimmed;
    }
}
