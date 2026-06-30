-- ============================================================
-- WBPOS clean install reset (tenant 1 only, admin user)
-- Run AFTER wbpos.sql on a fresh import.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `wbpos_sessions`;
TRUNCATE TABLE `wbpos_sales_payments`;
TRUNCATE TABLE `wbpos_sales_items_taxes`;
TRUNCATE TABLE `wbpos_sales_taxes`;
TRUNCATE TABLE `wbpos_sales_items`;
TRUNCATE TABLE `wbpos_sales_reward_points`;
TRUNCATE TABLE `wbpos_sales`;
TRUNCATE TABLE `wbpos_receivings_items`;
TRUNCATE TABLE `wbpos_receivings`;
TRUNCATE TABLE `wbpos_invoice_payments`;
TRUNCATE TABLE `wbpos_invoices`;
TRUNCATE TABLE `wbpos_inventory`;
TRUNCATE TABLE `wbpos_giftcards`;
TRUNCATE TABLE `wbpos_cash_up`;
TRUNCATE TABLE `wbpos_attribute_values`;
TRUNCATE TABLE `wbpos_attribute_links`;
TRUNCATE TABLE `wbpos_attribute_definitions`;
TRUNCATE TABLE `wbpos_item_kit_items`;
TRUNCATE TABLE `wbpos_item_kits`;
TRUNCATE TABLE `wbpos_item_quantities`;
TRUNCATE TABLE `wbpos_items_taxes`;
TRUNCATE TABLE `wbpos_items`;
TRUNCATE TABLE `wbpos_customers_points`;
TRUNCATE TABLE `wbpos_customers`;
TRUNCATE TABLE `wbpos_suppliers`;
TRUNCATE TABLE `wbpos_grants`;
TRUNCATE TABLE `wbpos_employees`;
TRUNCATE TABLE `wbpos_people`;
TRUNCATE TABLE `wbpos_tenant_users`;
TRUNCATE TABLE `wbpos_tenant_config`;
TRUNCATE TABLE `wbpos_tenant_domains`;
TRUNCATE TABLE `wbpos_subscription_requests`;
TRUNCATE TABLE `wbpos_subscriptions`;
TRUNCATE TABLE `wbpos_tenants`;
TRUNCATE TABLE `wbpos_dinner_tables`;
TRUNCATE TABLE `wbpos_customers_packages`;
TRUNCATE TABLE `wbpos_stock_locations`;
TRUNCATE TABLE `wbpos_tax_rates`;
TRUNCATE TABLE `wbpos_tax_jurisdictions`;
TRUNCATE TABLE `wbpos_tax_codes`;
TRUNCATE TABLE `wbpos_tax_categories`;
TRUNCATE TABLE `wbpos_invoices`;
TRUNCATE TABLE `wbpos_invoice_payments`;

DELETE FROM `wbpos_app_config`;

SET FOREIGN_KEY_CHECKS = 1;

-- App config (matches current code defaults + WBPOS branding)
INSERT INTO `wbpos_app_config` (`key`, `value`) VALUES
('account_number', ''),
('address', '100 Main Street'),
('allow_duplicate_barcodes', '0'),
('barcode_content', 'id'),
('barcode_first_row', 'category'),
('barcode_font', 'Arial'),
('barcode_font_size', '10'),
('barcode_formats', '[]'),
('barcode_generate_if_empty', '0'),
('barcode_height', '50'),
('barcode_num_in_row', '2'),
('barcode_page_cellspacing', '20'),
('barcode_page_width', '100'),
('barcode_second_row', 'item_code'),
('barcode_third_row', 'unit_price'),
('barcode_type', 'C39'),
('barcode_width', '250'),
('cash_decimals', '2'),
('cash_rounding_code', '0'),
('category_dropdown', ''),
('company', 'WBPOS Demo Store'),
('company_logo', ''),
('country_codes', 'us'),
('currency_code', ''),
('currency_decimals', '2'),
('currency_symbol', '$'),
('customer_reward_enable', '0'),
('dateformat', 'm/d/Y'),
('date_or_time_format', ''),
('default_receivings_discount', '0'),
('default_receivings_discount_type', '0'),
('default_register_mode', 'sale'),
('default_sales_discount', '0'),
('default_sales_discount_type', '0'),
('default_tax_1_name', 'Sales Tax'),
('default_tax_1_rate', '8'),
('default_tax_2_name', ''),
('default_tax_2_rate', ''),
('default_tax_category', 'Standard'),
('default_tax_code', ''),
('default_tax_jurisdiction', ''),
('default_tax_rate', '8'),
('derive_sale_quantity', '0'),
('dinner_table_enable', '0'),
('email', 'admin@wbpos.demo'),
('email_receipt_check_behaviour', 'last'),
('enforce_privacy', '0'),
('fax', ''),
('financial_year', '1'),
('gcaptcha_enable', '0'),
('gcaptcha_secret_key', ''),
('gcaptcha_site_key', ''),
('giftcard_number', 'series'),
('image_allowed_types', 'gif,jpg,png'),
('image_max_height', '1280'),
('image_max_size', '2048'),
('image_max_width', '1280'),
('include_hsn', '0'),
('invoice_default_comments', 'Thank you for your business.'),
('invoice_email_message', 'Dear {CU}, In attachment the receipt for sale {ISEQ}'),
('invoice_enable', '1'),
('invoice_type', 'invoice'),
('language', 'english'),
('language_code', 'en'),
('last_used_invoice_number', '0'),
('last_used_quote_number', '0'),
('last_used_work_order_number', '0'),
('lines_per_page', '10'),
('line_sequence', '0'),
('login_form', ''),
('mailpath', '/usr/sbin/sendmail'),
('msg_msg', ''),
('msg_pwd', ''),
('msg_src', ''),
('msg_uid', ''),
('multi_pack_enabled', '0'),
('notify_horizontal_position', 'center'),
('notify_vertical_position', 'bottom'),
('number_locale', 'en_US'),
('payment_message', ''),
('payment_options_order', 'cashdebitcredit'),
('phone', '555-555-0100'),
('print_bottom_margin', '0'),
('print_delay_autoreturn', '0'),
('print_footer', '0'),
('print_header', '0'),
('print_left_margin', '0'),
('print_receipt_check_behaviour', 'last'),
('print_right_margin', '0'),
('print_silently', '1'),
('print_top_margin', '0'),
('protocol', 'mail'),
('quantity_decimals', '0'),
('quote_default_comments', 'This is a default quote comment'),
('receipt_font_size', '12'),
('receipt_show_company_name', '1'),
('receipt_show_description', '1'),
('receipt_show_serialnumber', '1'),
('receipt_show_taxes', '0'),
('receipt_show_tax_ind', '0'),
('receipt_show_total_discount', '1'),
('receipt_template', 'receipt_default'),
('receiving_calculate_average_price', ''),
('recv_invoice_format', '{CO}'),
('return_policy', 'Returns within 30 days with receipt.'),
('sales_invoice_format', '{CO}'),
('sales_quote_format', 'Q%y{QSEQ:6}'),
('show_office_group', '1'),
('smtp_crypto', 'ssl'),
('smtp_host', ''),
('smtp_pass', ''),
('smtp_port', '465'),
('smtp_timeout', '5'),
('smtp_user', ''),
('statistics', '1'),
('suggestions_first_column', 'name'),
('suggestions_second_column', ''),
('suggestions_third_column', ''),
('tax_decimals', '2'),
('tax_id', ''),
('tax_included', '0'),
('theme', 'flatly'),
('thousands_separator', '1'),
('timeformat', 'H:i:s'),
('timezone', 'America/New_York'),
('use_destination_based_tax', '0'),
('website', ''),
('work_order_enable', '0'),
('work_order_format', 'W%y{WSEQ:6}');

INSERT INTO `wbpos_tenants` (`tenant_id`, `tenant_code`, `company_name`, `status`, `timezone`, `currency_code`, `db_hostname`, `db_port`, `db_name`, `db_username`, `db_password`, `db_prefix`, `created_at`, `updated_at`) VALUES
(1, 'default', 'WBPOS Demo Store', 'active', 'America/New_York', 'USD', NULL, NULL, 'wbpos', NULL, NULL, 'wbpos_', NOW(), NULL);

INSERT INTO `wbpos_tenant_config` (`tenant_id`, `config_key`, `config_value`) VALUES
(1, 'company', 'WBPOS Demo Store'),
(1, 'currency_code', 'USD'),
(1, 'timezone', 'America/New_York');

INSERT INTO `wbpos_subscriptions` (`subscription_id`, `tenant_id`, `plan_id`, `status`, `trial_ends_at`, `period_start`, `period_end`, `cancel_at_period_end`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', NULL, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 0, NOW(), NULL);

INSERT INTO `wbpos_stock_locations` (`location_id`, `location_name`, `deleted`, `tenant_id`) VALUES
(1, 'Main Store', 0, 1);

INSERT INTO `wbpos_customers_packages` (`package_id`, `package_name`, `points_percent`, `deleted`) VALUES
(1, 'Default', 0.00, 0),
(2, 'Silver', 5.00, 0),
(3, 'Gold', 10.00, 0);

INSERT INTO `wbpos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('Admin', 'User', NULL, '555-555-0100', 'admin@wbpos.demo', '100 Main Street', '', 'Springfield', 'IL', '62701', 'USA', 'System administrator', 1, 1);

INSERT INTO `wbpos_employees` (`username`, `password`, `person_id`, `deleted`, `hash_version`, `language`, `language_code`, `tenant_id`) VALUES
('admin', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1, 0, 2, 'english', 'en', 1);

INSERT INTO `wbpos_tenant_users` (`tenant_id`, `person_id`, `tenant_role`, `is_active`, `created_at`) VALUES
(1, 1, 'owner', 1, NOW());

-- Full admin grants (person_id 1)
INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('attributes', 1, 'office'),
('cashups', 1, 'home'),
('config', 1, 'office'),
('customers', 1, 'home'),
('employees', 1, 'office'),
('expenses', 1, 'home'),
('expenses_categories', 1, 'office'),
('giftcards', 1, 'home'),
('home', 1, 'home'),
('items', 1, 'home'),
('items_stock', 1, 'home'),
('item_kits', 1, 'home'),
('messages', 1, 'home'),
('office', 1, 'home'),
('receivings', 1, 'home'),
('receivings_stock', 1, 'home'),
('reports', 1, 'home'),
('reports_categories', 1, 'home'),
('reports_customers', 1, 'home'),
('reports_discounts', 1, 'home'),
('reports_employees', 1, 'home'),
('reports_expenses_categories', 1, 'home'),
('reports_inventory', 1, 'home'),
('reports_items', 1, 'home'),
('reports_payments', 1, 'home'),
('reports_receivings', 1, 'home'),
('reports_sales', 1, 'home'),
('reports_sales_taxes', 1, 'home'),
('reports_suppliers', 1, 'home'),
('reports_taxes', 1, 'home'),
('sales', 1, 'home'),
('sales_change_price', 1, '--'),
('sales_delete', 1, '--'),
('sales_stock', 1, 'home'),
('suppliers', 1, 'home'),
('taxes', 1, 'office');
