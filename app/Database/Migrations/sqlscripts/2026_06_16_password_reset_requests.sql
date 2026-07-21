-- Password reset requests (POS login forgot password flow)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_password_reset_requests` (
    `request_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_code` VARCHAR(64) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `person_id` INT UNSIGNED DEFAULT NULL,
    `tenant_id` INT UNSIGNED DEFAULT NULL,
    `new_password_hash` VARCHAR(255) NOT NULL,
    `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `reviewed_by_admin_id` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`request_id`),
    KEY `idx_password_reset_status` (`status`),
    KEY `idx_password_reset_user` (`tenant_code`, `username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
