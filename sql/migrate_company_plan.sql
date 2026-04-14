-- Company subscription tier (landing / registration). Run once on existing DBs.

ALTER TABLE companies
    ADD COLUMN plan ENUM('starter', 'professional', 'enterprise') NOT NULL DEFAULT 'professional' AFTER subscription_status;

UPDATE companies SET plan = 'professional' WHERE plan IS NULL OR plan = '';
