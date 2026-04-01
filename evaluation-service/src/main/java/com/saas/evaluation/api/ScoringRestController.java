package com.saas.evaluation.api;

import com.saas.evaluation.dto.ScoreEvaluationRequest;
import com.saas.evaluation.dto.ScoreEvaluationResponse;
import com.saas.evaluation.service.WeightedEvaluationService;
import jakarta.validation.Valid;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.Map;

@RestController
@RequestMapping(path = "/api/v1", produces = MediaType.APPLICATION_JSON_VALUE)
public class ScoringRestController {

    private final WeightedEvaluationService weightedEvaluationService;

    public ScoringRestController(WeightedEvaluationService weightedEvaluationService) {
        this.weightedEvaluationService = weightedEvaluationService;
    }

    @GetMapping("/health")
    public Map<String, String> health() {
        return Map.of("status", "UP", "service", "evaluation-service");
    }

    @PostMapping(path = "/evaluations/score", consumes = MediaType.APPLICATION_JSON_VALUE)
    public ScoreEvaluationResponse score(@Valid @RequestBody ScoreEvaluationRequest request) {
        return weightedEvaluationService.score(request);
    }
}
