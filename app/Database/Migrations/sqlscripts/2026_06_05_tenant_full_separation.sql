-- Full tenant separation: add tenant_id to remaining shared tables.
-- Safe to re-run.

SET @default_tenant_id = (
    SELECT `tenant_id` FROM `ospos_tenants` WHERE `tenant_code` = 'default' LIMIT 1
);
SET @default_tenant_id = IFNULL(@default_tenant_id, 1);

-- Helper: add tenant_id column if missing
-- tax_jurisdictions
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_tax_jurisdictions' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_tax_jurisdictions` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_tax_jurisdictions_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_tax_jurisdictions` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- tax_categories
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_tax_categories' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_tax_categories` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_tax_categories_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_tax_categories` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- tax_codes
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_tax_codes' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_tax_codes` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_tax_codes_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_tax_codes` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- tax_rates
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_tax_rates' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_tax_rates` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1, ADD INDEX `idx_tax_rates_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_tax_rates` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- customers_packages (rewards)
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_customers_packages' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_customers_packages` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_customers_packages_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_customers_packages` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- sales_reward_points
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_sales_reward_points' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_sales_reward_points` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1, ADD INDEX `idx_sales_reward_points_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_sales_reward_points` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- dinner_tables
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_dinner_tables' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_dinner_tables` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_dinner_tables_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_dinner_tables` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- stock_locations (if missing)
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_stock_locations' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_stock_locations` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_stock_locations_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_stock_locations` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;

-- Seed tenant_config from app_config for tenants that have no config yet
INSERT INTO `ospos_tenant_config` (`tenant_id`, `config_key`, `config_value`)
SELECT t.`tenant_id`, ac.`key`, ac.`value`
FROM `ospos_tenants` t
CROSS JOIN `ospos_app_config` ac
WHERE NOT EXISTS (
    SELECT 1 FROM `ospos_tenant_config` tc
    WHERE tc.`tenant_id` = t.`tenant_id`
    LIMIT 1
);
