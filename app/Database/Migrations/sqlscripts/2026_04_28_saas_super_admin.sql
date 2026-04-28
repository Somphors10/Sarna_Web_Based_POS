-- ============================================================
-- OSPOS SaaS - Super Admin + Registration Support
-- ============================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_platform_admins` (
    `admin_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(64) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uk_platform_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default super admin account:
-- username: superadmin
-- password: ChangeMe123!
INSERT INTO `ospos_platform_admins` (`username`, `password_hash`, `full_name`, `email`, `status`)
SELECT 'superadmin', '$2y$12$EONUNSMLcM5qo9WEmAI9LudBTc5MCdI2tDsu0xKCx5lBkcvMSIr7S', 'Platform Super Admin', NULL, 'active'
WHERE NOT EXISTS (
    SELECT 1 FROM `ospos_platform_admins` WHERE `username` = 'superadmin'
);
