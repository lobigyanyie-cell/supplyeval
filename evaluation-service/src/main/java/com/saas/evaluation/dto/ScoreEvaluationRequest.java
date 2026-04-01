package com.saas.evaluation.dto;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotEmpty;
import jakarta.validation.constraints.NotNull;

import java.util.List;
import java.util.Map;

public class ScoreEvaluationRequest {

    @NotEmpty
    @Valid
    private List<CriterionInput> criteria;

    /**
     * Raw scores keyed by criteria id (string keys, e.g. "12" -> 8.0).
     */
    @NotNull
    private Map<String, Double> scores;

    public List<CriterionInput> getCriteria() {
        return criteria;
    }

    public void setCriteria(List<CriterionInput> criteria) {
        this.criteria = criteria;
    }

    public Map<String, Double> getScores() {
        return scores;
    }

    public void setScores(Map<String, Double> scores) {
        this.scores = scores;
    }
}
