# SaaS Evaluation Service - Java Code Documentation

## Project Overview

This is a **Spring Boot microservice** that performs weighted scoring calculations for evaluating criteria (e.g., company assessments, supplier evaluations). It mirrors a legacy PHP scoring algorithm.

---

## File 1: EvaluationApplication.java

**Location:** `src/main/java/com/saas/evaluation/EvaluationApplication.java`

### Purpose
The entry point of the Spring Boot application. This class bootstraps the entire microservice.

### Code Breakdown

```java
package com.saas.evaluation;
```
- Declares the package name following standard Java naming conventions (reverse domain).

```java
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
```
- Imports Spring Boot classes needed to run the application.

```java
@SpringBootApplication
```
- **Annotation** that combines three essential Spring annotations:
  - `@Configuration` - Marks this as a configuration class
  - `@EnableAutoConfiguration` - Enables Spring Boot's auto-configuration magic
  - `@ComponentScan` - Scans for beans (services, controllers) in this package and sub-packages

```java
public class EvaluationApplication {
```
- Main application class.

```java
    public static void main(String[] args) {
        SpringApplication.run(EvaluationApplication.class, args);
    }
```
- The `main()` method that launches the Spring Boot application.
- `SpringApplication.run()` starts the embedded Tomcat server and initializes the application context.

---

## File 2: ScoringRestController.java

**Location:** `src/main/java/com/saas/evaluation/api/ScoringRestController.java`

### Purpose
REST API controller that handles HTTP requests for the scoring service.

### Code Breakdown

```java
package com.saas.evaluation.api;
```
- Package for API layer components.

```java
import com.saas.evaluation.dto.ScoreEvaluationRequest;
import com.saas.evaluation.dto.ScoreEvaluationResponse;
import com.saas.evaluation.service.WeightedEvaluationService;
```
- Imports DTOs (Data Transfer Objects) and the service class.

```java
import jakarta.validation.Valid;
```
- Jakarta Validation API - ensures request data meets validation constraints.

```java
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.*;
```
- Spring Web MVC annotations for building REST endpoints.

```java
@RestController
```
- Marks this class as a REST controller that returns JSON/XML responses (not views).

```java
@RequestMapping(path = "/api/v1", produces = MediaType.APPLICATION_JSON_VALUE)
```
- Base URL path for all endpoints in this controller: `/api/v1`
- `produces = MediaType.APPLICATION_JSON_VALUE` ensures all responses are JSON.

```java
private final WeightedEvaluationService weightedEvaluationService;
```
- **Dependency Injection**: Service is injected via constructor (Spring best practice).
- `final` ensures the reference cannot be changed after initialization.

```java
public ScoringRestController(WeightedEvaluationService weightedEvaluationService) {
    this.weightedEvaluationService = weightedEvaluationService;
}
```
- **Constructor Injection**: Spring automatically provides the bean instance.
- This is the preferred injection method (easier to test, immutable dependencies).

```java
@GetMapping("/health")
public Map<String, String> health() {
    return Map.of("status", "UP", "service", "evaluation-service");
}
```
- **Health Check Endpoint**: `GET /api/v1/health`
- Returns a simple JSON map: `{"status": "UP", "service": "evaluation-service"}`
- Used by load balancers and monitoring tools to check if service is alive.

```java
@PostMapping(path = "/evaluations/score", consumes = MediaType.APPLICATION_JSON_VALUE)
public ScoreEvaluationResponse score(@Valid @RequestBody ScoreEvaluationRequest request) {
    return weightedEvaluationService.score(request);
}
```
- **Scoring Endpoint**: `POST /api/v1/evaluations/score`
- `@RequestBody`: Deserializes incoming JSON into `ScoreEvaluationRequest` object
- `@Valid`: Triggers validation annotations on the request DTO (e.g., `@NotNull`, `@NotEmpty`)
- Delegates to `WeightedEvaluationService.score()` and returns the response

---

## File 3: CriterionInput.java

**Location:** `src/main/java/com/saas/evaluation/dto/CriterionInput.java`

### Purpose
DTO representing a single evaluation criterion with its configuration (weight, max score).

### Code Breakdown

```java
package com.saas.evaluation.dto;
```
- Package for Data Transfer Objects.

```java
import com.fasterxml.jackson.annotation.JsonProperty;
```
- Jackson annotation to map JSON property names to Java fields.

```java
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.PositiveOrZero;
```
- Bean Validation constraints for input validation.

```java
public class CriterionInput {
```
- Plain Java class (POJO) representing input data structure.

```java
    @NotNull
    private Long id;
```
- `@NotNull`: Ensures the ID cannot be null in the request.
- `Long`: Wrapper type for the criterion ID (allows null, unlike `long`).

```java
    @NotNull
    @PositiveOrZero
    private Double weight;
```
- `@PositiveOrZero`: Ensures weight is >= 0 (can't have negative weights).
- `Double`: Weight value (e.g., 20.0 means 20% weight).

```java
    @JsonProperty("maxScore")
    @NotNull
    @PositiveOrZero
    private Integer maxScore;
```
- `@JsonProperty("maxScore")`: Maps JSON field `maxScore` to this Java field.
- `Integer`: Maximum possible score for this criterion (e.g., 10 means score of 0-10).

**Getters and Setters:**
```java
public Long getId() { return id; }
public void setId(Long id) { this.id = id; }
```
- Standard JavaBean accessors for serialization/deserialization.
- Jackson uses these to convert JSON ↔ Java object.

---

## File 4: ScoreEvaluationRequest.java

**Location:** `src/main/java/com/saas/evaluation/dto/ScoreEvaluationRequest.java`

### Purpose
DTO representing the incoming scoring request containing criteria definitions and raw scores.

### Code Breakdown

```java
package com.saas.evaluation.dto;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotEmpty;
import jakarta.validation.constraints.NotNull;

import java.util.List;
import java.util.Map;
```
- Validation imports and Java collections for holding data.

```java
public class ScoreEvaluationRequest {
```
- Request payload class.

```java
    @NotEmpty
    @Valid
    private List<CriterionInput> criteria;
```
- `@NotEmpty`: List must contain at least one criterion.
- `@Valid`: Cascades validation to each `CriterionInput` object in the list.
- `List<CriterionInput>`: All criteria being evaluated.

```java
    /**
     * Raw scores keyed by criteria id (string keys, e.g. "12" -> 8.0).
     */
    @NotNull
    private Map<String, Double> scores;
```
- `@NotNull`: Scores map cannot be null.
- `Map<String, Double>`: Raw scores where:
  - **Key**: Criterion ID as String (e.g., "12")
  - **Value**: The actual score given (e.g., 8.0 out of maxScore)

**Example JSON Request:**
```json
{
  "criteria": [
    {"id": 1, "weight": 20.0, "maxScore": 10},
    {"id": 2, "weight": 30.0, "maxScore": 10}
  ],
  "scores": {
    "1": 8.0,
    "2": 5.0
  }
}
```

---

## File 5: ScoreEvaluationResponse.java

**Location:** `src/main/java/com/saas/evaluation/dto/ScoreEvaluationResponse.java`

### Purpose
DTO representing the scoring result returned to the client.

### Code Breakdown

```java
package com.saas.evaluation.dto;

import java.util.ArrayList;
import java.util.List;
```
- Collections imports for storing line items.

```java
public class ScoreEvaluationResponse {
```
- Response payload class.

```java
    public static final double LOW_SCORE_THRESHOLD = 50.0;
```
- **Constant**: Threshold below which a score is considered "low" (triggers alert).
- `static final`: Class-level constant, unmodifiable.

```java
    private double totalScore;
```
- The final calculated weighted score (sum of all weighted points).

```java
    private List<ScoreLineItem> lineItems = new ArrayList<>();
```
- Detailed breakdown of each criterion's contribution.
- Initialized to empty list to avoid NullPointerException.

```java
    private boolean lowScoreAlert;
```
- Flag indicating if `totalScore < LOW_SCORE_THRESHOLD`.
- Client can use this to show warnings.

```java
public boolean isLowScoreAlert() { return lowScoreAlert; }
```
- Note: Boolean getter uses `isXxx()` naming convention, not `getXxx()`.

---

## File 6: ScoreLineItem.java

**Location:** `src/main/java/com/saas/evaluation/dto/ScoreLineItem.java`

### Purpose
DTO representing a single line item in the scoring breakdown.

### Code Breakdown

```java
public class ScoreLineItem {

    private long criteriaId;
    private double rawScore;
    private double weightedPoints;
```
- `criteriaId`: Which criterion this line represents.
- `rawScore`: The input score provided (before weighting).
- `weightedPoints`: Calculated weighted contribution to total.

```java
    public ScoreLineItem() {
    }
```
- **No-arg constructor**: Required by Jackson for JSON deserialization.

```java
    public ScoreLineItem(long criteriaId, double rawScore, double weightedPoints) {
        this.criteriaId = criteriaId;
        this.rawScore = rawScore;
        this.weightedPoints = weightedPoints;
    }
```
- **Convenience constructor**: Used in service to create objects easily.

---

## File 7: WeightedEvaluationService.java

**Location:** `src/main/java/com/saas/evaluation/service/WeightedEvaluationService.java`

### Purpose
Core business logic for calculating weighted scores. Mirrors a legacy PHP algorithm.

### Code Breakdown

```java
package com.saas.evaluation.service;
```
- Package for business service classes.

```java
import com.saas.evaluation.dto.*;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
```
- Imports for DTOs, Spring stereotype, and collections.

```java
/**
 * Mirrors legacy PHP: weightedPoints = (input / maxScore) * weight when maxScore > 0, else 0.
 * totalScore is the sum of weightedPoints across all company criteria.
 */
```
- Javadoc explaining the scoring formula.
- **Formula**: `weightedPoints = (rawScore / maxScore) * weight`

```java
@Service
```
- **Spring Stereotype**: Marks this as a Spring bean to be auto-detected and injected.
- Singleton scope by default (one instance shared across the application).

```java
    public ScoreEvaluationResponse score(ScoreEvaluationRequest request) {
```
- Main method that processes the scoring request.

```java
        Map<String, Double> scores = request.getScores();
        List<ScoreLineItem> lineItems = new ArrayList<>();
        double total = 0.0;
```
- Extracts raw scores map.
- Initializes accumulator for line items and total score.

```java
        for (CriterionInput c : request.getCriteria()) {
            double input = resolveRawScore(scores, c.getId());
```
- Iterates through each criterion definition.
- `resolveRawScore()`: Safely retrieves the score for this criterion.

```java
            int max = c.getMaxScore() != null ? c.getMaxScore() : 0;
            double weighted = max > 0 ? (input / max) * c.getWeight() : 0.0;
```
- **Scoring Calculation**:
  - Handles null maxScore gracefully (defaults to 0).
  - If maxScore > 0: applies formula `(rawScore / maxScore) * weight`
  - If maxScore = 0: weighted points = 0 (prevents division by zero).

```java
            total += weighted;
            lineItems.add(new ScoreLineItem(c.getId(), input, weighted));
        }
```
- Accumulates weighted points to total.
- Creates line item with all details.

```java
        ScoreEvaluationResponse response = new ScoreEvaluationResponse();
        response.setTotalScore(total);
        response.setLineItems(lineItems);
        response.setLowScoreAlert(total < ScoreEvaluationResponse.LOW_SCORE_THRESHOLD);
        return response;
```
- Builds and returns the response.
- Sets `lowScoreAlert` flag if score is below threshold.

### Private Helper Method

```java
    private double resolveRawScore(Map<String, Double> scores, long criteriaId) {
        if (scores == null || scores.isEmpty()) {
            return 0.0;
        }
```
- Defensive programming: handles null/empty scores map.

```java
        String key = Long.toString(criteriaId);
        if (scores.containsKey(key) && scores.get(key) != null) {
            return scores.get(key);
        }
```
- Tries direct lookup using criteria ID as string key.

```java
        // tolerate numeric keys serialized differently
        for (Map.Entry<String, Double> e : scores.entrySet()) {
            if (key.equals(e.getKey())) {
                return e.getValue() != null ? e.getValue() : 0.0;
            }
        }
        return 0.0;
    }
```
- **Fallback search**: Iterates through entries if direct lookup fails.
- Handles edge cases where JSON keys might be serialized differently.
- Returns 0.0 if score not found (graceful degradation).

---

## File 8: WeightedEvaluationServiceTest.java

**Location:** `src/test/java/com/saas/evaluation/service/WeightedEvaluationServiceTest.java`

### Purpose
Unit tests for the scoring service using JUnit 5 and AssertJ.

### Code Breakdown

```java
package com.saas.evaluation.service;

import com.saas.evaluation.dto.CriterionInput;
import com.saas.evaluation.dto.ScoreEvaluationRequest;
import com.saas.evaluation.dto.ScoreEvaluationResponse;
import org.junit.jupiter.api.Test;
```
- JUnit 5 (`jupiter`) imports for modern testing.

```java
import java.util.List;
import java.util.Map;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.within;
```
- `AssertJ`: Fluent assertion library for readable tests.
- `within()`: For floating-point comparisons with tolerance.

```java
class WeightedEvaluationServiceTest {

    private final WeightedEvaluationService service = new WeightedEvaluationService();
```
- Test class (no `@ExtendWith` needed for simple unit tests).
- Direct instantiation of service (no Spring context = fast tests).

### Test 1: matchesLegacyPhpExample

```java
    @Test
    void matchesLegacyPhpExample() {
        // Setup
        CriterionInput q = new CriterionInput();
        q.setId(1L);
        q.setWeight(20.0);
        q.setMaxScore(10);

        ScoreEvaluationRequest req = new ScoreEvaluationRequest();
        req.setCriteria(List.of(q));
        req.setScores(Map.of("1", 8.0));
```
- **Arrange**: Creates criterion with weight 20, maxScore 10.
- Score input: 8.0 out of 10.

```java
        // Execute
        ScoreEvaluationResponse res = service.score(req);
```
- **Act**: Calls the scoring method.

```java
        // Assert
        assertThat(res.getTotalScore()).isCloseTo(16.0, within(0.001));
        assertThat(res.isLowScoreAlert()).isTrue();
    }
```
- **Verification**:
  - Expected: `(8/10) * 20 = 16.0` → matches legacy PHP calculation.
  - `isCloseTo`: Floating-point comparison with 0.001 tolerance.
  - Alert is true because 16.0 < 50.0 threshold.

### Test 2: sumsMultipleCriteria

```java
    @Test
    void sumsMultipleCriteria() {
        // Criterion A: 40% weight, scored 10/10
        CriterionInput a = new CriterionInput();
        a.setId(1L);
        a.setWeight(40.0);
        a.setMaxScore(10);

        // Criterion B: 60% weight, scored 5/10
        CriterionInput b = new CriterionInput();
        b.setId(2L);
        b.setWeight(60.0);
        b.setMaxScore(10);

        ScoreEvaluationRequest req = new ScoreEvaluationRequest();
        req.setCriteria(List.of(a, b));
        req.setScores(Map.of("1", 10.0, "2", 5.0));
```
- Two criteria with different weights.

```java
        ScoreEvaluationResponse res = service.score(req);

        assertThat(res.getTotalScore()).isCloseTo(70.0, within(0.001));
        assertThat(res.isLowScoreAlert()).isFalse();
    }
```
- **Calculation**:
  - A: `(10/10) * 40 = 40.0`
  - B: `(5/10) * 60 = 30.0`
  - Total: `40 + 30 = 70.0`
- No alert because 70.0 >= 50.0 threshold.

---

## Architecture Summary

```
┌─────────────────────────────────────────────────────────────┐
│                    Client (HTTP Request)                     │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              ScoringRestController (@RestController)         │
│                   POST /api/v1/evaluations/score              │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              ScoreEvaluationRequest (@Valid)               │
│  • List<CriterionInput> criteria (with weight, maxScore)     │
│  • Map<String, Double> scores (raw scores by criteria ID)   │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              WeightedEvaluationService (@Service)            │
│  Formula: weightedPoints = (rawScore / maxScore) * weight     │
│  totalScore = sum of all weightedPoints                      │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│              ScoreEvaluationResponse                         │
│  • double totalScore                                        │
│  • List<ScoreLineItem> lineItems (breakdown)                │
│  • boolean lowScoreAlert (if totalScore < 50.0)              │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│                    JSON Response to Client                   │
└─────────────────────────────────────────────────────────────┘
```

---

## Key Spring Boot Concepts Used

| Concept | Usage |
|---------|-------|
| `@SpringBootApplication` | Main application entry point |
| `@RestController` | REST API layer |
| `@RequestMapping` | Base URL path configuration |
| `@GetMapping` / `@PostMapping` | HTTP method handlers |
| `@RequestBody` | Deserialize JSON to Java object |
| `@Valid` | Trigger validation constraints |
| `@Service` | Business logic layer |
| Constructor Injection | Dependency injection pattern |
| Bean Validation (`@NotNull`, `@NotEmpty`, `@PositiveOrZero`) | Input validation |
| Jackson | JSON serialization/deserialization |

---

## Calculation Example

**Given:**
- Criterion 1: weight=30, maxScore=10, rawScore=7
- Criterion 2: weight=70, maxScore=10, rawScore=5

**Calculation:**
```
Criterion 1: (7/10) * 30 = 21.0 points
Criterion 2: (5/10) * 70 = 35.0 points
─────────────────────────────────────
Total Score: 21 + 35 = 56.0
Low Score Alert: false (56 >= 50)
```

**Response:**
```json
{
  "totalScore": 56.0,
  "lineItems": [
    {"criteriaId": 1, "rawScore": 7.0, "weightedPoints": 21.0},
    {"criteriaId": 2, "rawScore": 5.0, "weightedPoints": 35.0}
  ],
  "lowScoreAlert": false
}
```

---

## Endpoints Reference

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/health` | GET | Health check - returns `{"status": "UP"}` |
| `/api/v1/evaluations/score` | POST | Calculate weighted scores from criteria and raw scores |

---

*Generated for Presentation - SaaS Evaluation Service*
