package com.saas.evaluation.service;

import com.saas.evaluation.dto.CriterionInput;
import com.saas.evaluation.dto.ScoreEvaluationRequest;
import com.saas.evaluation.dto.ScoreEvaluationResponse;
import com.saas.evaluation.dto.ScoreLineItem;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 * Mirrors legacy PHP: weightedPoints = (input / maxScore) * weight when maxScore &gt; 0, else 0.
 * totalScore is the sum of weightedPoints across all company criteria.
 */
@Service
public class WeightedEvaluationService {

    public ScoreEvaluationResponse score(ScoreEvaluationRequest request) {
        Map<String, Double> scores = request.getScores();
        List<ScoreLineItem> lineItems = new ArrayList<>();
        double total = 0.0;

        for (CriterionInput c : request.getCriteria()) {
            double input = resolveRawScore(scores, c.getId());
            int max = c.getMaxScore() != null ? c.getMaxScore() : 0;
            double weighted = max > 0 ? (input / max) * c.getWeight() : 0.0;
            total += weighted;
            lineItems.add(new ScoreLineItem(c.getId(), input, weighted));
        }

        ScoreEvaluationResponse response = new ScoreEvaluationResponse();
        response.setTotalScore(total);
        response.setLineItems(lineItems);
        response.setLowScoreAlert(total < ScoreEvaluationResponse.LOW_SCORE_THRESHOLD);
        return response;
    }

    private double resolveRawScore(Map<String, Double> scores, long criteriaId) {
        if (scores == null || scores.isEmpty()) {
            return 0.0;
        }
        String key = Long.toString(criteriaId);
        if (scores.containsKey(key) && scores.get(key) != null) {
            return scores.get(key);
        }
        // tolerate numeric keys serialized differently
        for (Map.Entry<String, Double> e : scores.entrySet()) {
            if (key.equals(e.getKey())) {
                return e.getValue() != null ? e.getValue() : 0.0;
            }
        }
        return 0.0;
    }
}
