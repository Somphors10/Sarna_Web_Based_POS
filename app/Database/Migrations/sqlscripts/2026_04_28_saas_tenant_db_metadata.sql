/*
 * SaaS tenant database metadata (control-plane)
 *
 * This migration adds optional tenant DB connection columns to ospos_tenants.
 * Keep values NULL to continue using shared DB mode.
 * Fill these columns per tenant to enable database-per-tenant routing.
 */

ALTER TABLE `ospos_tenants`
    ADD COLUMN IF NOT EXISTS `db_hostname` VARCHAR(190) NULL AFTER `currency_code`,
    ADD COLUMN IF NOT EXISTS `db_port` INT NULL AFTER `db_hostname`,
    ADD COLUMN IF NOT EXISTS `db_name` VARCHAR(190) NULL AFTER `db_port`,
    ADD COLUMN IF NOT EXISTS `db_username` VARCHAR(190) NULL AFTER `db_name`,
    ADD COLUMN IF NOT EXISTS `db_password` VARCHAR(255) NULL AFTER `db_username`,
    ADD COLUMN IF NOT EXISTS `db_prefix` VARCHAR(50) NULL AFTER `db_password`;

