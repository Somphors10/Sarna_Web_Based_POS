-- Update subscription price to $20/month
SET NAMES utf8mb4;

INSERT INTO `wbpos_plans` (`plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`, `created_at`)
SELECT 'pos_monthly', 'POS Subscription', 20.00, 50, 5, 50000, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `wbpos_plans` WHERE `plan_code` = 'pos_monthly');

UPDATE `wbpos_plans`
SET `plan_name` = 'POS Subscription',
    `price_monthly` = 20.00,
    `max_users` = 50,
    `max_locations` = 5,
    `max_items` = 50000,
    `is_active` = 1
WHERE `plan_code` = 'pos_monthly';

UPDATE `wbpos_plans`
SET `price_monthly` = 20.00
WHERE `plan_code` = 'basic';
