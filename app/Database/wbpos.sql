-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 02:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wbpos`
--

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_app_config`
--

CREATE TABLE `wbpos_app_config` (
  `key` varchar(50) NOT NULL,
  `value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_app_config`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_attribute_definitions`
--

CREATE TABLE `wbpos_attribute_definitions` (
  `definition_id` int(10) NOT NULL,
  `definition_name` varchar(255) NOT NULL,
  `definition_type` varchar(45) NOT NULL,
  `definition_unit` varchar(16) DEFAULT NULL,
  `definition_flags` tinyint(1) NOT NULL,
  `definition_fk` int(10) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_attribute_links`
--

CREATE TABLE `wbpos_attribute_links` (
  `attribute_id` int(11) DEFAULT NULL,
  `definition_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `receiving_id` int(11) DEFAULT NULL,
  `generated_unique_column` varchar(255) GENERATED ALWAYS AS (case when `sale_id` is null and `receiving_id` is null and `item_id` is not null then concat(`definition_id`,'-',`item_id`) else NULL end) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_attribute_values`
--

CREATE TABLE `wbpos_attribute_values` (
  `attribute_id` int(11) NOT NULL,
  `attribute_value` varchar(255) DEFAULT NULL,
  `attribute_date` date DEFAULT NULL,
  `attribute_decimal` decimal(7,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_cash_up`
--

CREATE TABLE `wbpos_cash_up` (
  `cashup_id` int(10) NOT NULL,
  `open_date` timestamp NULL DEFAULT current_timestamp(),
  `close_date` timestamp NULL DEFAULT NULL,
  `open_amount_cash` decimal(15,2) NOT NULL,
  `transfer_amount_cash` decimal(15,2) NOT NULL,
  `note` tinyint(4) NOT NULL DEFAULT 0,
  `closed_amount_cash` decimal(15,2) NOT NULL,
  `closed_amount_card` decimal(15,2) NOT NULL,
  `closed_amount_check` decimal(15,2) NOT NULL,
  `closed_amount_total` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `open_employee_id` int(10) NOT NULL,
  `close_employee_id` int(10) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `closed_amount_due` decimal(15,2) NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_cash_up`
--

INSERT INTO `wbpos_cash_up` (`cashup_id`, `open_date`, `close_date`, `open_amount_cash`, `transfer_amount_cash`, `note`, `closed_amount_cash`, `closed_amount_card`, `closed_amount_check`, `closed_amount_total`, `description`, `open_employee_id`, `close_employee_id`, `deleted`, `closed_amount_due`, `tenant_id`) VALUES
(1001, '2026-05-07 01:00:00', '2026-05-07 13:00:00', 100.00, 0.00, 0, 180.00, 0.00, 0.00, 180.00, 'Day 1 close', 1, 1, 0, 0.00, 1),
(1002, '2026-05-12 01:00:00', '2026-05-12 13:00:00', 100.00, 0.00, 0, 50.00, 12.00, 0.00, 62.00, 'Day 2 close', 1, 1, 0, 0.00, 1),
(1003, '2026-05-17 01:00:00', '2026-05-17 13:00:00', 100.00, 0.00, 0, 0.00, 17.00, 0.00, 17.00, 'Day 3 close', 1, 1, 0, 0.00, 1),
(1004, '2026-05-22 01:00:00', '2026-05-22 13:00:00', 100.00, 0.00, 0, 146.50, 0.00, 0.00, 146.50, 'Day 4 close', 1, 1, 0, 0.00, 1),
(1005, '2026-05-28 01:00:00', '2026-05-28 13:00:00', 100.00, 0.00, 0, 240.00, 0.00, 0.00, 240.00, 'Day 5 close', 1, 1, 0, 0.00, 1),
(1006, '2026-06-05 01:00:00', '2026-06-05 13:00:00', 100.00, 0.00, 0, 123.00, 0.00, 0.00, 123.00, 'Today close', 1, 1, 0, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_customers`
--

CREATE TABLE `wbpos_customers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `taxable` tinyint(1) NOT NULL DEFAULT 1,
  `tax_id` varchar(32) NOT NULL DEFAULT '',
  `sales_tax_code_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` tinyint(1) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(10) NOT NULL,
  `consent` tinyint(4) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_customers`
--

INSERT INTO `wbpos_customers` (`person_id`, `company_name`, `account_number`, `taxable`, `tax_id`, `sales_tax_code_id`, `package_id`, `points`, `deleted`, `discount`, `discount_type`, `date`, `employee_id`, `consent`, `tenant_id`) VALUES
(1011, NULL, 'CUST-001', 1, '', NULL, 2, 120, 0, 0.00, 0, '2026-05-01 02:00:00', 1, 1, 1),
(1012, 'Hall Trading', 'CUST-002', 1, '', NULL, 3, 80, 0, 5.00, 0, '2026-05-02 02:00:00', 1, 1, 1),
(1013, NULL, 'CUST-003', 1, '', NULL, 2, 45, 0, 0.00, 0, '2026-05-03 02:00:00', 1, 1, 1),
(1014, 'Young Corp', 'CUST-004', 1, '', NULL, 4, 200, 0, 10.00, 1, '2026-05-04 02:00:00', 1, 1, 1),
(1015, NULL, 'CUST-005', 1, '', NULL, 1, 15, 0, 0.00, 0, '2026-05-05 02:00:00', 1, 1, 1),
(1016, 'Scott Retail', 'CUST-006', 1, '', NULL, 5, 300, 0, 0.00, 0, '2026-05-06 02:00:00', 1, 1, 1),
(1027, NULL, 'CUST-00001', 1, '', NULL, NULL, NULL, 0, 0.00, 0, '2026-06-16 04:08:57', 1026, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_customers_packages`
--

CREATE TABLE `wbpos_customers_packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `points_percent` float NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_customers_packages`
--

INSERT INTO `wbpos_customers_packages` (`package_id`, `package_name`, `points_percent`, `deleted`) VALUES
(1, 'Default', 0, 0),
(2, 'Silver', 5, 0),
(3, 'Gold', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_customers_points`
--

CREATE TABLE `wbpos_customers_points` (
  `id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `points_earned` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_dinner_tables`
--

CREATE TABLE `wbpos_dinner_tables` (
  `dinner_table_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_dinner_tables`
--

INSERT INTO `wbpos_dinner_tables` (`dinner_table_id`, `name`, `status`, `deleted`) VALUES
(1001, 'Table 1', 0, 0),
(1002, 'Table 2', 0, 0),
(1003, 'Table 3', 0, 0),
(1004, 'Take Away', 0, 0),
(1005, 'Delivery', 0, 0),
(1006, 'VIP Room', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_employees`
--

CREATE TABLE `wbpos_employees` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `hash_version` tinyint(1) NOT NULL DEFAULT 2,
  `language` varchar(48) DEFAULT NULL,
  `language_code` varchar(8) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_employees`
--

INSERT INTO `wbpos_employees` (`username`, `password`, `person_id`, `deleted`, `hash_version`, `language`, `language_code`, `tenant_id`) VALUES
('admin', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1, 0, 2, 'english', 'en', 1),
('maria', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1021, 0, 2, 'english', 'en', 1),
('tom', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1022, 0, 2, 'english', 'en', 1),
('nina', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1023, 0, 2, 'english', 'en', 1),
('paul', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1024, 0, 2, 'english', 'en', 1),
('rita', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1025, 0, 2, 'english', 'en', 1),
('maria_blue', '$2y$10$3TmH69FAwUINRA7sKHkhrOrmOPFlbCfiT3rluA.me/Nog3mu.A1EC', 1026, 0, 2, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_expenses`
--

CREATE TABLE `wbpos_expenses` (
  `expense_id` int(10) NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `amount` decimal(15,2) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `supplier_tax_code` varchar(255) DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT NULL,
  `supplier_id` int(10) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_expenses`
--

INSERT INTO `wbpos_expenses` (`expense_id`, `date`, `amount`, `payment_type`, `expense_category_id`, `description`, `employee_id`, `deleted`, `supplier_tax_code`, `tax_amount`, `supplier_id`, `tenant_id`) VALUES
(4, '2026-04-28 10:49:55', 10.00, 'Cash', 4, '', 12, 0, '', 0.00, NULL, 3),
(7, '2026-04-28 10:52:19', 0.00, 'Cash', 4, '', 12, 0, '', 0.00, NULL, 3),
(1001, '2026-05-01 01:00:00', 1200.00, 'Cash', 1001, 'May store rent', 1, 0, '', 0.00, NULL, 1),
(1002, '2026-05-05 02:00:00', 180.00, 'Cash', 1002, 'Electricity bill', 1, 0, '', 0.00, NULL, 1),
(1003, '2026-05-10 02:00:00', 2500.00, 'Check', 1003, 'Staff payroll', 1, 0, '', 0.00, NULL, 1),
(1004, '2026-05-15 03:00:00', 350.00, 'Cash', 1004, 'Facebook ads', 1, 0, '', 0.00, NULL, 1),
(1005, '2026-05-20 03:30:00', 95.00, 'Cash', 1005, 'Receipt paper', 1, 0, '', 0.00, NULL, 1),
(1006, '2026-05-25 04:00:00', 420.00, 'Credit', 1006, 'AC repair', 1, 0, '', 0.00, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_expense_categories`
--

CREATE TABLE `wbpos_expense_categories` (
  `expense_category_id` int(10) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_description` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_expense_categories`
--

INSERT INTO `wbpos_expense_categories` (`expense_category_id`, `category_name`, `category_description`, `deleted`, `tenant_id`) VALUES
(4, 'Bag', '', 0, 3),
(7, 'ad', '', 0, 3),
(10, 'Snack', '', 0, 3),
(1001, 'Rent', 'Store rent payments', 0, 1),
(1002, 'Utilities', 'Electricity, water, internet', 0, 1),
(1003, 'Salaries', 'Staff payroll', 0, 1),
(1004, 'Marketing', 'Ads and promotions', 0, 1),
(1005, 'Supplies', 'Office and store supplies', 0, 1),
(1006, 'Maintenance', 'Repairs and upkeep', 0, 1),
(1007, 'Drink', 'Drink', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_giftcards`
--

CREATE TABLE `wbpos_giftcards` (
  `record_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `giftcard_id` int(11) NOT NULL,
  `giftcard_number` varchar(255) DEFAULT NULL,
  `value` decimal(15,2) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `person_id` int(10) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_giftcards`
--

INSERT INTO `wbpos_giftcards` (`record_time`, `giftcard_id`, `giftcard_number`, `value`, `deleted`, `person_id`, `tenant_id`) VALUES
('2026-05-01 03:00:00', 1001, 'GC-10001', 25.00, 0, 1011, 1),
('2026-05-05 03:00:00', 1002, 'GC-10002', 50.00, 0, 1012, 1),
('2026-05-10 03:00:00', 1003, 'GC-10003', 100.00, 0, 1013, 1),
('2026-05-15 03:00:00', 1004, 'GC-10004', 75.00, 0, 1014, 1),
('2026-05-20 03:00:00', 1005, 'GC-10005', 30.00, 0, 1015, 1),
('2026-05-25 03:00:00', 1006, 'GC-10006', 150.00, 0, 1016, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_grants`
--

CREATE TABLE `wbpos_grants` (
  `permission_id` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `menu_group` varchar(32) DEFAULT 'home'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_grants`
--

INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('attributes', 1, 'office'),
('attributes', 1026, 'office'),
('cashups', 1, 'home'),
('cashups', 1021, 'home'),
('cashups', 1022, 'home'),
('cashups', 1023, 'home'),
('cashups', 1025, 'home'),
('cashups', 1026, 'home'),
('config', 1, 'office'),
('config', 1026, 'office'),
('customers', 1, 'home'),
('customers', 1021, 'home'),
('customers', 1022, 'home'),
('customers', 1023, 'home'),
('customers', 1026, 'home'),
('employees', 1, 'office'),
('employees', 1026, 'office'),
('expenses', 1, 'home'),
('expenses', 1022, 'home'),
('expenses', 1025, 'home'),
('expenses', 1026, 'home'),
('expenses_categories', 1, 'office'),
('expenses_categories', 1022, 'office'),
('expenses_categories', 1025, 'office'),
('expenses_categories', 1026, 'office'),
('giftcards', 1, 'home'),
('giftcards', 1022, 'home'),
('giftcards', 1023, 'home'),
('giftcards', 1026, 'home'),
('home', 1, 'home'),
('home', 1021, 'home'),
('home', 1022, 'home'),
('home', 1023, 'home'),
('home', 1024, 'home'),
('home', 1025, 'home'),
('home', 1026, 'home'),
('items', 1, 'home'),
('items', 1022, 'home'),
('items', 1024, 'home'),
('items', 1026, 'home'),
('items_stock', 1, 'home'),
('items_stock', 1022, 'home'),
('items_stock', 1024, 'home'),
('items_stock', 1026, 'home'),
('item_kits', 1, 'home'),
('item_kits', 1022, 'home'),
('item_kits', 1026, 'home'),
('messages', 1, 'home'),
('messages', 1026, 'home'),
('office', 1, 'home'),
('office', 1026, 'home'),
('receivings', 1, 'home'),
('receivings', 1022, 'home'),
('receivings', 1024, 'home'),
('receivings', 1026, 'home'),
('receivings_stock', 1, 'home'),
('receivings_stock', 1022, 'home'),
('receivings_stock', 1024, 'home'),
('receivings_stock', 1026, 'home'),
('reports', 1, 'home'),
('reports', 1022, 'home'),
('reports', 1024, 'home'),
('reports', 1025, 'home'),
('reports', 1026, 'home'),
('reports_categories', 1, 'home'),
('reports_categories', 1022, 'home'),
('reports_categories', 1026, 'home'),
('reports_customers', 1, 'home'),
('reports_customers', 1022, 'home'),
('reports_customers', 1026, 'home'),
('reports_discounts', 1, 'home'),
('reports_discounts', 1022, 'home'),
('reports_discounts', 1026, 'home'),
('reports_employees', 1, 'home'),
('reports_employees', 1026, 'home'),
('reports_expenses_categories', 1, 'home'),
('reports_expenses_categories', 1025, 'home'),
('reports_expenses_categories', 1026, 'home'),
('reports_inventory', 1, 'home'),
('reports_inventory', 1022, 'home'),
('reports_inventory', 1024, 'home'),
('reports_inventory', 1026, 'home'),
('reports_items', 1, 'home'),
('reports_items', 1022, 'home'),
('reports_items', 1024, 'home'),
('reports_items', 1026, 'home'),
('reports_payments', 1, 'home'),
('reports_payments', 1022, 'home'),
('reports_payments', 1025, 'home'),
('reports_payments', 1026, 'home'),
('reports_receivings', 1, 'home'),
('reports_receivings', 1022, 'home'),
('reports_receivings', 1024, 'home'),
('reports_receivings', 1026, 'home'),
('reports_sales', 1, 'home'),
('reports_sales', 1022, 'home'),
('reports_sales', 1025, 'home'),
('reports_sales', 1026, 'home'),
('reports_sales_taxes', 1, 'home'),
('reports_sales_taxes', 1026, 'home'),
('reports_suppliers', 1, 'home'),
('reports_suppliers', 1022, 'home'),
('reports_suppliers', 1024, 'home'),
('reports_suppliers', 1026, 'home'),
('reports_taxes', 1, 'home'),
('reports_taxes', 1025, 'home'),
('reports_taxes', 1026, 'home'),
('sales', 1, 'home'),
('sales', 1021, 'home'),
('sales', 1022, 'home'),
('sales', 1023, 'home'),
('sales', 1026, 'home'),
('sales_change_price', 1, '--'),
('sales_change_price', 1022, '--'),
('sales_change_price', 1023, '--'),
('sales_change_price', 1026, '--'),
('sales_delete', 1, '--'),
('sales_delete', 1022, '--'),
('sales_delete', 1026, '--'),
('sales_stock', 1, 'home'),
('sales_stock', 1021, '--'),
('sales_stock', 1022, 'home'),
('sales_stock', 1023, 'home'),
('sales_stock', 1026, 'home'),
('suppliers', 1, 'home'),
('suppliers', 1022, 'home'),
('suppliers', 1024, 'home'),
('suppliers', 1026, 'home'),
('taxes', 1, 'office'),
('taxes', 1026, 'office');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_inventory`
--

CREATE TABLE `wbpos_inventory` (
  `trans_id` int(11) NOT NULL,
  `trans_items` int(11) NOT NULL DEFAULT 0,
  `trans_user` int(11) NOT NULL DEFAULT 0,
  `trans_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `trans_comment` text NOT NULL,
  `trans_location` int(11) NOT NULL,
  `trans_inventory` decimal(15,3) NOT NULL DEFAULT 0.000,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_inventory`
--

INSERT INTO `wbpos_inventory` (`trans_id`, `trans_items`, `trans_user`, `trans_date`, `trans_comment`, `trans_location`, `trans_inventory`, `tenant_id`) VALUES
(1001, 1001, 1, '2026-05-01 00:30:00', 'Opening stock count', 1, 120.000, 1),
(1002, 1002, 1, '2026-05-05 00:30:00', 'Bread restock', 1, 45.000, 1),
(1003, 1003, 1, '2026-05-10 00:30:00', 'Milk restock', 1, 80.000, 1),
(1004, 1004, 1, '2026-05-15 00:30:00', 'Eggs restock', 1, 25.000, 1),
(1005, 1005, 1, '2026-05-20 00:30:00', 'Soap restock', 1, 60.000, 1),
(1006, 1006, 1, '2026-05-25 00:30:00', 'Cable restock', 1, 35.000, 1),
(1007, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1007, 0.000, 2),
(1008, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1008, 0.000, 2),
(1009, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1009, 0.000, 2),
(1010, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1010, 0.000, 2),
(1011, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1011, 0.000, 2),
(1012, 1007, 1026, '2026-06-16 04:12:17', 'Manual Edit of Quantity', 1012, 0.000, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_invoices`
--

CREATE TABLE `wbpos_invoices` (
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(64) NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'USD',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','open','paid','void') NOT NULL DEFAULT 'open',
  `due_date` date DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_invoice_payments`
--

CREATE TABLE `wbpos_invoice_payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(40) NOT NULL,
  `provider_payment_id` varchar(191) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_items`
--

CREATE TABLE `wbpos_items` (
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `reorder_level` decimal(15,3) NOT NULL DEFAULT 0.000,
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT 1.000,
  `item_id` int(10) NOT NULL,
  `pic_filename` varchar(255) DEFAULT NULL,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `stock_type` tinyint(1) NOT NULL DEFAULT 0,
  `item_type` tinyint(1) NOT NULL DEFAULT 0,
  `tax_category_id` int(11) DEFAULT NULL,
  `qty_per_pack` decimal(15,3) NOT NULL DEFAULT 1.000,
  `pack_name` varchar(8) DEFAULT 'Each',
  `low_sell_item_id` int(10) DEFAULT 0,
  `hsn_code` varchar(32) NOT NULL DEFAULT '',
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_items`
--

INSERT INTO `wbpos_items` (`name`, `category`, `supplier_id`, `item_number`, `description`, `cost_price`, `unit_price`, `reorder_level`, `receiving_quantity`, `item_id`, `pic_filename`, `allow_alt_description`, `is_serialized`, `deleted`, `stock_type`, `item_type`, `tax_category_id`, `qty_per_pack`, `pack_name`, `low_sell_item_id`, `hsn_code`, `tenant_id`) VALUES
('Coca Cola 330ml', 'Beverages', 1001, 'SKU-1001', 'Soft drink can', 0.80, 2.00, 20.000, 1.000, 1001, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('White Bread Loaf', 'Bakery', 1002, 'SKU-1002', 'Fresh white bread', 1.20, 4.00, 50.000, 1.000, 1002, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Fresh Milk 1L', 'Dairy', 1003, 'SKU-1003', 'Whole milk carton', 2.50, 5.50, 30.000, 1.000, 1003, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Large Eggs 12pk', 'Grocery', 1004, 'SKU-1004', 'Grade A eggs', 3.00, 6.00, 30.000, 1.000, 1004, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Dish Soap 500ml', 'Household', 1005, 'SKU-1005', 'Liquid dish soap', 1.50, 4.50, 25.000, 1.000, 1005, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('USB Cable Type-C', 'Electronics', 1006, 'SKU-1006', 'Charging cable', 4.00, 12.00, 15.000, 1.000, 1006, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Orange', 'Drink', NULL, NULL, '', 0.00, 0.00, 1.000, 1.000, 1007, 'photo_2025-08-19_13-20-47.jpg', 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 1007, '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_items_taxes`
--

CREATE TABLE `wbpos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_items_taxes`
--

INSERT INTO `wbpos_items_taxes` (`item_id`, `name`, `percent`, `tenant_id`) VALUES
(1001, 'Sales Tax', 8.000, 1),
(1002, 'Sales Tax', 8.000, 1),
(1003, 'Sales Tax', 8.000, 1),
(1004, 'Sales Tax', 8.000, 1),
(1005, 'Sales Tax', 8.000, 1),
(1006, 'Sales Tax', 8.000, 1),
(1007, 'Sales Tax', 8.000, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_item_kits`
--

CREATE TABLE `wbpos_item_kits` (
  `item_kit_id` int(11) NOT NULL,
  `item_kit_number` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `kit_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kit_discount_type` tinyint(1) NOT NULL DEFAULT 0,
  `price_option` tinyint(1) NOT NULL DEFAULT 0,
  `print_option` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_item_kits`
--

INSERT INTO `wbpos_item_kits` (`item_kit_id`, `item_kit_number`, `name`, `description`, `item_id`, `kit_discount`, `kit_discount_type`, `price_option`, `print_option`, `tenant_id`) VALUES
(1001, 'KIT-001', 'Breakfast Pack', 'Bread and milk combo', 0, 0.00, 0, 0, 0, 1),
(1002, 'KIT-002', 'Drink Bundle', 'Six cola cans', 0, 1.00, 0, 0, 0, 1),
(1003, 'KIT-003', 'Cleaning Kit', 'Dish soap pack', 0, 0.00, 0, 0, 0, 1),
(1004, 'KIT-004', 'Tech Starter', 'USB cable bundle', 0, 2.00, 0, 0, 0, 1),
(1005, 'KIT-005', 'Grocery Essentials', 'Eggs and milk', 0, 0.50, 0, 0, 0, 1),
(1006, 'KIT-006', 'Party Pack', 'Cola and bread', 0, 0.00, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_item_kit_items`
--

CREATE TABLE `wbpos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `kit_sequence` int(3) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_item_kit_items`
--

INSERT INTO `wbpos_item_kit_items` (`item_kit_id`, `item_id`, `quantity`, `kit_sequence`, `tenant_id`) VALUES
(1001, 1002, 1.000, 1, 1),
(1001, 1003, 1.000, 2, 1),
(1002, 1001, 6.000, 1, 1),
(1003, 1005, 2.000, 1, 1),
(1004, 1006, 2.000, 1, 1),
(1005, 1003, 1.000, 1, 1),
(1005, 1004, 1.000, 2, 1),
(1006, 1001, 4.000, 1, 1),
(1006, 1002, 2.000, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_item_quantities`
--

CREATE TABLE `wbpos_item_quantities` (
  `item_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_item_quantities`
--

INSERT INTO `wbpos_item_quantities` (`item_id`, `location_id`, `quantity`, `tenant_id`) VALUES
(1001, 1, 120.000, 1),
(1002, 1, 45.000, 1),
(1003, 1, 80.000, 1),
(1004, 1, 25.000, 1),
(1005, 1, 60.000, 1),
(1006, 1, 35.000, 1),
(1007, 1007, 0.000, 2),
(1007, 1008, 0.000, 2),
(1007, 1009, 0.000, 2),
(1007, 1010, 0.000, 2),
(1007, 1011, 0.000, 2),
(1007, 1012, 0.000, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_migrations`
--

CREATE TABLE `wbpos_migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_migrations`
--

INSERT INTO `wbpos_migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20170501150000', 'App\\Database\\Migrations\\Migration_Upgrade_To_3_1_1', 'default', 'App', 1777279690, 1),
(2, '20170502221506', 'App\\Database\\Migrations\\Migration_Sales_Tax_Data', 'default', 'App', 1777279690, 1),
(3, '20180225100000', 'App\\Database\\Migrations\\Migration_Upgrade_To_3_2_0', 'default', 'App', 1777279690, 1),
(4, '20180501100000', 'App\\Database\\Migrations\\Migration_Upgrade_To_3_2_1', 'default', 'App', 1777279690, 1),
(5, '20181015100000', 'App\\Database\\Migrations\\Migration_Attributes', 'default', 'App', 1777279690, 1),
(6, '20190111270000', 'App\\Database\\Migrations\\Migration_Upgrade_To_3_3_0', 'default', 'App', 1777279691, 1),
(7, '20190129212600', 'App\\Database\\Migrations\\Migration_IndiaGST', 'default', 'App', 1777279691, 1),
(8, '20190213210000', 'App\\Database\\Migrations\\Migration_IndiaGST1', 'default', 'App', 1777279691, 1),
(9, '20190220210000', 'App\\Database\\Migrations\\Migration_IndiaGST2', 'default', 'App', 1777279691, 1),
(10, '20190301124900', 'App\\Database\\Migrations\\Migration_decimal_attribute_type', 'default', 'App', 1777279691, 1),
(11, '20190317102600', 'App\\Database\\Migrations\\Migration_add_iso_4217', 'default', 'App', 1777279691, 1),
(12, '20190427100000', 'App\\Database\\Migrations\\Migration_PaymentTracking', 'default', 'App', 1777279691, 1),
(13, '20190502100000', 'App\\Database\\Migrations\\Migration_RefundTracking', 'default', 'App', 1777279691, 1),
(14, '20190612100000', 'App\\Database\\Migrations\\Migration_DBFix', 'default', 'App', 1777279692, 1),
(15, '20190615100000', 'App\\Database\\Migrations\\Migration_fix_attribute_datetime', 'default', 'App', 1777279692, 1),
(16, '20190712150200', 'App\\Database\\Migrations\\Migration_fix_empty_reports', 'default', 'App', 1777279692, 1),
(17, '20191008100000', 'App\\Database\\Migrations\\Migration_receipttaxindicator', 'default', 'App', 1777279692, 1),
(18, '20191231100000', 'App\\Database\\Migrations\\Migration_PaymentDateFix', 'default', 'App', 1777279692, 1),
(19, '20200125100000', 'App\\Database\\Migrations\\Migration_SalesChangePrice', 'default', 'App', 1777279692, 1),
(20, '20200202000000', 'App\\Database\\Migrations\\Migration_TaxAmount', 'default', 'App', 1777279692, 1),
(21, '20200215100000', 'App\\Database\\Migrations\\Migration_taxgroupconstraint', 'default', 'App', 1777279692, 1),
(22, '20200508000000', 'App\\Database\\Migrations\\Migration_image_upload_defaults', 'default', 'App', 1777279692, 1),
(23, '20200819000000', 'App\\Database\\Migrations\\Migration_modify_attr_links_constraint', 'default', 'App', 1777279692, 1),
(24, '20201108100000', 'App\\Database\\Migrations\\Migration_cashrounding', 'default', 'App', 1777279692, 1),
(25, '20201110000000', 'App\\Database\\Migrations\\Migration_add_item_kit_number', 'default', 'App', 1777279692, 1),
(26, '20210103000000', 'App\\Database\\Migrations\\Migration_modify_session_datatype', 'default', 'App', 1777279692, 1),
(27, '20210422000000', 'App\\Database\\Migrations\\Migration_database_optimizations', 'default', 'App', 1777279695, 1),
(28, '20210422000001', 'App\\Database\\Migrations\\Migration_remove_duplicate_links', 'default', 'App', 1777279695, 1),
(29, '20210714140000', 'App\\Database\\Migrations\\Migration_move_expenses_categories', 'default', 'App', 1777279695, 1),
(30, '20220127000000', 'App\\Database\\Migrations\\Convert_to_ci4', 'default', 'App', 1777279695, 1),
(31, '20230307000000', 'App\\Database\\Migrations\\IntToTinyint', 'default', 'App', 1777279695, 1),
(32, '20230412000000', 'App\\Database\\Migrations\\Migration_add_missing_config', 'default', 'App', 1777279695, 1),
(33, '20230412000001', 'App\\Database\\Migrations\\Migration_drop_account_number_index', 'default', 'App', 1777279695, 1),
(34, '20240319000000', 'App\\Database\\Migrations\\Migration_Convert_Barcode_Types', 'default', 'App', 1777279695, 1),
(35, '20240630000001', 'App\\Database\\Migrations\\Migration_fix_keys_for_db_upgrade', 'default', 'App', 1777279695, 1),
(36, '20240826000000', 'App\\Database\\Migrations\\fix_duplicate_attributes', 'default', 'App', 1777279696, 1),
(37, '20250213000000', 'App\\Database\\Migrations\\Migration_Attributes_fix_cascading_delete', 'default', 'App', 1777279696, 1),
(38, '20250425000000', 'App\\Database\\Migrations\\Migration_sessions_migration', 'default', 'App', 1777279696, 1),
(39, '20250519000000', 'App\\Database\\Migrations\\MigrationOptimizationIndices', 'default', 'App', 1777279696, 1),
(40, '20250522000000', 'App\\Database\\Migrations\\AttributeLinksUniqueConstraint', 'default', 'App', 1777279696, 1),
(41, '20250716170000', 'App\\Database\\Migrations\\Migration_MissingConfigKeys', 'default', 'App', 1777279696, 1),
(42, '20250729170000', 'App\\Database\\Migrations\\Migration_NullableTaxCategoryId', 'default', 'App', 1777279696, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_modules`
--

CREATE TABLE `wbpos_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_modules`
--

INSERT INTO `wbpos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_attributes', 'module_attributes_desc', 107, 'attributes'),
('module_cashups', 'module_cashups_desc', 110, 'cashups'),
('module_config', 'module_config_desc', 900, 'config'),
('module_customers', 'module_customers_desc', 10, 'customers'),
('module_employees', 'module_employees_desc', 80, 'employees'),
('module_expenses', 'module_expenses_desc', 108, 'expenses'),
('module_expenses_categories', 'module_expenses_categories_desc', 109, 'expenses_categories'),
('module_giftcards', 'module_giftcards_desc', 90, 'giftcards'),
('module_home', 'module_home_desc', 1, 'home'),
('module_items', 'module_items_desc', 20, 'items'),
('module_item_kits', 'module_item_kits_desc', 30, 'item_kits'),
('module_messages', 'module_messages_desc', 98, 'messages'),
('module_office', 'module_office_desc', 999, 'office'),
('module_receivings', 'module_receivings_desc', 60, 'receivings'),
('module_reports', 'module_reports_desc', 50, 'reports'),
('module_sales', 'module_sales_desc', 70, 'sales'),
('module_suppliers', 'module_suppliers_desc', 40, 'suppliers'),
('module_taxes', 'module_taxes_desc', 105, 'taxes');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_people`
--

CREATE TABLE `wbpos_people` (
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` int(1) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `person_id` int(10) NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_people`
--

INSERT INTO `wbpos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('Admin', 'User', NULL, '555-555-0100', 'admin@wbpos.demo', '100 Main Street', '', 'Springfield', 'IL', '62701', 'USA', 'System administrator', 1, 1),
('James', 'Miller', 1, '555-1001', 'james@freshfoods.com', '100 Supply Road', '', 'Springfield', 'IL', '62701', 'USA', 'Beverage supplier', 1001, 1),
('Sarah', 'Johnson', 0, '555-1002', 'sarah@bakeryco.com', '200 Baker St', '', 'Springfield', 'IL', '62702', 'USA', 'Bakery supplier', 1002, 1),
('David', 'Brown', 1, '555-1003', 'david@dairyfarm.com', '300 Farm Lane', '', 'Springfield', 'IL', '62703', 'USA', 'Dairy supplier', 1003, 1),
('Emily', 'Davis', 0, '555-1004', 'emily@groceryhub.com', '400 Market Ave', '', 'Springfield', 'IL', '62704', 'USA', 'Grocery supplier', 1004, 1),
('Robert', 'Wilson', 1, '555-1005', 'robert@homegoods.com', '500 Trade Blvd', '', 'Springfield', 'IL', '62705', 'USA', 'Household supplier', 1005, 1),
('Lisa', 'Taylor', 0, '555-1006', 'lisa@techparts.com', '600 Tech Park', '', 'Springfield', 'IL', '62706', 'USA', 'Electronics supplier', 1006, 1),
('Alice', 'Walker', 0, '555-2001', 'alice@email.com', '10 Oak Street', '', 'Springfield', 'IL', '62710', 'USA', 'Regular customer', 1011, 1),
('Brian', 'Hall', 1, '555-2002', 'brian@email.com', '20 Pine Road', '', 'Springfield', 'IL', '62711', 'USA', 'Regular customer', 1012, 1),
('Carol', 'Allen', 0, '555-2003', 'carol@email.com', '30 Maple Ave', '', 'Springfield', 'IL', '62712', 'USA', 'Regular customer', 1013, 1),
('Daniel', 'Young', 1, '555-2004', 'daniel@email.com', '40 Cedar Lane', '', 'Springfield', 'IL', '62713', 'USA', 'Regular customer', 1014, 1),
('Eva', 'King', 0, '555-2005', 'eva@email.com', '50 Birch Way', '', 'Springfield', 'IL', '62714', 'USA', 'Regular customer', 1015, 1),
('Frank', 'Scott', 1, '555-2006', 'frank@email.com', '60 Elm Court', '', 'Springfield', 'IL', '62715', 'USA', 'Regular customer', 1016, 1),
('Maria', 'Cashier', 0, '555-3001', 'maria@store.com', '1 Staff Lane', '', 'Springfield', 'IL', '62720', 'USA', 'Cashier', 1021, 1),
('Tom', 'Manager', 1, '555-3002', 'tom@store.com', '2 Staff Lane', '', 'Springfield', 'IL', '62721', 'USA', 'Manager', 1022, 1),
('Nina', 'Sales', 0, '555-3003', 'nina@store.com', '3 Staff Lane', '', 'Springfield', 'IL', '62722', 'USA', 'Sales staff', 1023, 1),
('Paul', 'Stock', 1, '555-3004', 'paul@store.com', '4 Staff Lane', '', 'Springfield', 'IL', '62723', 'USA', 'Stock clerk', 1024, 1),
('Rita', 'Supervisor', 0, '555-3005', 'rita@store.com', '5 Staff Lane', '', 'Springfield', 'IL', '62724', 'USA', 'Supervisor', 1025, 1),
('Maria', 'Lopez', NULL, '098-777-1234', 'maria@blueocean.cafe', '', '', '', '', '', '', '', 1026, 2),
('Wonhee', 'Lee', NULL, '', '', '', '', '', '', '', '', '', 1027, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_permissions`
--

CREATE TABLE `wbpos_permissions` (
  `permission_id` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `location_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_permissions`
--

INSERT INTO `wbpos_permissions` (`permission_id`, `module_id`, `location_id`) VALUES
('attributes', 'attributes', NULL),
('cashups', 'cashups', NULL),
('config', 'config', NULL),
('customers', 'customers', NULL),
('employees', 'employees', NULL),
('expenses', 'expenses', NULL),
('expenses_categories', 'expenses_categories', NULL),
('giftcards', 'giftcards', NULL),
('home', 'home', NULL),
('items', 'items', NULL),
('items_stock', 'items', 1),
('item_kits', 'item_kits', NULL),
('messages', 'messages', NULL),
('office', 'office', NULL),
('receivings', 'receivings', NULL),
('receivings_stock', 'receivings', 1),
('reports', 'reports', NULL),
('reports_categories', 'reports', NULL),
('reports_customers', 'reports', NULL),
('reports_discounts', 'reports', NULL),
('reports_employees', 'reports', NULL),
('reports_expenses_categories', 'reports', NULL),
('reports_inventory', 'reports', NULL),
('reports_items', 'reports', NULL),
('reports_payments', 'reports', NULL),
('reports_receivings', 'reports', NULL),
('reports_sales', 'reports', NULL),
('reports_sales_taxes', 'reports', NULL),
('reports_suppliers', 'reports', NULL),
('reports_taxes', 'reports', NULL),
('sales', 'sales', NULL),
('sales_change_price', 'sales', NULL),
('sales_delete', 'sales', NULL),
('sales_stock', 'sales', 1),
('suppliers', 'suppliers', NULL),
('taxes', 'taxes', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_plans`
--

CREATE TABLE `wbpos_plans` (
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `plan_code` varchar(64) NOT NULL,
  `plan_name` varchar(120) NOT NULL,
  `price_monthly` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_users` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `max_locations` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `max_items` int(10) UNSIGNED NOT NULL DEFAULT 5000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_plans`
--

INSERT INTO `wbpos_plans` (`plan_id`, `plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`, `created_at`) VALUES
(1, 'starter', 'Starter', 0.00, 5, 1, 5000, 1, '2026-04-27 21:23:16'),
(2, 'basic', 'Basic', 19.00, 5, 1, 5000, 1, '2026-04-27 23:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_platform_admins`
--

CREATE TABLE `wbpos_platform_admins` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_platform_admins`
--

INSERT INTO `wbpos_platform_admins` (`admin_id`, `username`, `password_hash`, `full_name`, `email`, `status`, `created_at`) VALUES
(1, 'superadmin', '$2y$12$N8lJSGcJNcJ8SmXHm2IeheIe3QAzLmrmt1q0opbjhlXj2uQHZu3nO', 'Platform Super Admin', NULL, 'active', '2026-04-27 21:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_receivings`
--

CREATE TABLE `wbpos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `receiving_id` int(10) NOT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_receivings`
--

INSERT INTO `wbpos_receivings` (`receiving_time`, `supplier_id`, `employee_id`, `comment`, `receiving_id`, `payment_type`, `reference`, `tenant_id`) VALUES
('2026-05-01 01:00:00', 1001, 1, 'Monthly beverage stock', 1001, 'Cash', 'PO-001', 1),
('2026-05-05 01:30:00', 1002, 1, 'Bakery delivery', 1002, 'Cash', 'PO-002', 1),
('2026-05-10 02:00:00', 1003, 1, 'Dairy restock', 1003, 'Check', 'PO-003', 1),
('2026-05-15 02:30:00', 1004, 1, 'Grocery order', 1004, 'Cash', 'PO-004', 1),
('2026-05-20 03:00:00', 1005, 1, 'Household supplies', 1005, 'Cash', 'PO-005', 1),
('2026-05-25 03:30:00', 1006, 1, 'Electronics order', 1006, 'Credit', 'PO-006', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_receivings_items`
--

CREATE TABLE `wbpos_receivings_items` (
  `receiving_id` int(10) NOT NULL DEFAULT 0,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `quantity_purchased` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` tinyint(1) NOT NULL DEFAULT 0,
  `item_location` int(11) NOT NULL,
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT 1.000,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_receivings_items`
--

INSERT INTO `wbpos_receivings_items` (`receiving_id`, `item_id`, `description`, `serialnumber`, `line`, `quantity_purchased`, `item_cost_price`, `item_unit_price`, `discount`, `discount_type`, `item_location`, `receiving_quantity`, `tenant_id`) VALUES
(1001, 1001, NULL, NULL, 1, 100.000, 0.80, 2.00, 0.00, 0, 1, 1.000, 1),
(1002, 1002, NULL, NULL, 1, 50.000, 1.20, 4.00, 0.00, 0, 1, 1.000, 1),
(1003, 1003, NULL, NULL, 1, 60.000, 2.50, 5.50, 0.00, 0, 1, 1.000, 1),
(1004, 1004, NULL, NULL, 1, 40.000, 3.00, 6.00, 0.00, 0, 1, 1.000, 1),
(1005, 1005, NULL, NULL, 1, 30.000, 1.50, 4.50, 0.00, 0, 1, 1.000, 1),
(1006, 1006, NULL, NULL, 1, 20.000, 4.00, 12.00, 0.00, 0, 1, 1.000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales`
--

CREATE TABLE `wbpos_sales` (
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `invoice_number` varchar(32) DEFAULT NULL,
  `dinner_table_id` int(11) DEFAULT NULL,
  `sale_id` int(10) NOT NULL,
  `quote_number` varchar(32) DEFAULT NULL,
  `sale_status` tinyint(1) NOT NULL DEFAULT 0,
  `work_order_number` varchar(32) DEFAULT NULL,
  `sale_type` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_sales`
--

INSERT INTO `wbpos_sales` (`sale_time`, `customer_id`, `employee_id`, `comment`, `invoice_number`, `dinner_table_id`, `sale_id`, `quote_number`, `sale_status`, `work_order_number`, `sale_type`, `tenant_id`) VALUES
('2026-05-07 03:15:00', 1011, 1, 'Morning sale', NULL, 1001, 1001, NULL, 0, NULL, 0, 1),
('2026-05-12 04:30:00', 1012, 1, 'Lunch rush', NULL, 1002, 1002, NULL, 0, NULL, 0, 1),
('2026-05-17 07:00:00', 1013, 1, 'Afternoon order', NULL, 1003, 1003, NULL, 0, NULL, 0, 1),
('2026-05-22 09:45:00', 1014, 1, 'Corporate purchase', NULL, 1004, 1004, NULL, 0, NULL, 0, 1),
('2026-05-28 11:20:00', 1015, 1, 'Evening sale', NULL, 1005, 1005, NULL, 0, NULL, 0, 1),
('2026-06-05 05:00:00', 1016, 1, 'Today sale', NULL, 1006, 1006, NULL, 0, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales_items`
--

CREATE TABLE `wbpos_sales_items` (
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `quantity_purchased` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` tinyint(1) NOT NULL DEFAULT 0,
  `item_location` int(11) NOT NULL,
  `print_option` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_sales_items`
--

INSERT INTO `wbpos_sales_items` (`sale_id`, `item_id`, `description`, `serialnumber`, `line`, `quantity_purchased`, `item_cost_price`, `item_unit_price`, `discount`, `discount_type`, `item_location`, `print_option`, `tenant_id`) VALUES
(1001, 1001, NULL, NULL, 1, 4.000, 0.80, 2.00, 0.00, 0, 1, 0, 1),
(1002, 1002, NULL, NULL, 1, 3.000, 1.20, 4.00, 0.00, 0, 1, 0, 1),
(1003, 1003, NULL, NULL, 1, 2.000, 2.50, 5.50, 0.00, 0, 1, 0, 1),
(1003, 1004, NULL, NULL, 2, 1.000, 3.00, 6.00, 0.00, 0, 1, 0, 1),
(1004, 1005, NULL, NULL, 1, 5.000, 1.50, 4.50, 0.00, 0, 1, 0, 1),
(1004, 1006, NULL, NULL, 2, 2.000, 4.00, 12.00, 0.00, 0, 1, 0, 1),
(1005, 1001, NULL, NULL, 1, 10.000, 0.80, 2.00, 0.00, 0, 1, 0, 1),
(1005, 1002, NULL, NULL, 2, 5.000, 1.20, 4.00, 0.00, 0, 1, 0, 1),
(1006, 1003, NULL, NULL, 1, 2.000, 2.50, 5.50, 0.00, 0, 1, 0, 1),
(1006, 1006, NULL, NULL, 2, 1.000, 4.00, 12.00, 0.00, 0, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales_items_taxes`
--

CREATE TABLE `wbpos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `tax_type` tinyint(1) NOT NULL DEFAULT 0,
  `rounding_code` tinyint(1) NOT NULL DEFAULT 0,
  `cascade_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `item_tax_amount` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `sales_tax_code_id` int(11) DEFAULT NULL,
  `jurisdiction_id` int(11) DEFAULT NULL,
  `tax_category_id` int(11) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales_payments`
--

CREATE TABLE `wbpos_sales_payments` (
  `payment_id` int(11) NOT NULL,
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `cash_refund` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cash_adjustment` tinyint(4) NOT NULL DEFAULT 0,
  `employee_id` int(11) DEFAULT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_code` varchar(40) NOT NULL DEFAULT '',
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_sales_payments`
--

INSERT INTO `wbpos_sales_payments` (`payment_id`, `sale_id`, `payment_type`, `payment_amount`, `cash_refund`, `cash_adjustment`, `employee_id`, `payment_time`, `reference_code`, `tenant_id`) VALUES
(1001, 1001, 'Cash', 8.00, 0.00, 0, 1, '2026-05-07 03:15:00', '', 1),
(1002, 1002, 'Debit', 12.00, 0.00, 0, 1, '2026-05-12 04:30:00', '', 1),
(1003, 1003, 'Credit', 17.00, 0.00, 0, 1, '2026-05-17 07:00:00', '', 1),
(1004, 1004, 'Cash', 46.50, 0.00, 0, 1, '2026-05-22 09:45:00', '', 1),
(1005, 1005, 'Cash', 40.00, 0.00, 0, 1, '2026-05-28 11:20:00', '', 1),
(1006, 1006, 'Cash', 23.00, 0.00, 0, 1, '2026-06-05 05:00:00', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales_reward_points`
--

CREATE TABLE `wbpos_sales_reward_points` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `earned` float NOT NULL,
  `used` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sales_taxes`
--

CREATE TABLE `wbpos_sales_taxes` (
  `sales_taxes_id` int(11) NOT NULL,
  `sale_id` int(10) NOT NULL,
  `jurisdiction_id` int(11) DEFAULT NULL,
  `tax_category_id` int(11) DEFAULT NULL,
  `tax_type` smallint(2) NOT NULL,
  `tax_group` varchar(32) NOT NULL,
  `sale_tax_basis` decimal(15,4) NOT NULL,
  `sale_tax_amount` decimal(15,4) NOT NULL,
  `print_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL,
  `sales_tax_code_id` int(11) DEFAULT NULL,
  `rounding_code` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_sessions`
--

CREATE TABLE `wbpos_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_sessions`
--

INSERT INTO `wbpos_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('ospos_session:273d3a3b6dc4bd90a2a0dad56d1c5638', '::1', '2026-06-16 08:37:30', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393035303b637372665f6f73706f735f76347c733a33323a226564363765643039343264336438633439666537633531633130343835323034223b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a33383a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f223b),
('ospos_session:2a1be80e1171fc14d60c2e00a6373c57', '::1', '2026-06-16 12:49:55', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313631343139343b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6974656d73223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a223365353538366663626136656433316231626630636335383562663734366533223b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a303b6974656d5f6c6f636174696f6e7c733a313a2231223b),
('ospos_session:392b9d08f41bf8f45dcae76e6c03e2dd', '::1', '2026-06-16 09:58:51', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313630333738333b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f636f6e666967223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a226264633761353331643533333066326466326233316536333963336432656261223b6d656e755f67726f75707c733a363a226f6666696365223b616c6c6f775f74656d705f6974656d737c693a313b6974656d5f6c6f636174696f6e7c733a313a2231223b726563765f636172747c613a303a7b7d726563765f6d6f64657c733a373a2272656365697665223b726563765f73746f636b5f736f757263657c693a313030323b726563765f73746f636b5f64657374696e6174696f6e7c733a313a2231223b726563765f737570706c6965727c693a2d313b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d),
('ospos_session:413c77910eeee0fb8eb83dfd4912b359', '::1', '2026-06-16 09:03:09', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313630303538363b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a223334326335613539303338653633613765373862616339323633346134623433223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:64fd25cf93c816d90f741d05bb4ace44', '::1', '2026-06-16 08:56:19', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313630303137333b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a223262373832653330656165616536386137343331616565646365366434303335223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:6c0a842c5cc08c3abbf265a8325e781b', '::1', '2026-06-16 08:43:54', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393433333b637372665f6f73706f735f76347c733a33323a223931663263396438323536306565333533636461373564396562353261353866223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b),
('ospos_session:7652fd5933365d773e9bf552ed0615f3', '::1', '2026-06-16 08:43:36', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393431363b637372665f6f73706f735f76347c733a33323a223938333234336539656165363733323466633435333061366435666237386630223b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a33383a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f223b),
('ospos_session:78c54d645fe6b7c11ec843d7e0c55ed5', '::1', '2026-06-16 08:31:44', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539383730343b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:7c9bdfcb868737c28044f343b353b77e', '::1', '2026-06-16 08:43:53', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393433333b637372665f6f73706f735f76347c733a33323a223261633634643439646430636335303134333061613766633963336163383561223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b),
('ospos_session:8180b2a044cc317a01fb94231026478a', '::1', '2026-06-16 08:43:37', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393431373b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:84d561cbf01f1fede7ae4bf80ba94df3', '::1', '2026-06-16 08:27:42', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539383235363b637372665f6f73706f735f76347c733a33323a223864363833613161613966663530666461343238383734326266396163303765223b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a33383a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:88ff2fcecc579e15ba965ee759c32b5a', '::1', '2026-06-16 08:43:37', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393431373b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:89cc80017a8aa627c4d32810af6da40b', '::1', '2026-06-19 11:18:05', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313836373838323b637372665f6f73706f735f76347c733a33323a223961313963313537363865656238346632323864303239623336653039626261223b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:9702c5c6fa970f137033e0275f749929', '::1', '2026-06-16 08:51:10', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539393836393b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b706572736f6e5f69647c733a313a2231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a223666353261623230616237366663626663396337393761313363336236323239223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:9b484eacafe2aae8b886186b85964cff', '::1', '2026-06-19 12:10:55', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313837313034313b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a36303a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f73757065722d61646d696e2f627573696e6573736573223b637372665f6f73706f735f76347c733a33323a223462376265343366386162393930306539333035656530323034303961326161223b706c6174666f726d5f61646d696e5f69647c693a313b),
('ospos_session:9f736188bad056ba8b4b1793829e51b9', '::1', '2026-06-16 08:31:45', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313539383730353b637372665f6f73706f735f76347c733a33323a223066643164376161636163613636353164373166363335366131643539353231223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b),
('ospos_session:c84049a64d4472b994bf18b88a61c2be', '::1', '2026-06-16 09:34:53', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313630323439313b74656e616e745f69647c693a313b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a343a2231303233223b74656e616e745f64625f686f73746e616d657c733a393a226c6f63616c686f7374223b74656e616e745f64625f706f72747c693a333330363b74656e616e745f64625f6e616d657c733a353a227762706f73223b74656e616e745f64625f757365726e616d657c733a303a22223b74656e616e745f64625f70617373776f72647c733a303a22223b74656e616e745f64625f7072656669787c733a363a227762706f735f223b637372665f6f73706f735f76347c733a33323a223932383462613566386464623533326366326137363734323630313563386363223b6d656e755f67726f75707c733a343a22686f6d65223b);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_stock_locations`
--

CREATE TABLE `wbpos_stock_locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_stock_locations`
--

INSERT INTO `wbpos_stock_locations` (`location_id`, `location_name`, `deleted`, `tenant_id`) VALUES
(1, 'Main Store', 0, 1),
(1002, 'Warehouse A', 0, 1),
(1003, 'Warehouse B', 0, 1),
(1004, 'Front Counter', 0, 1),
(1005, 'Back Room', 0, 1),
(1006, 'Online Stock', 0, 1),
(1007, 'Main Store', 0, 2),
(1008, 'Warehouse A', 0, 2),
(1009, 'Warehouse B', 0, 2),
(1010, 'Front Counter', 0, 2),
(1011, 'Back Room', 0, 2),
(1012, 'Online Stock', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_subscriptions`
--

CREATE TABLE `wbpos_subscriptions` (
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('trialing','active','past_due','cancelled') NOT NULL DEFAULT 'trialing',
  `trial_ends_at` datetime DEFAULT NULL,
  `period_start` datetime NOT NULL,
  `period_end` datetime NOT NULL,
  `cancel_at_period_end` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_subscriptions`
--

INSERT INTO `wbpos_subscriptions` (`subscription_id`, `tenant_id`, `plan_id`, `status`, `trial_ends_at`, `period_start`, `period_end`, `cancel_at_period_end`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', NULL, '2026-06-16 15:07:20', '2027-06-16 15:07:20', 0, '2026-06-16 08:07:20', NULL),
(2, 2, 2, 'active', NULL, '2026-06-16 07:08:22', '2026-07-16 07:08:22', 0, '2026-06-16 11:08:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_subscription_requests`
--

CREATE TABLE `wbpos_subscription_requests` (
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `tenant_code` varchar(64) NOT NULL,
  `owner_first_name` varchar(100) NOT NULL,
  `owner_last_name` varchar(100) NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `owner_phone` varchar(255) DEFAULT NULL,
  `owner_username` varchar(50) NOT NULL,
  `owner_password_hash` varchar(255) NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `payment_reference` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `reviewed_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_subscription_requests`
--

INSERT INTO `wbpos_subscription_requests` (`request_id`, `company_name`, `tenant_code`, `owner_first_name`, `owner_last_name`, `owner_email`, `owner_phone`, `owner_username`, `owner_password_hash`, `plan_id`, `payment_reference`, `status`, `notes`, `reviewed_by_admin_id`, `created_at`, `reviewed_at`) VALUES
(1, 'Acme Retail Shop', 'blue-ocean', 'Maria', 'Lopez', 'maria@blueocean.cafe', '098-777-1234', 'maria_blue', '$2y$10$3TmH69FAwUINRA7sKHkhrOrmOPFlbCfiT3rluA.me/Nog3mu.A1EC', 2, 'PAY-778899', 'approved', 'Submitted from website signup flow', 1, '2026-06-16 11:07:15', '2026-06-16 07:08:22');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_suppliers`
--

CREATE TABLE `wbpos_suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `agency_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `tax_id` varchar(32) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `category` tinyint(1) NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `wbpos_suppliers`
--

INSERT INTO `wbpos_suppliers` (`person_id`, `company_name`, `agency_name`, `account_number`, `tax_id`, `deleted`, `category`, `tenant_id`) VALUES
(1001, 'Fresh Foods Ltd', 'Fresh Foods', 'SUP-001', '', 0, 0, 1),
(1002, 'Bakery Co', 'Bakery Co', 'SUP-002', '', 0, 0, 1),
(1003, 'Dairy Farm Inc', 'Dairy Farm', 'SUP-003', '', 0, 0, 1),
(1004, 'Grocery Hub', 'Grocery Hub', 'SUP-004', '', 0, 0, 1),
(1005, 'Home Goods Supply', 'Home Goods', 'SUP-005', '', 0, 0, 1),
(1006, 'Tech Parts LLC', 'Tech Parts', 'SUP-006', '', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tax_categories`
--

CREATE TABLE `wbpos_tax_categories` (
  `tax_category_id` int(10) NOT NULL,
  `tax_category` varchar(32) NOT NULL,
  `tax_group_sequence` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tax_codes`
--

CREATE TABLE `wbpos_tax_codes` (
  `tax_code_id` int(11) NOT NULL,
  `tax_code` varchar(32) NOT NULL,
  `tax_code_name` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tax_jurisdictions`
--

CREATE TABLE `wbpos_tax_jurisdictions` (
  `jurisdiction_id` int(11) NOT NULL,
  `jurisdiction_name` varchar(255) DEFAULT NULL,
  `tax_group` varchar(32) NOT NULL,
  `tax_type` smallint(2) NOT NULL,
  `reporting_authority` varchar(255) DEFAULT NULL,
  `tax_group_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `cascade_sequence` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tax_rates`
--

CREATE TABLE `wbpos_tax_rates` (
  `tax_rate_id` int(11) NOT NULL,
  `rate_tax_code_id` int(11) NOT NULL,
  `rate_tax_category_id` int(10) NOT NULL,
  `rate_jurisdiction_id` int(11) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `tax_rounding_code` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tenants`
--

CREATE TABLE `wbpos_tenants` (
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_code` varchar(64) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `status` enum('active','suspended','cancelled') NOT NULL DEFAULT 'active',
  `timezone` varchar(64) NOT NULL DEFAULT 'UTC',
  `currency_code` char(3) NOT NULL DEFAULT 'USD',
  `db_hostname` varchar(190) DEFAULT NULL,
  `db_port` int(11) DEFAULT NULL,
  `db_name` varchar(190) DEFAULT NULL,
  `db_username` varchar(190) DEFAULT NULL,
  `db_password` varchar(255) DEFAULT NULL,
  `db_prefix` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_tenants`
--

INSERT INTO `wbpos_tenants` (`tenant_id`, `tenant_code`, `company_name`, `status`, `timezone`, `currency_code`, `db_hostname`, `db_port`, `db_name`, `db_username`, `db_password`, `db_prefix`, `created_at`, `updated_at`) VALUES
(1, 'default', 'WBPOS Demo Store', 'active', 'America/New_York', 'USD', NULL, NULL, 'wbpos', NULL, NULL, 'wbpos_', '2026-06-16 08:07:20', NULL),
(2, 'blue-ocean', 'Acme Retail Shop', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 11:08:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tenant_config`
--

CREATE TABLE `wbpos_tenant_config` (
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_tenant_config`
--

INSERT INTO `wbpos_tenant_config` (`tenant_id`, `config_key`, `config_value`) VALUES
(1, 'address', '100 Main Street'),
(1, 'company', 'WBPOS Demo Store'),
(1, 'company_logo', 'photo_2025-08-19_13-20-47.jpg'),
(1, 'currency_code', 'USD'),
(1, 'email', 'admin@wbpos.demo'),
(1, 'fax', ''),
(1, 'phone', '555-555-0100'),
(1, 'return_policy', 'Returns within 30 days with receipt.'),
(1, 'timezone', 'America/New_York'),
(1, 'website', ''),
(2, 'address', '100 Main Street'),
(2, 'company', 'Acme Retail Shop'),
(2, 'company_logo', 'photo_2025-08-19_13-20-47.jpg'),
(2, 'currency_code', 'USD'),
(2, 'email', 'admin@wbpos.demo'),
(2, 'fax', ''),
(2, 'phone', '555-555-0100'),
(2, 'return_policy', 'Returns within 30 days with receipt.'),
(2, 'timezone', 'UTC'),
(2, 'website', '');

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tenant_domains`
--

CREATE TABLE `wbpos_tenant_domains` (
  `domain_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wbpos_tenant_users`
--

CREATE TABLE `wbpos_tenant_users` (
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `person_id` int(10) NOT NULL,
  `tenant_role` enum('owner','admin','manager','cashier') NOT NULL DEFAULT 'cashier',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wbpos_tenant_users`
--

INSERT INTO `wbpos_tenant_users` (`tenant_id`, `person_id`, `tenant_role`, `is_active`, `created_at`) VALUES
(1, 1, 'owner', 1, '2026-06-16 08:07:20'),
(2, 1026, 'owner', 1, '2026-06-16 11:08:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wbpos_app_config`
--
ALTER TABLE `wbpos_app_config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `wbpos_attribute_definitions`
--
ALTER TABLE `wbpos_attribute_definitions`
  ADD PRIMARY KEY (`definition_id`),
  ADD KEY `definition_fk` (`definition_fk`),
  ADD KEY `definition_name` (`definition_name`),
  ADD KEY `definition_type` (`definition_type`),
  ADD KEY `idx_attribute_definitions_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_attribute_links`
--
ALTER TABLE `wbpos_attribute_links`
  ADD UNIQUE KEY `attribute_links_uq1` (`attribute_id`,`definition_id`,`item_id`,`sale_id`,`receiving_id`),
  ADD UNIQUE KEY `attribute_links_uq2` (`item_id`,`receiving_id`,`sale_id`,`definition_id`,`attribute_id`),
  ADD UNIQUE KEY `attribute_links_uq3` (`generated_unique_column`),
  ADD KEY `attribute_id` (`attribute_id`),
  ADD KEY `definition_id` (`definition_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `receiving_id` (`receiving_id`);

--
-- Indexes for table `wbpos_attribute_values`
--
ALTER TABLE `wbpos_attribute_values`
  ADD PRIMARY KEY (`attribute_id`),
  ADD UNIQUE KEY `attribute_value` (`attribute_value`),
  ADD UNIQUE KEY `attribute_date` (`attribute_date`),
  ADD UNIQUE KEY `attribute_decimal` (`attribute_decimal`);

--
-- Indexes for table `wbpos_cash_up`
--
ALTER TABLE `wbpos_cash_up`
  ADD PRIMARY KEY (`cashup_id`),
  ADD KEY `open_employee_id` (`open_employee_id`),
  ADD KEY `close_employee_id` (`close_employee_id`),
  ADD KEY `idx_cash_up_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_customers`
--
ALTER TABLE `wbpos_customers`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `sales_tax_code_id` (`sales_tax_code_id`),
  ADD KEY `account_number` (`account_number`),
  ADD KEY `company_name` (`company_name`),
  ADD KEY `idx_customers_tenant_id` (`tenant_id`),
  ADD KEY `idx_customers_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `wbpos_customers_packages`
--
ALTER TABLE `wbpos_customers_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `wbpos_customers_points`
--
ALTER TABLE `wbpos_customers_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `wbpos_dinner_tables`
--
ALTER TABLE `wbpos_dinner_tables`
  ADD PRIMARY KEY (`dinner_table_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `wbpos_employees`
--
ALTER TABLE `wbpos_employees`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_employees_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_expenses`
--
ALTER TABLE `wbpos_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `expense_category_id` (`expense_category_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `date` (`date`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `amount` (`amount`),
  ADD KEY `wbpos_expenses_ibfk_3` (`supplier_id`),
  ADD KEY `idx_expenses_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_expense_categories`
--
ALTER TABLE `wbpos_expense_categories`
  ADD PRIMARY KEY (`expense_category_id`),
  ADD UNIQUE KEY `uk_expense_categories_tenant_name` (`tenant_id`,`category_name`),
  ADD KEY `category_description` (`category_description`),
  ADD KEY `idx_expense_categories_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_giftcards`
--
ALTER TABLE `wbpos_giftcards`
  ADD PRIMARY KEY (`giftcard_id`),
  ADD UNIQUE KEY `uk_giftcards_tenant_number` (`tenant_id`,`giftcard_number`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `idx_giftcards_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_grants`
--
ALTER TABLE `wbpos_grants`
  ADD PRIMARY KEY (`permission_id`,`person_id`),
  ADD KEY `wbpos_grants_ibfk_2` (`person_id`);

--
-- Indexes for table `wbpos_inventory`
--
ALTER TABLE `wbpos_inventory`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `trans_items` (`trans_items`),
  ADD KEY `trans_user` (`trans_user`),
  ADD KEY `trans_location` (`trans_location`),
  ADD KEY `trans_date` (`trans_date`),
  ADD KEY `trans_items_trans_date` (`trans_items`,`trans_date`),
  ADD KEY `idx_inventory_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_invoices`
--
ALTER TABLE `wbpos_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `uk_invoices_tenant_number` (`tenant_id`,`invoice_number`),
  ADD KEY `idx_invoices_subscription` (`subscription_id`),
  ADD KEY `idx_invoices_tenant_status` (`tenant_id`,`status`);

--
-- Indexes for table `wbpos_invoice_payments`
--
ALTER TABLE `wbpos_invoice_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_invoice_payments_tenant` (`tenant_id`),
  ADD KEY `idx_invoice_payments_invoice` (`invoice_id`),
  ADD KEY `idx_invoice_payments_provider_ref` (`provider`,`provider_payment_id`);

--
-- Indexes for table `wbpos_items`
--
ALTER TABLE `wbpos_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `items_uq1` (`supplier_id`,`item_id`,`deleted`,`item_type`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `item_number` (`item_number`),
  ADD KEY `deleted` (`deleted`,`item_type`),
  ADD KEY `item_id` (`item_id`,`deleted`),
  ADD KEY `idx_items_tenant_id` (`tenant_id`),
  ADD KEY `idx_items_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `wbpos_items_taxes`
--
ALTER TABLE `wbpos_items_taxes`
  ADD PRIMARY KEY (`item_id`,`name`,`percent`),
  ADD KEY `idx_items_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_item_kits`
--
ALTER TABLE `wbpos_item_kits`
  ADD PRIMARY KEY (`item_kit_id`),
  ADD KEY `item_kit_number` (`item_kit_number`),
  ADD KEY `name` (`name`,`description`),
  ADD KEY `idx_item_kits_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_item_kit_items`
--
ALTER TABLE `wbpos_item_kit_items`
  ADD PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`),
  ADD KEY `wbpos_item_kit_items_ibfk_2` (`item_id`),
  ADD KEY `idx_item_kit_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_item_quantities`
--
ALTER TABLE `wbpos_item_quantities`
  ADD PRIMARY KEY (`item_id`,`location_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `idx_item_quantities_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_migrations`
--
ALTER TABLE `wbpos_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wbpos_modules`
--
ALTER TABLE `wbpos_modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  ADD UNIQUE KEY `name_lang_key` (`name_lang_key`);

--
-- Indexes for table `wbpos_people`
--
ALTER TABLE `wbpos_people`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `email` (`email`),
  ADD KEY `first_name` (`first_name`,`last_name`,`email`,`phone_number`),
  ADD KEY `idx_people_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_permissions`
--
ALTER TABLE `wbpos_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `wbpos_permissions_ibfk_2` (`location_id`);

--
-- Indexes for table `wbpos_plans`
--
ALTER TABLE `wbpos_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD UNIQUE KEY `uk_plans_code` (`plan_code`);

--
-- Indexes for table `wbpos_platform_admins`
--
ALTER TABLE `wbpos_platform_admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uk_platform_admins_username` (`username`);

--
-- Indexes for table `wbpos_receivings`
--
ALTER TABLE `wbpos_receivings`
  ADD PRIMARY KEY (`receiving_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reference` (`reference`),
  ADD KEY `receiving_time` (`receiving_time`),
  ADD KEY `idx_receivings_tenant_id` (`tenant_id`),
  ADD KEY `idx_receivings_tenant_date` (`tenant_id`,`receiving_time`);

--
-- Indexes for table `wbpos_receivings_items`
--
ALTER TABLE `wbpos_receivings_items`
  ADD PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_receivings_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_sales`
--
ALTER TABLE `wbpos_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `sale_time` (`sale_time`),
  ADD KEY `dinner_table_id` (`dinner_table_id`),
  ADD KEY `idx_sales_tenant_id` (`tenant_id`),
  ADD KEY `idx_sales_tenant_date` (`tenant_id`,`sale_time`);

--
-- Indexes for table `wbpos_sales_items`
--
ALTER TABLE `wbpos_sales_items`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_location` (`item_location`),
  ADD KEY `idx_sales_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_sales_items_taxes`
--
ALTER TABLE `wbpos_sales_items_taxes`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_sales_items_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_sales_payments`
--
ALTER TABLE `wbpos_sales_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_sale` (`sale_id`,`payment_type`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payment_time` (`payment_time`),
  ADD KEY `idx_sales_payments_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_sales_reward_points`
--
ALTER TABLE `wbpos_sales_reward_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `wbpos_sales_taxes`
--
ALTER TABLE `wbpos_sales_taxes`
  ADD PRIMARY KEY (`sales_taxes_id`),
  ADD KEY `print_sequence` (`sale_id`,`print_sequence`,`tax_group`),
  ADD KEY `idx_sales_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_sessions`
--
ALTER TABLE `wbpos_sessions`
  ADD PRIMARY KEY (`id`,`ip_address`),
  ADD KEY `wbpos_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `wbpos_stock_locations`
--
ALTER TABLE `wbpos_stock_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `idx_locations_tenant_id` (`tenant_id`);

--
-- Indexes for table `wbpos_subscriptions`
--
ALTER TABLE `wbpos_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_subscriptions_tenant_status` (`tenant_id`,`status`),
  ADD KEY `idx_subscriptions_plan` (`plan_id`);

--
-- Indexes for table `wbpos_subscription_requests`
--
ALTER TABLE `wbpos_subscription_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_subscription_requests_status` (`status`),
  ADD KEY `idx_subscription_requests_plan` (`plan_id`),
  ADD KEY `idx_subscription_requests_code` (`tenant_code`);

--
-- Indexes for table `wbpos_suppliers`
--
ALTER TABLE `wbpos_suppliers`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `category` (`category`),
  ADD KEY `company_name` (`company_name`,`deleted`),
  ADD KEY `idx_suppliers_tenant_id` (`tenant_id`),
  ADD KEY `idx_suppliers_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `wbpos_tax_categories`
--
ALTER TABLE `wbpos_tax_categories`
  ADD PRIMARY KEY (`tax_category_id`);

--
-- Indexes for table `wbpos_tax_codes`
--
ALTER TABLE `wbpos_tax_codes`
  ADD PRIMARY KEY (`tax_code_id`);

--
-- Indexes for table `wbpos_tax_jurisdictions`
--
ALTER TABLE `wbpos_tax_jurisdictions`
  ADD PRIMARY KEY (`jurisdiction_id`),
  ADD UNIQUE KEY `tax_jurisdictions_uq1` (`tax_group`);

--
-- Indexes for table `wbpos_tax_rates`
--
ALTER TABLE `wbpos_tax_rates`
  ADD PRIMARY KEY (`tax_rate_id`),
  ADD KEY `rate_tax_category_id` (`rate_tax_category_id`),
  ADD KEY `rate_tax_code_id` (`rate_tax_code_id`),
  ADD KEY `rate_jurisdiction_id` (`rate_jurisdiction_id`);

--
-- Indexes for table `wbpos_tenants`
--
ALTER TABLE `wbpos_tenants`
  ADD PRIMARY KEY (`tenant_id`),
  ADD UNIQUE KEY `uk_tenants_code` (`tenant_code`);

--
-- Indexes for table `wbpos_tenant_config`
--
ALTER TABLE `wbpos_tenant_config`
  ADD PRIMARY KEY (`tenant_id`,`config_key`);

--
-- Indexes for table `wbpos_tenant_domains`
--
ALTER TABLE `wbpos_tenant_domains`
  ADD PRIMARY KEY (`domain_id`),
  ADD UNIQUE KEY `uk_tenant_domains_domain` (`domain`),
  ADD KEY `idx_tenant_domains_tenant` (`tenant_id`);

--
-- Indexes for table `wbpos_tenant_users`
--
ALTER TABLE `wbpos_tenant_users`
  ADD PRIMARY KEY (`tenant_id`,`person_id`),
  ADD KEY `idx_tenant_users_person` (`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wbpos_attribute_definitions`
--
ALTER TABLE `wbpos_attribute_definitions`
  MODIFY `definition_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_attribute_values`
--
ALTER TABLE `wbpos_attribute_values`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_cash_up`
--
ALTER TABLE `wbpos_cash_up`
  MODIFY `cashup_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_customers_packages`
--
ALTER TABLE `wbpos_customers_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wbpos_customers_points`
--
ALTER TABLE `wbpos_customers_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_dinner_tables`
--
ALTER TABLE `wbpos_dinner_tables`
  MODIFY `dinner_table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_expenses`
--
ALTER TABLE `wbpos_expenses`
  MODIFY `expense_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_expense_categories`
--
ALTER TABLE `wbpos_expense_categories`
  MODIFY `expense_category_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT for table `wbpos_giftcards`
--
ALTER TABLE `wbpos_giftcards`
  MODIFY `giftcard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_inventory`
--
ALTER TABLE `wbpos_inventory`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013;

--
-- AUTO_INCREMENT for table `wbpos_invoices`
--
ALTER TABLE `wbpos_invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_invoice_payments`
--
ALTER TABLE `wbpos_invoice_payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_items`
--
ALTER TABLE `wbpos_items`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT for table `wbpos_item_kits`
--
ALTER TABLE `wbpos_item_kits`
  MODIFY `item_kit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_migrations`
--
ALTER TABLE `wbpos_migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `wbpos_people`
--
ALTER TABLE `wbpos_people`
  MODIFY `person_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1028;

--
-- AUTO_INCREMENT for table `wbpos_plans`
--
ALTER TABLE `wbpos_plans`
  MODIFY `plan_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wbpos_platform_admins`
--
ALTER TABLE `wbpos_platform_admins`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wbpos_receivings`
--
ALTER TABLE `wbpos_receivings`
  MODIFY `receiving_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_sales`
--
ALTER TABLE `wbpos_sales`
  MODIFY `sale_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_sales_payments`
--
ALTER TABLE `wbpos_sales_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `wbpos_sales_reward_points`
--
ALTER TABLE `wbpos_sales_reward_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_sales_taxes`
--
ALTER TABLE `wbpos_sales_taxes`
  MODIFY `sales_taxes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_stock_locations`
--
ALTER TABLE `wbpos_stock_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013;

--
-- AUTO_INCREMENT for table `wbpos_subscriptions`
--
ALTER TABLE `wbpos_subscriptions`
  MODIFY `subscription_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wbpos_subscription_requests`
--
ALTER TABLE `wbpos_subscription_requests`
  MODIFY `request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wbpos_tax_categories`
--
ALTER TABLE `wbpos_tax_categories`
  MODIFY `tax_category_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_tax_codes`
--
ALTER TABLE `wbpos_tax_codes`
  MODIFY `tax_code_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_tax_jurisdictions`
--
ALTER TABLE `wbpos_tax_jurisdictions`
  MODIFY `jurisdiction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_tax_rates`
--
ALTER TABLE `wbpos_tax_rates`
  MODIFY `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wbpos_tenants`
--
ALTER TABLE `wbpos_tenants`
  MODIFY `tenant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wbpos_tenant_domains`
--
ALTER TABLE `wbpos_tenant_domains`
  MODIFY `domain_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wbpos_attribute_definitions`
--
ALTER TABLE `wbpos_attribute_definitions`
  ADD CONSTRAINT `fk_wbpos_attribute_definitions_ibfk_1` FOREIGN KEY (`definition_fk`) REFERENCES `wbpos_attribute_definitions` (`definition_id`);

--
-- Constraints for table `wbpos_attribute_links`
--
ALTER TABLE `wbpos_attribute_links`
  ADD CONSTRAINT `wbpos_attribute_links_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `wbpos_attribute_definitions` (`definition_id`),
  ADD CONSTRAINT `wbpos_attribute_links_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `wbpos_attribute_values` (`attribute_id`),
  ADD CONSTRAINT `wbpos_attribute_links_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`),
  ADD CONSTRAINT `wbpos_attribute_links_ibfk_4` FOREIGN KEY (`receiving_id`) REFERENCES `wbpos_receivings` (`receiving_id`),
  ADD CONSTRAINT `wbpos_attribute_links_ibfk_5` FOREIGN KEY (`sale_id`) REFERENCES `wbpos_sales` (`sale_id`);

--
-- Constraints for table `wbpos_cash_up`
--
ALTER TABLE `wbpos_cash_up`
  ADD CONSTRAINT `wbpos_cash_up_ibfk_1` FOREIGN KEY (`open_employee_id`) REFERENCES `wbpos_employees` (`person_id`),
  ADD CONSTRAINT `wbpos_cash_up_ibfk_2` FOREIGN KEY (`close_employee_id`) REFERENCES `wbpos_employees` (`person_id`);

--
-- Constraints for table `wbpos_customers`
--
ALTER TABLE `wbpos_customers`
  ADD CONSTRAINT `fk_customers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_customers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `wbpos_people` (`person_id`),
  ADD CONSTRAINT `wbpos_customers_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `wbpos_customers_packages` (`package_id`),
  ADD CONSTRAINT `wbpos_customers_ibfk_3` FOREIGN KEY (`sales_tax_code_id`) REFERENCES `wbpos_tax_codes` (`tax_code_id`);

--
-- Constraints for table `wbpos_customers_points`
--
ALTER TABLE `wbpos_customers_points`
  ADD CONSTRAINT `wbpos_customers_points_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `wbpos_customers` (`person_id`),
  ADD CONSTRAINT `wbpos_customers_points_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `wbpos_customers_packages` (`package_id`),
  ADD CONSTRAINT `wbpos_customers_points_ibfk_3` FOREIGN KEY (`sale_id`) REFERENCES `wbpos_sales` (`sale_id`);

--
-- Constraints for table `wbpos_employees`
--
ALTER TABLE `wbpos_employees`
  ADD CONSTRAINT `fk_employees_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_employees_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `wbpos_people` (`person_id`);

--
-- Constraints for table `wbpos_expenses`
--
ALTER TABLE `wbpos_expenses`
  ADD CONSTRAINT `wbpos_expenses_ibfk_1` FOREIGN KEY (`expense_category_id`) REFERENCES `wbpos_expense_categories` (`expense_category_id`),
  ADD CONSTRAINT `wbpos_expenses_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `wbpos_employees` (`person_id`),
  ADD CONSTRAINT `wbpos_expenses_ibfk_3` FOREIGN KEY (`supplier_id`) REFERENCES `wbpos_suppliers` (`person_id`);

--
-- Constraints for table `wbpos_giftcards`
--
ALTER TABLE `wbpos_giftcards`
  ADD CONSTRAINT `wbpos_giftcards_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `wbpos_people` (`person_id`);

--
-- Constraints for table `wbpos_grants`
--
ALTER TABLE `wbpos_grants`
  ADD CONSTRAINT `wbpos_grants_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `wbpos_permissions` (`permission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wbpos_grants_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `wbpos_employees` (`person_id`) ON DELETE CASCADE;

--
-- Constraints for table `wbpos_inventory`
--
ALTER TABLE `wbpos_inventory`
  ADD CONSTRAINT `wbpos_inventory_ibfk_1` FOREIGN KEY (`trans_items`) REFERENCES `wbpos_items` (`item_id`),
  ADD CONSTRAINT `wbpos_inventory_ibfk_2` FOREIGN KEY (`trans_user`) REFERENCES `wbpos_employees` (`person_id`),
  ADD CONSTRAINT `wbpos_inventory_ibfk_3` FOREIGN KEY (`trans_location`) REFERENCES `wbpos_stock_locations` (`location_id`);

--
-- Constraints for table `wbpos_invoices`
--
ALTER TABLE `wbpos_invoices`
  ADD CONSTRAINT `fk_invoices_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `wbpos_subscriptions` (`subscription_id`),
  ADD CONSTRAINT `fk_invoices_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_invoice_payments`
--
ALTER TABLE `wbpos_invoice_payments`
  ADD CONSTRAINT `fk_invoice_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `wbpos_invoices` (`invoice_id`),
  ADD CONSTRAINT `fk_invoice_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_items`
--
ALTER TABLE `wbpos_items`
  ADD CONSTRAINT `fk_items_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `wbpos_suppliers` (`person_id`);

--
-- Constraints for table `wbpos_items_taxes`
--
ALTER TABLE `wbpos_items_taxes`
  ADD CONSTRAINT `wbpos_items_taxes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `wbpos_item_kits`
--
ALTER TABLE `wbpos_item_kits`
  ADD CONSTRAINT `fk_item_kits_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_item_kit_items`
--
ALTER TABLE `wbpos_item_kit_items`
  ADD CONSTRAINT `wbpos_item_kit_items_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `wbpos_item_kits` (`item_kit_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wbpos_item_kit_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `wbpos_item_quantities`
--
ALTER TABLE `wbpos_item_quantities`
  ADD CONSTRAINT `wbpos_item_quantities_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`),
  ADD CONSTRAINT `wbpos_item_quantities_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `wbpos_stock_locations` (`location_id`);

--
-- Constraints for table `wbpos_people`
--
ALTER TABLE `wbpos_people`
  ADD CONSTRAINT `fk_people_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_permissions`
--
ALTER TABLE `wbpos_permissions`
  ADD CONSTRAINT `wbpos_permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `wbpos_modules` (`module_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wbpos_permissions_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `wbpos_stock_locations` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `wbpos_receivings`
--
ALTER TABLE `wbpos_receivings`
  ADD CONSTRAINT `fk_receivings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_receivings_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `wbpos_employees` (`person_id`),
  ADD CONSTRAINT `wbpos_receivings_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `wbpos_suppliers` (`person_id`);

--
-- Constraints for table `wbpos_receivings_items`
--
ALTER TABLE `wbpos_receivings_items`
  ADD CONSTRAINT `wbpos_receivings_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`),
  ADD CONSTRAINT `wbpos_receivings_items_ibfk_2` FOREIGN KEY (`receiving_id`) REFERENCES `wbpos_receivings` (`receiving_id`);

--
-- Constraints for table `wbpos_sales`
--
ALTER TABLE `wbpos_sales`
  ADD CONSTRAINT `fk_sales_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_sales_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `wbpos_employees` (`person_id`),
  ADD CONSTRAINT `wbpos_sales_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `wbpos_customers` (`person_id`),
  ADD CONSTRAINT `wbpos_sales_ibfk_3` FOREIGN KEY (`dinner_table_id`) REFERENCES `wbpos_dinner_tables` (`dinner_table_id`);

--
-- Constraints for table `wbpos_sales_items`
--
ALTER TABLE `wbpos_sales_items`
  ADD CONSTRAINT `wbpos_sales_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`),
  ADD CONSTRAINT `wbpos_sales_items_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `wbpos_sales` (`sale_id`),
  ADD CONSTRAINT `wbpos_sales_items_ibfk_3` FOREIGN KEY (`item_location`) REFERENCES `wbpos_stock_locations` (`location_id`);

--
-- Constraints for table `wbpos_sales_items_taxes`
--
ALTER TABLE `wbpos_sales_items_taxes`
  ADD CONSTRAINT `wbpos_sales_items_taxes_ibfk_1` FOREIGN KEY (`sale_id`,`item_id`,`line`) REFERENCES `wbpos_sales_items` (`sale_id`, `item_id`, `line`),
  ADD CONSTRAINT `wbpos_sales_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `wbpos_items` (`item_id`);

--
-- Constraints for table `wbpos_sales_payments`
--
ALTER TABLE `wbpos_sales_payments`
  ADD CONSTRAINT `wbpos_sales_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `wbpos_sales` (`sale_id`),
  ADD CONSTRAINT `wbpos_sales_payments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `wbpos_employees` (`person_id`);

--
-- Constraints for table `wbpos_sales_reward_points`
--
ALTER TABLE `wbpos_sales_reward_points`
  ADD CONSTRAINT `wbpos_sales_reward_points_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `wbpos_sales` (`sale_id`);

--
-- Constraints for table `wbpos_stock_locations`
--
ALTER TABLE `wbpos_stock_locations`
  ADD CONSTRAINT `fk_stock_locations_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_subscriptions`
--
ALTER TABLE `wbpos_subscriptions`
  ADD CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `wbpos_plans` (`plan_id`),
  ADD CONSTRAINT `fk_subscriptions_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_subscription_requests`
--
ALTER TABLE `wbpos_subscription_requests`
  ADD CONSTRAINT `fk_subscription_requests_plan` FOREIGN KEY (`plan_id`) REFERENCES `wbpos_plans` (`plan_id`);

--
-- Constraints for table `wbpos_suppliers`
--
ALTER TABLE `wbpos_suppliers`
  ADD CONSTRAINT `fk_suppliers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`),
  ADD CONSTRAINT `wbpos_suppliers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `wbpos_people` (`person_id`);

--
-- Constraints for table `wbpos_tax_rates`
--
ALTER TABLE `wbpos_tax_rates`
  ADD CONSTRAINT `wbpos_tax_rates_ibfk_1` FOREIGN KEY (`rate_tax_category_id`) REFERENCES `wbpos_tax_categories` (`tax_category_id`),
  ADD CONSTRAINT `wbpos_tax_rates_ibfk_2` FOREIGN KEY (`rate_tax_code_id`) REFERENCES `wbpos_tax_codes` (`tax_code_id`),
  ADD CONSTRAINT `wbpos_tax_rates_ibfk_3` FOREIGN KEY (`rate_jurisdiction_id`) REFERENCES `wbpos_tax_jurisdictions` (`jurisdiction_id`);

--
-- Constraints for table `wbpos_tenant_config`
--
ALTER TABLE `wbpos_tenant_config`
  ADD CONSTRAINT `fk_tenant_config_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_tenant_domains`
--
ALTER TABLE `wbpos_tenant_domains`
  ADD CONSTRAINT `fk_tenant_domains_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);

--
-- Constraints for table `wbpos_tenant_users`
--
ALTER TABLE `wbpos_tenant_users`
  ADD CONSTRAINT `fk_tenant_users_person` FOREIGN KEY (`person_id`) REFERENCES `wbpos_people` (`person_id`),
  ADD CONSTRAINT `fk_tenant_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `wbpos_tenants` (`tenant_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
