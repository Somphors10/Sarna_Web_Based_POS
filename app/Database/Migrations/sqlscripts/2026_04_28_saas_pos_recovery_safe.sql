-- ============================================================
-- OSPOS SaaS Recovery Script (Safe / Idempotent)
-- Purpose:
-- - Continue after partial import failures (e.g. missing table errors)
-- - Add tenant_id only on tables that actually exist
-- - Avoid duplicate index/constraint errors on re-run
-- ============================================================

SET NAMES utf8mb4;

-- Ensure baseline tenant exists
INSERT INTO `ospos_tenants` (`tenant_code`, `company_name`, `status`, `timezone`, `currency_code`)
SELECT 'default', 'Default Tenant', 'active', 'UTC', 'USD'
WHERE NOT EXISTS (SELECT 1 FROM `ospos_tenants` WHERE `tenant_code` = 'default');

SET @default_tenant_id = (SELECT `tenant_id` FROM `ospos_tenants` WHERE `tenant_code` = 'default' LIMIT 1);

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_add_tenant_column_if_table_exists`$$
CREATE PROCEDURE `sp_add_tenant_column_if_table_exists`(IN p_table VARCHAR(64), IN p_index VARCHAR(64))
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = p_table
    ) THEN
        IF NOT EXISTS (
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = p_table AND column_name = 'tenant_id'
        ) THEN
            SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `tenant_id` BIGINT UNSIGNED NULL');
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;

        IF NOT EXISTS (
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE() AND table_name = p_table AND index_name = p_index
        ) THEN
            SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index, '` (`tenant_id`)');
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_backfill_tenant_id_if_table_exists`$$
CREATE PROCEDURE `sp_backfill_tenant_id_if_table_exists`(IN p_table VARCHAR(64))
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = p_table AND column_name = 'tenant_id'
    ) THEN
        SET @sql = CONCAT('UPDATE `', p_table, '` SET `tenant_id` = ', @default_tenant_id, ' WHERE `tenant_id` IS NULL');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_make_tenant_not_null_if_clean`$$
CREATE PROCEDURE `sp_make_tenant_not_null_if_clean`(IN p_table VARCHAR(64))
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = p_table
          AND column_name = 'tenant_id'
          AND is_nullable = 'YES'
    ) THEN
        SET @sql = CONCAT('SELECT COUNT(*) INTO @tenant_nulls FROM `', p_table, '` WHERE `tenant_id` IS NULL');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        IF IFNULL(@tenant_nulls, 0) = 0 THEN
            SET @sql = CONCAT('ALTER TABLE `', p_table, '` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL');
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_add_fk_if_possible`$$
CREATE PROCEDURE `sp_add_fk_if_possible`(
    IN p_table VARCHAR(64),
    IN p_constraint VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_ref_table VARCHAR(64),
    IN p_ref_column VARCHAR(64)
)
BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = p_table)
       AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = p_ref_table)
       AND EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = p_table AND column_name = p_column)
       AND EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = p_ref_table AND column_name = p_ref_column)
       AND NOT EXISTS (
           SELECT 1
           FROM information_schema.table_constraints
           WHERE table_schema = DATABASE() AND table_name = p_table AND constraint_name = p_constraint AND constraint_type = 'FOREIGN KEY'
       ) THEN
        SET @sql = CONCAT(
            'ALTER TABLE `', p_table, '` ADD CONSTRAINT `', p_constraint,
            '` FOREIGN KEY (`', p_column, '`) REFERENCES `', p_ref_table, '` (`', p_ref_column, '`)'
        );
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_add_index_if_missing`$$
CREATE PROCEDURE `sp_add_index_if_missing`(IN p_table VARCHAR(64), IN p_index VARCHAR(64), IN p_columns VARCHAR(255))
BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = p_table)
       AND NOT EXISTS (
           SELECT 1
           FROM information_schema.statistics
           WHERE table_schema = DATABASE() AND table_name = p_table AND index_name = p_index
       ) THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index, '` (', p_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- ------------------------------------------------------------
-- Add tenant_id + simple index (if table exists)
-- ------------------------------------------------------------
CALL sp_add_tenant_column_if_table_exists('ospos_people', 'idx_people_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_employees', 'idx_employees_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_customers', 'idx_customers_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_suppliers', 'idx_suppliers_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_stock_locations', 'idx_locations_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_items', 'idx_items_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_item_kits', 'idx_item_kits_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_item_kit_items', 'idx_item_kit_items_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_item_quantities', 'idx_item_quantities_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_items_taxes', 'idx_items_taxes_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_giftcards', 'idx_giftcards_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_inventory', 'idx_inventory_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_receivings', 'idx_receivings_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_receivings_items', 'idx_receivings_items_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales', 'idx_sales_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_items', 'idx_sales_items_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_items_taxes', 'idx_sales_items_taxes_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_payments', 'idx_sales_payments_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_suspended', 'idx_sales_suspended_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_suspended_items', 'idx_sales_suspended_items_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_suspended_items_taxes', 'idx_sales_suspended_items_taxes_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_suspended_payments', 'idx_sales_suspended_payments_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_sales_taxes', 'idx_sales_taxes_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_expenses', 'idx_expenses_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_expense_categories', 'idx_expense_categories_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_cash_up', 'idx_cash_up_tenant_id');
CALL sp_add_tenant_column_if_table_exists('ospos_cashups', 'idx_cashups_tenant_id');

-- ------------------------------------------------------------
-- Backfill tenant_id (if table/column exists)
-- ------------------------------------------------------------
CALL sp_backfill_tenant_id_if_table_exists('ospos_people');
CALL sp_backfill_tenant_id_if_table_exists('ospos_employees');
CALL sp_backfill_tenant_id_if_table_exists('ospos_customers');
CALL sp_backfill_tenant_id_if_table_exists('ospos_suppliers');
CALL sp_backfill_tenant_id_if_table_exists('ospos_stock_locations');
CALL sp_backfill_tenant_id_if_table_exists('ospos_items');
CALL sp_backfill_tenant_id_if_table_exists('ospos_item_kits');
CALL sp_backfill_tenant_id_if_table_exists('ospos_item_kit_items');
CALL sp_backfill_tenant_id_if_table_exists('ospos_item_quantities');
CALL sp_backfill_tenant_id_if_table_exists('ospos_items_taxes');
CALL sp_backfill_tenant_id_if_table_exists('ospos_giftcards');
CALL sp_backfill_tenant_id_if_table_exists('ospos_inventory');
CALL sp_backfill_tenant_id_if_table_exists('ospos_receivings');
CALL sp_backfill_tenant_id_if_table_exists('ospos_receivings_items');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_items');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_items_taxes');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_payments');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_suspended');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_suspended_items');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_suspended_items_taxes');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_suspended_payments');
CALL sp_backfill_tenant_id_if_table_exists('ospos_sales_taxes');
CALL sp_backfill_tenant_id_if_table_exists('ospos_expenses');
CALL sp_backfill_tenant_id_if_table_exists('ospos_expense_categories');
CALL sp_backfill_tenant_id_if_table_exists('ospos_cash_up');
CALL sp_backfill_tenant_id_if_table_exists('ospos_cashups');

-- ------------------------------------------------------------
-- Make tenant_id NOT NULL only when safe
-- ------------------------------------------------------------
CALL sp_make_tenant_not_null_if_clean('ospos_people');
CALL sp_make_tenant_not_null_if_clean('ospos_employees');
CALL sp_make_tenant_not_null_if_clean('ospos_customers');
CALL sp_make_tenant_not_null_if_clean('ospos_suppliers');
CALL sp_make_tenant_not_null_if_clean('ospos_stock_locations');
CALL sp_make_tenant_not_null_if_clean('ospos_items');
CALL sp_make_tenant_not_null_if_clean('ospos_item_kits');
CALL sp_make_tenant_not_null_if_clean('ospos_item_kit_items');
CALL sp_make_tenant_not_null_if_clean('ospos_item_quantities');
CALL sp_make_tenant_not_null_if_clean('ospos_items_taxes');
CALL sp_make_tenant_not_null_if_clean('ospos_giftcards');
CALL sp_make_tenant_not_null_if_clean('ospos_inventory');
CALL sp_make_tenant_not_null_if_clean('ospos_receivings');
CALL sp_make_tenant_not_null_if_clean('ospos_receivings_items');
CALL sp_make_tenant_not_null_if_clean('ospos_sales');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_items');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_items_taxes');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_payments');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_suspended');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_suspended_items');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_suspended_items_taxes');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_suspended_payments');
CALL sp_make_tenant_not_null_if_clean('ospos_sales_taxes');
CALL sp_make_tenant_not_null_if_clean('ospos_expenses');
CALL sp_make_tenant_not_null_if_clean('ospos_expense_categories');
CALL sp_make_tenant_not_null_if_clean('ospos_cash_up');
CALL sp_make_tenant_not_null_if_clean('ospos_cashups');

-- ------------------------------------------------------------
-- Add tenant foreign keys where possible
-- ------------------------------------------------------------
CALL sp_add_fk_if_possible('ospos_people', 'fk_people_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_stock_locations', 'fk_stock_locations_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_items', 'fk_items_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_item_kits', 'fk_item_kits_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_customers', 'fk_customers_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_suppliers', 'fk_suppliers_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_employees', 'fk_employees_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_sales', 'fk_sales_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');
CALL sp_add_fk_if_possible('ospos_receivings', 'fk_receivings_tenant', 'tenant_id', 'ospos_tenants', 'tenant_id');

-- ------------------------------------------------------------
-- Helpful composite indexes where possible
-- ------------------------------------------------------------
CALL sp_add_index_if_missing('ospos_sales', 'idx_sales_tenant_date', '`tenant_id`, `sale_time`');
CALL sp_add_index_if_missing('ospos_receivings', 'idx_receivings_tenant_date', '`tenant_id`, `receiving_time`');
CALL sp_add_index_if_missing('ospos_items', 'idx_items_tenant_deleted', '`tenant_id`, `deleted`');
CALL sp_add_index_if_missing('ospos_customers', 'idx_customers_tenant_deleted', '`tenant_id`, `deleted`');
CALL sp_add_index_if_missing('ospos_suppliers', 'idx_suppliers_tenant_deleted', '`tenant_id`, `deleted`');

-- Cleanup helper procedures
DROP PROCEDURE IF EXISTS `sp_add_tenant_column_if_table_exists`;
DROP PROCEDURE IF EXISTS `sp_backfill_tenant_id_if_table_exists`;
DROP PROCEDURE IF EXISTS `sp_make_tenant_not_null_if_clean`;
DROP PROCEDURE IF EXISTS `sp_add_fk_if_possible`;
DROP PROCEDURE IF EXISTS `sp_add_index_if_missing`;

-- ============================================================
-- End
-- ============================================================
