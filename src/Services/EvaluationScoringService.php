<?php

namespace App\Services;

use App\Config\Settings;

/**
 * Delegates weighted evaluation scoring to the Spring Boot service when configured.
 *
 * Configuration (first match wins):
 * - Environment variable {@code EVALUATION_SERVICE_URL} (e.g. http://127.0.0.1:8080)
 * - Database settings key {@code evaluation_service_url}
 *
 * If no URL is set, {@see LocalEvaluationScoringCalculator} runs in-process (legacy dev).
 *
 * On HTTP failure, enable fallback via env {@code EVALUATION_SERVICE_FALLBACK}, or settings key {@code evaluation_service_fallback} (Platform Settings), or use the local calculator.
 */
class EvaluationScoringService
{
    /**
     * @param array<int, array<string, mixed>> $allCriteria
     * @param array<int|string, mixed> $scoresInput
     * @return array{total_score: float, evaluation_scores: list<array{criteria_id: int, score: float}>, low_score_alert: bool}
     */
    public function score(array $allCriteria, array $scoresInput): array
    {
        $baseUrl = getenv('EVALUATION_SERVICE_URL');
        if ($baseUrl === false || trim((string) $baseUrl) === '') {
            $baseUrl = Settings::get('evaluation_service_url', '');
        }

        if (trim((string) $baseUrl) === '') {
            return LocalEvaluationScoringCalculator::calculate($allCriteria, $scoresInput);
        }

        $url = rtrim((string) $baseUrl, '/') . '/api/v1/evaluations/score';

        $criteriaPayload = [];
        foreach ($allCriteria as $c) {
            $criteriaPayload[] = [
                'id' => (int) $c['id'],
                'weight' => (float) $c['weight'],
                'maxScore' => (int) $c['max_score'],
            ];
        }

        $scoresPayload = [];
        foreach ($allCriteria as $c) {
            $cid = (int) $c['id'];
            $raw = 0.0;
            if (isset($scoresInput[$cid])) {
                $raw = (float) $scoresInput[$cid];
            } elseif (isset($scoresInput[(string) $cid])) {
                $raw = (float) $scoresInput[(string) $cid];
            }
            $scoresPayload[(string) $cid] = $raw;
        }

        $payload = json_encode([
            'criteria' => $criteriaPayload,
            'scores' => $scoresPayload,
        ], JSON_THROW_ON_ERROR);

        $result = $this->postJson($url, $payload);
        if ($result['error'] !== null) {
            return $this->onRemoteFailure($allCriteria, $scoresInput, $result['error']);
        }

        $response = $result['body'];
        $httpCode = $result['status'];

        if ($httpCode < 200 || $httpCode >= 300) {
            return $this->onRemoteFailure(
                $allCriteria,
                $scoresInput,
                'HTTP ' . $httpCode . ' ' . (string) $response
            );
        }

        $data = json_decode((string) $response, true);
        if (!is_array($data) || !array_key_exists('totalScore', $data)) {
            return $this->onRemoteFailure($allCriteria, $scoresInput, 'Invalid JSON from evaluation service');
        }

        $evaluation_scores = [];
        foreach ($data['lineItems'] ?? [] as $item) {
            $evaluation_scores[] = [
                'criteria_id' => (int) ($item['criteriaId'] ?? $item['criteria_id'] ?? 0),
                'score' => (float) ($item['rawScore'] ?? $item['raw_score'] ?? 0),
            ];
        }

        return [
            'total_score' => (float) $data['totalScore'],
            'evaluation_scores' => $evaluation_scores,
            'low_score_alert' => !empty($data['lowScoreAlert']),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $allCriteria
     * @param array<int|string, mixed> $scoresInput
     * @return array{total_score: float, evaluation_scores: list<array{criteria_id: int, score: float}>, low_score_alert: bool}
     */
    private function onRemoteFailure(array $allCriteria, array $scoresInput, string $message): array
    {
        $fallback = getenv('EVALUATION_SERVICE_FALLBACK');
        if ($fallback === false || $fallback === '') {
            $fallback = Settings::get('evaluation_service_fallback', '0');
        }
        if ($fallback === '1' || strtolower((string) $fallback) === 'true') {
            error_log('[EvaluationScoringService] Remote scoring failed, using fallback: ' . $message);
            return LocalEvaluationScoringCalculator::calculate($allCriteria, $scoresInput);
        }

        throw new \RuntimeException(
            'Evaluation scoring service is unavailable. Start the Java evaluation-service, enable fallback in Platform Settings, or set EVALUATION_SERVICE_FALLBACK=1. Detail: ' . $message
        );
    }

    /**
     * @return array{error: ?string, body: string, status: int}
     */
    private function postJson(string $url, string $jsonBody): array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($ch === false) {
                return ['error' => 'Could not init cURL', 'body' => '', 'status' => 0];
            }
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS => $jsonBody,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
                // Prefer IPv4: avoids ::1 vs 127.0.0.1 mismatches on Windows when Java listens on IPv4 only
                CURLOPT_IPRESOLVE => defined('CURL_IPRESOLVE_V4') ? CURL_IPRESOLVE_V4 : 1,
            ]);
            $response = curl_exec($ch);
            $errno = curl_errno($ch);
            $err = curl_error($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($response === false) {
                $detail = $this->formatCurlFailure($errno, $err);
                return ['error' => $detail, 'body' => '', 'status' => $httpCode];
            }
            return ['error' => null, 'body' => (string) $response, 'status' => $httpCode];
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content' => $jsonBody,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            return ['error' => 'HTTP request failed (enable curl or allow_url_fopen)', 'body' => '', 'status' => 0];
        }
        $status = 0;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $h) {
                if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) {
                    $status = (int) $m[1];
                    break;
                }
            }
        }
        return ['error' => null, 'body' => $response, 'status' => $status];
    }

    /**
     * cURL often returns errno with an empty string on Windows; map common codes for clarity.
     */
    private function formatCurlFailure(int $errno, string $err): string
    {
        $hint = '';
        switch ($errno) {
            case 7:
                $hint = ' (connection refused — is the Java service running on that host/port?)';
                break;
            case 6:
                $hint = ' (could not resolve host — use http://127.0.0.1:8080 on XAMPP, not http://evaluation:8080)';
                break;
            case 28:
                $hint = ' (timeout — firewall or wrong URL/port)';
                break;
            case 52:
                $hint = ' (empty reply — wrong protocol http/https or nothing listening)';
                break;
        }
        $msg = $err !== '' ? $err : '(empty cURL message)';
        return sprintf('cURL error %d: %s%s', $errno, $msg, $hint);
    }
}
