-- Evaluation workflow (draft/submit) + per-evaluation audit trail.
-- Run once on existing databases (after schema is applied).

ALTER TABLE evaluations
    ADD COLUMN status ENUM('draft', 'submitted') NOT NULL DEFAULT 'submitted' AFTER comments;

-- Redundant if DEFAULT applied to existing rows; uses primary key for safe-update mode (Workbench).
UPDATE evaluations SET status = 'submitted' WHERE id > 0 AND (status IS NULL OR status = '');

CREATE TABLE IF NOT EXISTS evaluation_workflow_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    company_id INT NOT NULL,
    user_id INT NULL,
    action VARCHAR(64) NOT NULL,
    meta TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ewe_eval (evaluation_id),
    INDEX idx_ewe_company (company_id),
    CONSTRAINT fk_ewe_eval FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
    CONSTRAINT fk_ewe_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_ewe_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
