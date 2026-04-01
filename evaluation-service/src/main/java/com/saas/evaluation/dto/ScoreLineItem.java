package com.saas.evaluation.dto;

public class ScoreLineItem {

    private long criteriaId;
    private double rawScore;
    private double weightedPoints;

    public ScoreLineItem() {
    }

    public ScoreLineItem(long criteriaId, double rawScore, double weightedPoints) {
        this.criteriaId = criteriaId;
        this.rawScore = rawScore;
        this.weightedPoints = weightedPoints;
    }

    public long getCriteriaId() {
        return criteriaId;
    }

    public void setCriteriaId(long criteriaId) {
        this.criteriaId = criteriaId;
    }

    public double getRawScore() {
        return rawScore;
    }

    public void setRawScore(double rawScore) {
        this.rawScore = rawScore;
    }

    public double getWeightedPoints() {
        return weightedPoints;
    }

    public void setWeightedPoints(double weightedPoints) {
        this.weightedPoints = weightedPoints;
    }
}
