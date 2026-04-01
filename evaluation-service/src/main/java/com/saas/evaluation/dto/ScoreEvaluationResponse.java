package com.saas.evaluation.dto;

import java.util.ArrayList;
import java.util.List;

public class ScoreEvaluationResponse {

    public static final double LOW_SCORE_THRESHOLD = 50.0;

    private double totalScore;
    private List<ScoreLineItem> lineItems = new ArrayList<>();
    private boolean lowScoreAlert;

    public double getTotalScore() {
        return totalScore;
    }

    public void setTotalScore(double totalScore) {
        this.totalScore = totalScore;
    }

    public List<ScoreLineItem> getLineItems() {
        return lineItems;
    }

    public void setLineItems(List<ScoreLineItem> lineItems) {
        this.lineItems = lineItems;
    }

    public boolean isLowScoreAlert() {
        return lowScoreAlert;
    }

    public void setLowScoreAlert(boolean lowScoreAlert) {
        this.lowScoreAlert = lowScoreAlert;
    }
}
