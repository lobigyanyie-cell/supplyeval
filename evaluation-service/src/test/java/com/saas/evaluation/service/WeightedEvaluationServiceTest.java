package com.saas.evaluation.service;

import com.saas.evaluation.dto.CriterionInput;
import com.saas.evaluation.dto.ScoreEvaluationRequest;
import com.saas.evaluation.dto.ScoreEvaluationResponse;
import org.junit.jupiter.api.Test;

import java.util.List;
import java.util.Map;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.within;

class WeightedEvaluationServiceTest {

    private final WeightedEvaluationService service = new WeightedEvaluationService();

    @Test
    void matchesLegacyPhpExample() {
        CriterionInput q = new CriterionInput();
        q.setId(1L);
        q.setWeight(20.0);
        q.setMaxScore(10);

        ScoreEvaluationRequest req = new ScoreEvaluationRequest();
        req.setCriteria(List.of(q));
        req.setScores(Map.of("1", 8.0));

        ScoreEvaluationResponse res = service.score(req);

        assertThat(res.getTotalScore()).isCloseTo(16.0, within(0.001));
        assertThat(res.isLowScoreAlert()).isTrue();
    }

    @Test
    void sumsMultipleCriteria() {
        CriterionInput a = new CriterionInput();
        a.setId(1L);
        a.setWeight(40.0);
        a.setMaxScore(10);

        CriterionInput b = new CriterionInput();
        b.setId(2L);
        b.setWeight(60.0);
        b.setMaxScore(10);

        ScoreEvaluationRequest req = new ScoreEvaluationRequest();
        req.setCriteria(List.of(a, b));
        req.setScores(Map.of("1", 10.0, "2", 5.0));

        ScoreEvaluationResponse res = service.score(req);

        assertThat(res.getTotalScore()).isCloseTo(70.0, within(0.001));
        assertThat(res.isLowScoreAlert()).isFalse();
    }
}
