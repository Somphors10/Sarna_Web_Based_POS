-- ============================================================
-- OSPOS SaaS Website Signup Flow
-- ============================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_subscription_requests` (
    `request_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name` VARCHAR(255) NOT NULL,
    `tenant_code` VARCHAR(64) NOT NULL,
    `owner_first_name` VARCHAR(100) NOT NULL,
    `owner_last_name` VARCHAR(100) NOT NULL,
    `owner_email` VARCHAR(255) NOT NULL,
    `owner_phone` VARCHAR(255) DEFAULT NULL,
    `owner_username` VARCHAR(50) NOT NULL,
    `owner_password_hash` VARCHAR(255) NOT NULL,
    `plan_id` BIGINT UNSIGNED NOT NULL,
    `payment_reference` VARCHAR(100) NOT NULL,
    `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `reviewed_by_admin_id` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`request_id`),
    KEY `idx_subscription_requests_status` (`status`),
    KEY `idx_subscription_requests_plan` (`plan_id`),
    KEY `idx_subscription_requests_code` (`tenant_code`),
    CONSTRAINT `fk_subscription_requests_plan` FOREIGN KEY (`plan_id`) REFERENCES `ospos_plans` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure there is at least one plan available on signup page
INSERT INTO `ospos_plans` (`plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`)
SELECT 'basic', 'Basic', 19.00, 5, 1, 5000, 1
WHERE NOT EXISTS (SELECT 1 FROM `ospos_plans` WHERE `plan_code` = 'basic');
