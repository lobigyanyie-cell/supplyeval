-- Pending invite flag for team members who must set password via email link. Run once on existing DBs.

ALTER TABLE users
    ADD COLUMN invite_pending TINYINT(1) NOT NULL DEFAULT 0 AFTER role;
