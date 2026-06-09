-- Single POS subscription plan at $10/month
SET NAMES utf8mb4;

UPDATE `ospos_plans` SET `is_active` = 0;

INSERT INTO `ospos_plans` (`plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`)
SELECT 'pos_monthly', 'POS Subscription', 10.00, 50, 5, 50000, 1
WHERE NOT EXISTS (SELECT 1 FROM `ospos_plans` WHERE `plan_code` = 'pos_monthly');

UPDATE `ospos_plans`
SET `plan_name` = 'POS Subscription',
    `price_monthly` = 10.00,
    `max_users` = 50,
    `max_locations` = 5,
    `max_items` = 50000,
    `is_active` = 1
WHERE `plan_code` = 'pos_monthly';
