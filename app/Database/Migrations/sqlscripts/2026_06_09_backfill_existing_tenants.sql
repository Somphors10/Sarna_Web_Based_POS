-- Backfill existing tenants with missing config and reference data cloned from tenant 1.
-- Safe to re-run.

-- 1) Merge missing config keys (keeps each tenant's own company/timezone/currency values)
INSERT INTO `ospos_tenant_config` (`tenant_id`, `config_key`, `config_value`)
SELECT t.`tenant_id`, src.`config_key`, src.`config_value`
FROM `ospos_tenants` t
CROSS JOIN `ospos_tenant_config` src
WHERE src.`tenant_id` = 1
  AND t.`tenant_id` > 1
  AND NOT EXISTS (
      SELECT 1
      FROM `ospos_tenant_config` existing
      WHERE existing.`tenant_id` = t.`tenant_id`
        AND existing.`config_key` = src.`config_key`
  );

-- 2) Stock locations
INSERT INTO `ospos_stock_locations` (`location_name`, `deleted`, `tenant_id`)
SELECT sl.`location_name`, sl.`deleted`, t.`tenant_id`
FROM `ospos_stock_locations` sl
CROSS JOIN `ospos_tenants` t
WHERE sl.`tenant_id` = 1
  AND t.`tenant_id` > 1
  AND NOT EXISTS (
      SELECT 1 FROM `ospos_stock_locations` x WHERE x.`tenant_id` = t.`tenant_id`
  );

-- 3) Dinner tables
INSERT INTO `ospos_dinner_tables` (`name`, `status`, `deleted`, `tenant_id`)
SELECT dt.`name`, dt.`status`, dt.`deleted`, t.`tenant_id`
FROM `ospos_dinner_tables` dt
CROSS JOIN `ospos_tenants` t
WHERE dt.`tenant_id` = 1
  AND t.`tenant_id` > 1
  AND NOT EXISTS (
      SELECT 1 FROM `ospos_dinner_tables` x WHERE x.`tenant_id` = t.`tenant_id`
  );

-- 4) Reward packages
INSERT INTO `ospos_customers_packages` (`package_name`, `points_percent`, `deleted`, `tenant_id`)
SELECT cp.`package_name`, cp.`points_percent`, cp.`deleted`, t.`tenant_id`
FROM `ospos_customers_packages` cp
CROSS JOIN `ospos_tenants` t
WHERE cp.`tenant_id` = 1
  AND t.`tenant_id` > 1
  AND NOT EXISTS (
      SELECT 1 FROM `ospos_customers_packages` x WHERE x.`tenant_id` = t.`tenant_id`
  );
