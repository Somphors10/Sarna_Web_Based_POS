-- Add tenant_id to tables that were still shared across companies.
-- Safe to re-run: checks column existence via information_schema patterns.

SET @default_tenant_id = (
    SELECT `tenant_id` FROM `ospos_tenants` WHERE `tenant_code` = 'default' LIMIT 1
);
SET @default_tenant_id = IFNULL(@default_tenant_id, 1);

-- dinner_tables
SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ospos_dinner_tables' AND COLUMN_NAME = 'tenant_id') = 0,
    'ALTER TABLE `ospos_dinner_tables` ADD COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`, ADD INDEX `idx_dinner_tables_tenant` (`tenant_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE `ospos_dinner_tables` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` = 0 OR `tenant_id` IS NULL;
