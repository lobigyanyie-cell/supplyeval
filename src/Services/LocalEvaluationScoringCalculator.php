<?php

namespace App\Services;

/**
 * Legacy weighted scoring (same formula as Java {@code WeightedEvaluationService}).
 * Used when {@see EvaluationScoringService} has no remote URL or when fallback is enabled.
 */
class LocalEvaluationScoringCalculator
{
    public const LOW_SCORE_THRESHOLD = 50.0;

    /**
     * @param array<int, array<string, mixed>> $allCriteria rows from criteria table
     * @param array<int|string, float|int|string> $scoresInput criteria_id => raw score
     * @return array{total_score: float, evaluation_scores: list<array{criteria_id: int, score: float}>, low_score_alert: bool}
     */
    public static function calculate(array $allCriteria, array $scoresInput): array
    {
        $total_score = 0.0;
        $evaluation_scores = [];

        foreach ($allCriteria as $criterion) {
            $c_id = (int) $criterion['id'];
            $weight = (float) $criterion['weight'];
            $max = (int) $criterion['max_score'];

            $input_score = isset($scoresInput[$c_id]) ? (float) $scoresInput[$c_id] : 0.0;
            if (!isset($scoresInput[$c_id]) && isset($scoresInput[(string) $c_id])) {
                $input_score = (float) $scoresInput[(string) $c_id];
            }

            $calculated_points = ($max > 0) ? ($input_score / $max) * $weight : 0.0;
            $total_score += $calculated_points;

            $evaluation_scores[] = [
                'criteria_id' => $c_id,
                'score' => $input_score,
            ];
        }

        return [
            'total_score' => $total_score,
            'evaluation_scores' => $evaluation_scores,
            'low_score_alert' => $total_score < self::LOW_SCORE_THRESHOLD,
        ];
    }
}
