-- Email-based password reset (replaces Super Admin approval flow)

SET NAMES utf8mb4;

ALTER TABLE `ospos_password_reset_requests`
    ADD COLUMN `reset_token` VARCHAR(64) DEFAULT NULL AFTER `tenant_id`,
    ADD COLUMN `email` VARCHAR(255) DEFAULT NULL AFTER `reset_token`,
    ADD COLUMN `expires_at` DATETIME DEFAULT NULL AFTER `email`,
    ADD COLUMN `used_at` DATETIME DEFAULT NULL AFTER `expires_at`;

ALTER TABLE `ospos_password_reset_requests`
    MODIFY COLUMN `new_password_hash` VARCHAR(255) NULL,
    MODIFY COLUMN `status` ENUM('pending','approved','rejected','used','expired') NOT NULL DEFAULT 'pending';

UPDATE `ospos_password_reset_requests`
SET `status` = 'expired'
WHERE `status` IN ('pending', 'approved', 'rejected') AND `reset_token` IS NULL;

ALTER TABLE `ospos_password_reset_requests`
    ADD UNIQUE KEY `uniq_password_reset_token` (`reset_token`),
    ADD KEY `idx_password_reset_expires` (`expires_at`);
