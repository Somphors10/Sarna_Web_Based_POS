-- ============================================================
-- OSPOS SaaS POS Foundation Schema (Multi-tenant)
-- Target: MySQL/MariaDB with InnoDB
-- ============================================================
-- IMPORTANT:
-- 1) Back up your database before running.
-- 2) Run on staging first.
-- 3) This script prepares DB structure; application code must also
--    enforce tenant scoping in every query.
-- ============================================================

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- 1) SaaS Core Tables
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ospos_tenants` (
    `tenant_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_code` VARCHAR(64) NOT NULL,
    `company_name` VARCHAR(255) NOT NULL,
    `status` ENUM('active', 'suspended', 'cancelled') NOT NULL DEFAULT 'active',
    `timezone` VARCHAR(64) NOT NULL DEFAULT 'UTC',
    `currency_code` CHAR(3) NOT NULL DEFAULT 'USD',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`tenant_id`),
    UNIQUE KEY `uk_tenants_code` (`tenant_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_plans` (
    `plan_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `plan_code` VARCHAR(64) NOT NULL,
    `plan_name` VARCHAR(120) NOT NULL,
    `price_monthly` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `max_users` INT UNSIGNED NOT NULL DEFAULT 5,
    `max_locations` INT UNSIGNED NOT NULL DEFAULT 1,
    `max_items` INT UNSIGNED NOT NULL DEFAULT 5000,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`plan_id`),
    UNIQUE KEY `uk_plans_code` (`plan_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_subscriptions` (
    `subscription_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `plan_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('trialing', 'active', 'past_due', 'cancelled') NOT NULL DEFAULT 'trialing',
    `trial_ends_at` DATETIME NULL,
    `period_start` DATETIME NOT NULL,
    `period_end` DATETIME NOT NULL,
    `cancel_at_period_end` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`subscription_id`),
    KEY `idx_subscriptions_tenant_status` (`tenant_id`, `status`),
    KEY `idx_subscriptions_plan` (`plan_id`),
    CONSTRAINT `fk_subscriptions_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
    CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `ospos_plans` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_tenant_domains` (
    `domain_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `domain` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`domain_id`),
    UNIQUE KEY `uk_tenant_domains_domain` (`domain`),
    KEY `idx_tenant_domains_tenant` (`tenant_id`),
    CONSTRAINT `fk_tenant_domains_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_tenant_users` (
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `person_id` INT(10) NOT NULL,
    `tenant_role` ENUM('owner', 'admin', 'manager', 'cashier') NOT NULL DEFAULT 'cashier',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tenant_id`, `person_id`),
    KEY `idx_tenant_users_person` (`person_id`),
    CONSTRAINT `fk_tenant_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
    CONSTRAINT `fk_tenant_users_person` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_tenant_config` (
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` TEXT NULL,
    PRIMARY KEY (`tenant_id`, `config_key`),
    CONSTRAINT `fk_tenant_config_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_invoices` (
    `invoice_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `subscription_id` BIGINT UNSIGNED NOT NULL,
    `invoice_number` VARCHAR(64) NOT NULL,
    `currency_code` CHAR(3) NOT NULL DEFAULT 'USD',
    `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('draft', 'open', 'paid', 'void') NOT NULL DEFAULT 'open',
    `due_date` DATE NULL,
    `paid_at` DATETIME NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`invoice_id`),
    UNIQUE KEY `uk_invoices_tenant_number` (`tenant_id`, `invoice_number`),
    KEY `idx_invoices_subscription` (`subscription_id`),
    KEY `idx_invoices_tenant_status` (`tenant_id`, `status`),
    CONSTRAINT `fk_invoices_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
    CONSTRAINT `fk_invoices_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `ospos_subscriptions` (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ospos_invoice_payments` (
    `payment_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` BIGINT UNSIGNED NOT NULL,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `provider` VARCHAR(40) NOT NULL,
    `provider_payment_id` VARCHAR(191) NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `paid_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`payment_id`),
    KEY `idx_invoice_payments_tenant` (`tenant_id`),
    KEY `idx_invoice_payments_invoice` (`invoice_id`),
    KEY `idx_invoice_payments_provider_ref` (`provider`, `provider_payment_id`),
    CONSTRAINT `fk_invoice_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
    CONSTRAINT `fk_invoice_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `ospos_invoices` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2) Seed baseline plan + baseline tenant (for existing data)
-- ------------------------------------------------------------

INSERT INTO `ospos_plans` (`plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`)
SELECT 'starter', 'Starter', 0.00, 5, 1, 5000, 1
WHERE NOT EXISTS (SELECT 1 FROM `ospos_plans` WHERE `plan_code` = 'starter');

INSERT INTO `ospos_tenants` (`tenant_code`, `company_name`, `status`, `timezone`, `currency_code`)
SELECT 'default', 'Default Tenant', 'active', 'UTC', 'USD'
WHERE NOT EXISTS (SELECT 1 FROM `ospos_tenants` WHERE `tenant_code` = 'default');

INSERT INTO `ospos_subscriptions` (`tenant_id`, `plan_id`, `status`, `trial_ends_at`, `period_start`, `period_end`, `cancel_at_period_end`)
SELECT
    t.tenant_id,
    p.plan_id,
    'active',
    NULL,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 1 MONTH),
    0
FROM `ospos_tenants` t
JOIN `ospos_plans` p ON p.plan_code = 'starter'
WHERE t.tenant_code = 'default'
AND NOT EXISTS (
    SELECT 1
    FROM `ospos_subscriptions` s
    WHERE s.tenant_id = t.tenant_id
);

-- ------------------------------------------------------------
-- 3) Add tenant_id to core OSPOS tables
-- ------------------------------------------------------------
-- NOTE: application code must be updated to always filter by tenant_id.

ALTER TABLE `ospos_people` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_people` ADD INDEX `idx_people_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_employees` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_employees` ADD INDEX `idx_employees_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_customers` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_customers` ADD INDEX `idx_customers_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_suppliers` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_suppliers` ADD INDEX `idx_suppliers_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_stock_locations` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_stock_locations` ADD INDEX `idx_locations_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_items` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_items` ADD INDEX `idx_items_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_item_kits` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_item_kits` ADD INDEX `idx_item_kits_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_item_kit_items` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_item_kit_items` ADD INDEX `idx_item_kit_items_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_items_taxes` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_items_taxes` ADD INDEX `idx_items_taxes_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_giftcards` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_giftcards` ADD INDEX `idx_giftcards_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_inventory` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_inventory` ADD INDEX `idx_inventory_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_receivings` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_receivings` ADD INDEX `idx_receivings_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_receivings_items` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_receivings_items` ADD INDEX `idx_receivings_items_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales` ADD INDEX `idx_sales_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_items` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_items` ADD INDEX `idx_sales_items_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_items_taxes` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_items_taxes` ADD INDEX `idx_sales_items_taxes_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_payments` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_payments` ADD INDEX `idx_sales_payments_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_suspended` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_suspended` ADD INDEX `idx_sales_suspended_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_suspended_items` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_suspended_items` ADD INDEX `idx_sales_suspended_items_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_suspended_items_taxes` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_suspended_items_taxes` ADD INDEX `idx_sales_suspended_items_taxes_tenant_id` (`tenant_id`);

ALTER TABLE `ospos_sales_suspended_payments` ADD COLUMN IF NOT EXISTS `tenant_id` BIGINT UNSIGNED NULL;
ALTER TABLE `ospos_sales_suspended_payments` ADD INDEX `idx_sales_suspended_payments_tenant_id` (`tenant_id`);

-- ------------------------------------------------------------
-- 4) Backfill tenant_id using the default tenant
-- ------------------------------------------------------------

SET @default_tenant_id = (SELECT `tenant_id` FROM `ospos_tenants` WHERE `tenant_code` = 'default' LIMIT 1);

UPDATE `ospos_people` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_employees` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_customers` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_suppliers` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_stock_locations` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_items` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_item_kits` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_item_kit_items` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_items_taxes` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_giftcards` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_inventory` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_receivings` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_receivings_items` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_items` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_items_taxes` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_payments` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_suspended` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_suspended_items` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_suspended_items_taxes` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;
UPDATE `ospos_sales_suspended_payments` SET `tenant_id` = @default_tenant_id WHERE `tenant_id` IS NULL;

-- ------------------------------------------------------------
-- 5) Make tenant_id required (only after successful backfill)
-- ------------------------------------------------------------

ALTER TABLE `ospos_people` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_employees` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_customers` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_suppliers` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_stock_locations` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_items` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_item_kits` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_item_kit_items` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_items_taxes` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_giftcards` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_inventory` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_receivings` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_receivings_items` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_items` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_items_taxes` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_payments` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_suspended` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_suspended_items` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_suspended_items_taxes` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `ospos_sales_suspended_payments` MODIFY COLUMN `tenant_id` BIGINT UNSIGNED NOT NULL;

-- ------------------------------------------------------------
-- 6) Tenant foreign keys (start with parent tables)
-- ------------------------------------------------------------

ALTER TABLE `ospos_people`
    ADD CONSTRAINT `fk_people_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_stock_locations`
    ADD CONSTRAINT `fk_stock_locations_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_items`
    ADD CONSTRAINT `fk_items_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_item_kits`
    ADD CONSTRAINT `fk_item_kits_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_customers`
    ADD CONSTRAINT `fk_customers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_suppliers`
    ADD CONSTRAINT `fk_suppliers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_employees`
    ADD CONSTRAINT `fk_employees_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_sales`
    ADD CONSTRAINT `fk_sales_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

ALTER TABLE `ospos_receivings`
    ADD CONSTRAINT `fk_receivings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

-- ------------------------------------------------------------
-- 7) Helpful composite indexes for tenant filtering
-- ------------------------------------------------------------

CREATE INDEX `idx_sales_tenant_date` ON `ospos_sales` (`tenant_id`, `sale_time`);
CREATE INDEX `idx_receivings_tenant_date` ON `ospos_receivings` (`tenant_id`, `receiving_time`);
CREATE INDEX `idx_items_tenant_deleted` ON `ospos_items` (`tenant_id`, `deleted`);
CREATE INDEX `idx_customers_tenant_deleted` ON `ospos_customers` (`tenant_id`, `deleted`);
CREATE INDEX `idx_suppliers_tenant_deleted` ON `ospos_suppliers` (`tenant_id`, `deleted`);

-- ============================================================
-- End of SaaS foundation schema
-- ============================================================
