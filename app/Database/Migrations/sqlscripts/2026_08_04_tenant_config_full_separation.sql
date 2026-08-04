-- Full per-tenant config: copy missing keys from app_config into tenant_config.
-- Safe to re-run. Does not overwrite existing tenant values.

INSERT INTO `ospos_tenant_config` (`tenant_id`, `config_key`, `config_value`)
SELECT t.`tenant_id`, ac.`key`, ac.`value`
FROM `ospos_tenants` t
CROSS JOIN `ospos_app_config` ac
WHERE NOT EXISTS (
    SELECT 1
    FROM `ospos_tenant_config` tc
    WHERE tc.`tenant_id` = t.`tenant_id`
      AND tc.`config_key` = ac.`key`
);
