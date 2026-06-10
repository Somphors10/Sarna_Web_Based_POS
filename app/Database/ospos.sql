-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2026 at 09:40 AM
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
-- Database: `ospos`
--

-- --------------------------------------------------------

--
-- Table structure for table `ospos_app_config`
--

CREATE TABLE `ospos_app_config` (
  `key` varchar(50) NOT NULL,
  `value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_app_config`
--

INSERT INTO `ospos_app_config` (`key`, `value`) VALUES
('account_number', ''),
('address', '123 Nowhere street'),
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
('company', 'Web-Based POS'),
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
('default_tax_1_name', ''),
('default_tax_1_rate', ''),
('default_tax_2_name', ''),
('default_tax_2_rate', ''),
('default_tax_category', 'Standard'),
('default_tax_code', ''),
('default_tax_jurisdiction', ''),
('default_tax_rate', '8'),
('derive_sale_quantity', '0'),
('dinner_table_enable', '0'),
('email', 'changeme@example.com'),
('email_receipt_check_behaviour', 'last'),
('enforce_privacy', '0'),
('fax', ''),
('financial_year', '1'),
('gcaptcha_enable', '0'),
('gcaptcha_secret_key', ''),
('gcaptcha_site_key', ''),
('giftcard_number', 'series'),
('image_allowed_types', 'gif,jpg,png'),
('image_max_height', '480'),
('image_max_size', '128'),
('image_max_width', '640'),
('include_hsn', '0'),
('invoice_default_comments', 'This is a default comment'),
('invoice_email_message', 'Dear {CU}, In attachment the receipt for sale {ISEQ}'),
('invoice_enable', '1'),
('invoice_type', 'invoice'),
('language', 'english'),
('language_code', 'en'),
('last_used_invoice_number', '0'),
('last_used_quote_number', '0'),
('last_used_work_order_number', '0'),
('lines_per_page', '25'),
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
('phone', '555-555-5555'),
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
('return_policy', 'Test'),
('sales_invoice_format', '{CO}'),
('sales_quote_format', 'Q%y{QSEQ:6}'),
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
-- Table structure for table `ospos_attribute_definitions`
--

CREATE TABLE `ospos_attribute_definitions` (
  `definition_id` int(10) NOT NULL,
  `definition_name` varchar(255) NOT NULL,
  `definition_type` varchar(45) NOT NULL,
  `definition_unit` varchar(16) DEFAULT NULL,
  `definition_flags` tinyint(1) NOT NULL,
  `definition_fk` int(10) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_attribute_definitions`
--

INSERT INTO `ospos_attribute_definitions` (`definition_id`, `definition_name`, `definition_type`, `definition_unit`, `definition_flags`, `definition_fk`, `deleted`, `tenant_id`) VALUES
(1, 'add', 'CHECKBOX', NULL, 7, NULL, 0, 1),
(2, 'ok', 'GROUP', NULL, 7, NULL, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_attribute_links`
--

CREATE TABLE `ospos_attribute_links` (
  `attribute_id` int(11) DEFAULT NULL,
  `definition_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `receiving_id` int(11) DEFAULT NULL,
  `generated_unique_column` varchar(255) GENERATED ALWAYS AS (case when `sale_id` is null and `receiving_id` is null and `item_id` is not null then concat(`definition_id`,'-',`item_id`) else NULL end) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_attribute_values`
--

CREATE TABLE `ospos_attribute_values` (
  `attribute_id` int(11) NOT NULL,
  `attribute_value` varchar(255) DEFAULT NULL,
  `attribute_date` date DEFAULT NULL,
  `attribute_decimal` decimal(7,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_cash_up`
--

CREATE TABLE `ospos_cash_up` (
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
-- Dumping data for table `ospos_cash_up`
--

INSERT INTO `ospos_cash_up` (`cashup_id`, `open_date`, `close_date`, `open_amount_cash`, `transfer_amount_cash`, `note`, `closed_amount_cash`, `closed_amount_card`, `closed_amount_check`, `closed_amount_total`, `description`, `open_employee_id`, `close_employee_id`, `deleted`, `closed_amount_due`, `tenant_id`) VALUES
(4, '2026-04-29 16:09:20', '2026-04-29 16:09:20', 0.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, '', 12, 12, 0, 0.00, 3),
(5, '2026-04-29 16:09:38', '2026-04-29 16:09:38', 0.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, '', 1, 1, 0, 0.00, 1),
(6, '2026-04-29 17:30:29', '2026-04-29 17:36:07', 10.00, 0.00, 1, 0.00, 0.00, 0.00, -10.00, '', 20, 20, 0, 0.00, 4),
(7, '2026-04-29 17:46:29', '2026-04-29 17:46:29', 0.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, '', 1, 1, 0, 0.00, 1),
(8, '2026-05-29 17:10:18', '2026-05-29 17:10:18', 0.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, '', 12, 12, 0, 0.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_customers`
--

CREATE TABLE `ospos_customers` (
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
-- Dumping data for table `ospos_customers`
--

INSERT INTO `ospos_customers` (`person_id`, `company_name`, `account_number`, `taxable`, `tax_id`, `sales_tax_code_id`, `package_id`, `points`, `deleted`, `discount`, `discount_type`, `date`, `employee_id`, `consent`, `tenant_id`) VALUES
(10, 'Trust', NULL, 0, '', NULL, NULL, NULL, 0, 0.00, 0, '2026-04-27 15:14:41', 1, 1, 1),
(13, NULL, NULL, 0, '', NULL, NULL, NULL, 0, 0.00, 0, '2026-04-27 20:27:16', 12, 1, 3),
(14, 'My Company Retail', 'CUST-00001', 1, '', NULL, NULL, NULL, 0, 0.00, 0, '2026-04-27 21:53:57', 12, 1, 3),
(21, NULL, 'CUST-00001', 1, '', NULL, NULL, NULL, 0, 0.00, 0, '2026-04-29 17:25:04', 20, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_customers_packages`
--

CREATE TABLE `ospos_customers_packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `points_percent` float NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_customers_packages`
--

INSERT INTO `ospos_customers_packages` (`package_id`, `package_name`, `points_percent`, `deleted`) VALUES
(1, 'Default', 0, 0),
(2, 'Bronze', 10, 0),
(3, 'Silver', 20, 0),
(4, 'Gold', 30, 0),
(5, 'Premium', 50, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_customers_points`
--

CREATE TABLE `ospos_customers_points` (
  `id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `points_earned` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_dinner_tables`
--

CREATE TABLE `ospos_dinner_tables` (
  `dinner_table_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_dinner_tables`
--

INSERT INTO `ospos_dinner_tables` (`dinner_table_id`, `name`, `status`, `deleted`) VALUES
(1, 'Delivery', 0, 0),
(2, 'Take Away', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_employees`
--

CREATE TABLE `ospos_employees` (
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
-- Dumping data for table `ospos_employees`
--

INSERT INTO `ospos_employees` (`username`, `password`, `person_id`, `deleted`, `hash_version`, `language`, `language_code`, `tenant_id`) VALUES
('admin', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1, 0, 2, NULL, NULL, 1),
('somphors', '$2y$10$MIMQndyE3U38UtZPQ/55JuYmOff09FV.er7vOnAgymof4Gp5FT2xi', 11, 0, 2, NULL, NULL, 2),
('myco_owner', '$2y$10$p/V7.0bW8JBORu9mv46cFO09mWnEpoPrW3HvnvjozRxGzjCHIUUH.', 12, 0, 2, NULL, NULL, 3),
('anhkeonho', '$2y$10$aeJE1LX6XZj27CU5xRNiJ..pz5thkeyc517RGHaXpKngt8JxWo62S', 20, 0, 2, NULL, NULL, 4),
('jungkook', '$2y$10$AO4m0K.ZCfKqQUuop/7rG.Ijoe6RkZK2jY4paLpMw0Ka1crbT5HCa', 22, 0, 2, '', '', 3),
('wonhee', '$2y$10$omd04u7F7DLwzFWYeF4gCOHw6tgAsRqxzAreqlu7dPbjeHZnp8BPu', 24, 0, 2, '', '', 3),
('sokunthea', '$2y$10$jw5rOWB5zseZTJziLALmCueShz1O1lO3isIfOMOwE3PfTdRkysee6', 25, 0, 2, NULL, NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_expenses`
--

CREATE TABLE `ospos_expenses` (
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
-- Dumping data for table `ospos_expenses`
--

INSERT INTO `ospos_expenses` (`expense_id`, `date`, `amount`, `payment_type`, `expense_category_id`, `description`, `employee_id`, `deleted`, `supplier_tax_code`, `tax_amount`, `supplier_id`, `tenant_id`) VALUES
(2, '2026-04-27 16:47:17', 5.00, 'Cash', 1, '', 1, 0, '', 0.00, NULL, 1),
(4, '2026-04-28 17:49:55', 10.00, 'Cash', 4, '', 12, 0, '', 0.00, NULL, 3),
(5, '2026-04-28 17:50:19', 120.00, 'Cash', 3, '', 1, 0, '', 0.00, NULL, 1),
(7, '2026-04-28 17:52:19', 0.00, 'Cash', 4, '', 12, 0, '', 0.00, NULL, 3),
(8, '2026-04-28 17:53:34', 0.00, 'Cash', 3, '', 1, 0, '', 0.00, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_expense_categories`
--

CREATE TABLE `ospos_expense_categories` (
  `expense_category_id` int(10) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_description` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_expense_categories`
--

INSERT INTO `ospos_expense_categories` (`expense_category_id`, `category_name`, `category_description`, `deleted`, `tenant_id`) VALUES
(1, 'Snack', 'nham mix k ban', 0, 1),
(3, 'Drink', 'Use for drink', 0, 1),
(4, 'Bag', '', 0, 3),
(5, 'test', '', 0, 1),
(7, 'ad', '', 0, 3),
(10, 'Snack', '', 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_giftcards`
--

CREATE TABLE `ospos_giftcards` (
  `record_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `giftcard_id` int(11) NOT NULL,
  `giftcard_number` varchar(255) DEFAULT NULL,
  `value` decimal(15,2) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `person_id` int(10) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_giftcards`
--

INSERT INTO `ospos_giftcards` (`record_time`, `giftcard_id`, `giftcard_number`, `value`, `deleted`, `person_id`, `tenant_id`) VALUES
('2026-04-28 18:04:03', 7, '1', 0.00, 0, 13, 3),
('2026-04-29 17:48:26', 11, '1', 0.00, 0, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_grants`
--

CREATE TABLE `ospos_grants` (
  `permission_id` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `menu_group` varchar(32) DEFAULT 'home'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_grants`
--

INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('attributes', 1, 'office'),
('attributes', 11, 'office'),
('attributes', 12, 'office'),
('attributes', 20, 'office'),
('attributes', 24, 'home'),
('attributes', 25, 'office'),
('cashups', 1, 'home'),
('cashups', 11, 'home'),
('cashups', 12, 'home'),
('cashups', 20, 'home'),
('cashups', 25, 'home'),
('config', 1, 'office'),
('config', 11, 'office'),
('config', 12, 'office'),
('config', 20, 'office'),
('config', 25, 'office'),
('customers', 1, 'home'),
('customers', 11, 'home'),
('customers', 12, 'home'),
('customers', 20, 'home'),
('customers', 25, 'home'),
('employees', 1, 'office'),
('employees', 11, 'office'),
('employees', 12, 'office'),
('employees', 20, 'office'),
('employees', 24, 'home'),
('employees', 25, 'office'),
('expenses', 1, 'home'),
('expenses', 11, 'home'),
('expenses', 12, 'home'),
('expenses', 20, 'home'),
('expenses', 24, 'home'),
('expenses', 25, 'home'),
('expenses_categories', 1, 'office'),
('expenses_categories', 11, 'office'),
('expenses_categories', 12, 'office'),
('expenses_categories', 20, 'office'),
('expenses_categories', 24, 'home'),
('expenses_categories', 25, 'office'),
('giftcards', 1, 'home'),
('giftcards', 11, 'home'),
('giftcards', 12, 'home'),
('giftcards', 20, 'home'),
('giftcards', 24, 'home'),
('giftcards', 25, 'home'),
('home', 1, 'office'),
('home', 11, 'office'),
('home', 12, 'office'),
('home', 20, 'office'),
('home', 22, 'home'),
('home', 25, 'office'),
('items', 1, 'home'),
('items', 11, 'home'),
('items', 12, 'home'),
('items', 20, 'home'),
('items', 24, 'home'),
('items', 25, 'home'),
('items_stock', 1, 'home'),
('items_stock', 11, 'home'),
('items_stock', 12, 'home'),
('items_stock', 20, 'home'),
('items_stock', 24, '--'),
('items_stock', 25, 'home'),
('item_kits', 1, 'home'),
('item_kits', 11, 'home'),
('item_kits', 12, 'home'),
('item_kits', 20, 'home'),
('item_kits', 25, 'home'),
('messages', 1, 'home'),
('messages', 11, 'home'),
('messages', 12, 'home'),
('messages', 20, 'home'),
('messages', 24, 'home'),
('messages', 25, 'home'),
('office', 1, 'home'),
('office', 11, 'home'),
('office', 12, 'home'),
('office', 20, 'home'),
('office', 25, 'home'),
('receivings', 1, 'home'),
('receivings', 11, 'home'),
('receivings', 12, 'home'),
('receivings', 20, 'home'),
('receivings', 25, 'home'),
('receivings_stock', 1, 'home'),
('receivings_stock', 11, 'home'),
('receivings_stock', 12, 'home'),
('receivings_stock', 20, 'home'),
('receivings_stock', 25, 'home'),
('reports', 1, 'home'),
('reports', 11, 'home'),
('reports', 12, 'home'),
('reports', 20, 'home'),
('reports', 22, 'home'),
('reports', 24, 'home'),
('reports', 25, 'home'),
('reports_categories', 1, 'home'),
('reports_categories', 11, 'home'),
('reports_categories', 12, 'home'),
('reports_categories', 20, 'home'),
('reports_categories', 22, '--'),
('reports_categories', 24, '--'),
('reports_categories', 25, 'home'),
('reports_customers', 1, 'home'),
('reports_customers', 11, 'home'),
('reports_customers', 12, 'home'),
('reports_customers', 20, 'home'),
('reports_customers', 22, '--'),
('reports_customers', 24, '--'),
('reports_customers', 25, 'home'),
('reports_discounts', 1, 'home'),
('reports_discounts', 11, 'home'),
('reports_discounts', 12, 'home'),
('reports_discounts', 20, 'home'),
('reports_discounts', 24, '--'),
('reports_discounts', 25, 'home'),
('reports_employees', 1, 'home'),
('reports_employees', 11, 'home'),
('reports_employees', 12, 'home'),
('reports_employees', 20, 'home'),
('reports_employees', 22, '--'),
('reports_employees', 25, 'home'),
('reports_expenses_categories', 1, 'home'),
('reports_expenses_categories', 11, 'home'),
('reports_expenses_categories', 12, 'home'),
('reports_expenses_categories', 20, 'home'),
('reports_expenses_categories', 22, '--'),
('reports_expenses_categories', 24, '--'),
('reports_expenses_categories', 25, 'home'),
('reports_inventory', 1, 'home'),
('reports_inventory', 11, 'home'),
('reports_inventory', 12, 'home'),
('reports_inventory', 20, 'home'),
('reports_inventory', 24, '--'),
('reports_inventory', 25, 'home'),
('reports_items', 1, 'home'),
('reports_items', 11, 'home'),
('reports_items', 12, 'home'),
('reports_items', 20, 'home'),
('reports_items', 25, 'home'),
('reports_payments', 1, 'home'),
('reports_payments', 11, 'home'),
('reports_payments', 12, 'home'),
('reports_payments', 20, 'home'),
('reports_payments', 25, 'home'),
('reports_receivings', 1, 'home'),
('reports_receivings', 11, 'home'),
('reports_receivings', 12, 'home'),
('reports_receivings', 20, 'home'),
('reports_receivings', 25, 'home'),
('reports_sales', 1, 'home'),
('reports_sales', 11, 'home'),
('reports_sales', 12, 'home'),
('reports_sales', 20, 'home'),
('reports_sales', 25, 'home'),
('reports_sales_taxes', 1, 'home'),
('reports_sales_taxes', 11, 'home'),
('reports_sales_taxes', 12, 'home'),
('reports_sales_taxes', 20, 'home'),
('reports_sales_taxes', 25, 'home'),
('reports_suppliers', 1, 'home'),
('reports_suppliers', 11, 'home'),
('reports_suppliers', 12, 'home'),
('reports_suppliers', 20, 'home'),
('reports_suppliers', 25, 'home'),
('reports_taxes', 1, 'home'),
('reports_taxes', 11, 'home'),
('reports_taxes', 12, 'home'),
('reports_taxes', 20, 'home'),
('reports_taxes', 25, 'home'),
('sales', 1, 'home'),
('sales', 11, 'home'),
('sales', 12, 'home'),
('sales', 20, 'home'),
('sales', 25, 'home'),
('sales_change_price', 1, '--'),
('sales_change_price', 11, '--'),
('sales_change_price', 12, '--'),
('sales_change_price', 20, '--'),
('sales_change_price', 25, '--'),
('sales_delete', 1, '--'),
('sales_delete', 11, '--'),
('sales_delete', 12, '--'),
('sales_delete', 20, '--'),
('sales_delete', 25, '--'),
('sales_stock', 1, 'home'),
('sales_stock', 11, 'home'),
('sales_stock', 12, 'home'),
('sales_stock', 20, 'home'),
('sales_stock', 25, 'home'),
('suppliers', 1, 'home'),
('suppliers', 11, 'home'),
('suppliers', 12, 'home'),
('suppliers', 20, 'home'),
('suppliers', 25, 'home'),
('taxes', 24, 'home');

-- --------------------------------------------------------

--
-- Table structure for table `ospos_inventory`
--

CREATE TABLE `ospos_inventory` (
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
-- Dumping data for table `ospos_inventory`
--

INSERT INTO `ospos_inventory` (`trans_id`, `trans_items`, `trans_user`, `trans_date`, `trans_comment`, `trans_location`, `trans_inventory`, `tenant_id`) VALUES
(1, 1, 1, '2026-04-27 16:21:28', 'Manual Edit of Quantity', 1, 0.000, 1),
(2, 2, 12, '2026-04-27 21:50:07', 'Manual Edit of Quantity', 1, 0.000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_invoices`
--

CREATE TABLE `ospos_invoices` (
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
-- Table structure for table `ospos_invoice_payments`
--

CREATE TABLE `ospos_invoice_payments` (
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
-- Table structure for table `ospos_items`
--

CREATE TABLE `ospos_items` (
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
-- Dumping data for table `ospos_items`
--

INSERT INTO `ospos_items` (`name`, `category`, `supplier_id`, `item_number`, `description`, `cost_price`, `unit_price`, `reorder_level`, `receiving_quantity`, `item_id`, `pic_filename`, `allow_alt_description`, `is_serialized`, `deleted`, `stock_type`, `item_type`, `tax_category_id`, `qty_per_pack`, `pack_name`, `low_sell_item_id`, `hsn_code`, `tenant_id`) VALUES
('Bread', 'Snack', NULL, '001', '', 10.00, 8.00, 1.000, 1.000, 1, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 1, '', 1),
('Coffee', 'drink', NULL, '003', '', 0.00, 0.00, 1.000, 1.000, 2, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 2, '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_items_taxes`
--

CREATE TABLE `ospos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_items_taxes`
--

INSERT INTO `ospos_items_taxes` (`item_id`, `name`, `percent`, `tenant_id`) VALUES
(1, '1', 0.010, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_kits`
--

CREATE TABLE `ospos_item_kits` (
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
-- Dumping data for table `ospos_item_kits`
--

INSERT INTO `ospos_item_kits` (`item_kit_id`, `item_kit_number`, `name`, `description`, `item_id`, `kit_discount`, `kit_discount_type`, `price_option`, `print_option`, `tenant_id`) VALUES
(2, '01', 'top-manager', '', 0, 0.00, 0, 0, 0, 1),
(3, '', 'Buket', '', 0, 0.00, 0, 0, 0, 3),
(4, '', 'sdf', '', 0, 0.00, 0, 0, 0, 1),
(5, '', 'top-manager', '', 0, 0.00, 0, 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_kit_items`
--

CREATE TABLE `ospos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `kit_sequence` int(3) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_quantities`
--

CREATE TABLE `ospos_item_quantities` (
  `item_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_item_quantities`
--

INSERT INTO `ospos_item_quantities` (`item_id`, `location_id`, `quantity`, `tenant_id`) VALUES
(1, 1, 0.000, 1),
(2, 1, 0.000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_migrations`
--

CREATE TABLE `ospos_migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ospos_migrations`
--

INSERT INTO `ospos_migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
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
-- Table structure for table `ospos_modules`
--

CREATE TABLE `ospos_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_modules`
--

INSERT INTO `ospos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
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
-- Table structure for table `ospos_people`
--

CREATE TABLE `ospos_people` (
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
-- Dumping data for table `ospos_people`
--

INSERT INTO `ospos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('John', 'Doe', NULL, '555-555-5555', 'changeme@example.com', 'Address 1', '', '', '', '', '', '', 1, 1),
('Srorn', 'Chansomphors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 2, 1),
('Srorn', 'Chansomphors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 3, 1),
('Srorn', 'Chansomphors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 4, 1),
('Srorn', 'Chansomphors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 5, 1),
('Som', 'Phors', NULL, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 6, 1),
('Som', 'Phors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 7, 1),
('Srorn', 'Chansomphors', NULL, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 8, 1),
('Srorn', 'Chansomphors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 9, 1),
('Som', 'Phors', 0, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 10, 1),
('Srorn', 'Chansomphors', NULL, '', 'srornchansomphors@gmail.com', '', '', '', '', '', '', '', 11, 2),
('Srorn', 'Chansomphors', NULL, '085371346', 'srornchansomphors@gmail.com', '', '', '', '', '', '', '', 12, 3),
('Srorn', 'Chansomphors', NULL, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 13, 3),
('Chan', 'Tii', 0, '0812071309', 'chantii@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 14, 3),
('Srorn', 'Chansomphors', NULL, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 17, 3),
('Som', 'Phors', NULL, '', 'somphorstae9@gmail.com', '', '', '', '', '', '', '', 18, 1),
('Delete', 'Supplier', 0, '', 'delete.supplier.test@example.com', '', '', '', '', '', '', '', 19, 1),
('Anh', 'Keonho', NULL, '085371346', 'anhkeonho@gmail.com', '', '', '', '', '', '', '', 20, 4),
('Srorn', 'Chansomphors', NULL, '085371346', 'srornchansomphors@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', '', 'Cambodia', '', 21, 4),
('Jeon', 'Jungkook', 1, '085371346', 'jungkook@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 22, 3),
('Kim', 'Juhoon', 1, '085371346', 'juhoon@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 23, 3),
('Lee', 'Wonhee', 0, '085371346', 'wonhee@gmail.com', 'Chroy Changva', '', 'Phnom Penh', '', 'CHROY CHANGVA', 'Cambodia', '', 24, 3),
('Sok', 'sokunthea', NULL, '012345678', 'sokunthea@gmail.com', '', '', '', '', '', '', '', 25, 5);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_permissions`
--

CREATE TABLE `ospos_permissions` (
  `permission_id` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `location_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_permissions`
--

INSERT INTO `ospos_permissions` (`permission_id`, `module_id`, `location_id`) VALUES
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
-- Table structure for table `ospos_plans`
--

CREATE TABLE `ospos_plans` (
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
-- Dumping data for table `ospos_plans`
--

INSERT INTO `ospos_plans` (`plan_id`, `plan_code`, `plan_name`, `price_monthly`, `max_users`, `max_locations`, `max_items`, `is_active`, `created_at`) VALUES
(1, 'starter', 'Starter', 0.00, 5, 1, 5000, 1, '2026-04-28 04:23:16'),
(2, 'basic', 'Basic', 19.00, 5, 1, 5000, 1, '2026-04-28 06:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `ospos_platform_admins`
--

CREATE TABLE `ospos_platform_admins` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ospos_platform_admins`
--

INSERT INTO `ospos_platform_admins` (`admin_id`, `username`, `password_hash`, `full_name`, `email`, `status`, `created_at`) VALUES
(1, 'superadmin', '$2y$12$N8lJSGcJNcJ8SmXHm2IeheIe3QAzLmrmt1q0opbjhlXj2uQHZu3nO', 'Platform Super Admin', NULL, 'active', '2026-04-28 04:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `ospos_receivings`
--

CREATE TABLE `ospos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `receiving_id` int(10) NOT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_receivings_items`
--

CREATE TABLE `ospos_receivings_items` (
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

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales`
--

CREATE TABLE `ospos_sales` (
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

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_items`
--

CREATE TABLE `ospos_sales_items` (
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

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_items_taxes`
--

CREATE TABLE `ospos_sales_items_taxes` (
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
-- Table structure for table `ospos_sales_payments`
--

CREATE TABLE `ospos_sales_payments` (
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

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_reward_points`
--

CREATE TABLE `ospos_sales_reward_points` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `earned` float NOT NULL,
  `used` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_taxes`
--

CREATE TABLE `ospos_sales_taxes` (
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
-- Table structure for table `ospos_sessions`
--

CREATE TABLE `ospos_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_sessions`
--

INSERT INTO `ospos_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('ospos_session:01c97455726a029313a721228565555d', '127.0.0.1', '2026-05-06 09:16:36', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383035383939363b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:0476edb10d856e793d4e447b7472ad0f', '::1', '2026-05-20 09:15:51', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393236383532303b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6e6f5f6163636573732f686f6d652f223b706572736f6e5f69647c733a323a223234223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223461396332643265316633316465323663336130323534303537396131356237223b),
('ospos_session:05620402556e0ab0df51d930e0a4e41d', '::1', '2026-04-30 02:45:26', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373132333b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223833633331663764303461363964383331363235313132616432363365656134223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:0a8191b9d6d2d2ce56df71fe1cd890b0', '::1', '2026-05-04 04:16:45', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373836383139393b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6e6f5f6163636573732f686f6d652f223b706572736f6e5f69647c733a323a223233223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a226562303034623065333365363835343866333636666263623337393733333930223b),
('ospos_session:10f7a7737b98af92db94d4bf524d1c3d', '::1', '2026-04-29 06:08:33', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434323931323b5f63695f70726576696f75735f75726c7c733a35353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f736176652f2d31223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226136643063396131633766656432323139333937376263633762656132336536223b),
('ospos_session:1284f22afaa0569e6d41b4eb7a7f8dc1', '::1', '2026-04-30 07:21:32', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373533333638373b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f676966746361726473223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223738353732366530393939326363303765323161363031373063343136656134223b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a313b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d726563765f636172747c613a303a7b7d726563765f6d6f64657c733a373a2272656365697665223b726563765f737570706c6965727c693a2d313b),
('ospos_session:1f4210abe30e46ab86e5078e0f42c80a', '::1', '2026-04-29 06:10:05', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434323935393b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223231363535343163313534373239613362653963373539373131356132313439223b),
('ospos_session:2b17afee3543a69a60b3a6c9854248e7', '::1', '2026-05-04 04:15:13', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373836383131323b637372665f6f73706f735f76347c733a33323a223139366461323733323633393937663662613337306436616334616632346437223b5f63695f70726576696f75735f75726c7c733a33383a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f223b),
('ospos_session:2c147f3b91754502b7f0f9e1b9bf1dce', '::1', '2026-04-29 06:36:40', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343539363b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223131363761306666633365343938393365323162626530666666313763363464223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:3d714b31e20d08ec5b533e9c2daeb13f', '127.0.0.1', '2026-05-07 09:58:36', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383134373931353b637372665f6f73706f735f76347c733a33323a223131623139613262346131663834313066663666616566643162393865363834223b),
('ospos_session:3fd67ccdf03886a44b08b01c1fa83b6c', '::1', '2026-04-29 06:49:38', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434353337363b5f63695f70726576696f75735f75726c7c733a3134373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f657870656e7365732f7365617263683f6c696d69743d3130266f66667365743d30267365617263683d26736f72743d657870656e73655f6964266f726465723d6173632673746172745f646174653d323032362d30342d323926656e645f646174653d323032362d30342d3239223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226233366433653738363632333930346433666430626164313339383932363537223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:40a93f44516ecf52819e655de71060d8', '::1', '2026-04-30 02:48:37', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373331343b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223030343533626233636163616332666163383438656530346634316464363734223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:4595cca869e14bdf90a0ac5f9932a9a6', '::1', '2026-04-30 02:43:43', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373032303b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226562343263323634623036363361623730616531323262653134623466313738223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:47e3f6985ebeb4f12c04cc11bd3e7184', '::1', '2026-04-30 02:44:32', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373037303b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223864346262613664333835336436313265633463383637616630613335323461223b),
('ospos_session:4a553278a23a83d359efeb39752506c4', '::1', '2026-04-30 02:40:45', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531363834323b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223964376661643665383130646531376538323831363831303234323037396163223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:4c06bbcc7de07d6716cb73f6da680fff', '::1', '2026-05-04 04:21:22', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373836383438313b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6e6f5f6163636573732f686f6d652f223b706572736f6e5f69647c733a323a223233223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a226136316634626261626639333536313664383331386262643634623734316130223b),
('ospos_session:4c8ea8c79b4417086400bd17c29e685a', '::1', '2026-05-16 12:46:49', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383933353538383b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f637573746f6d657273223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223433373231386264323261663732333837323161613638626139633234323638223b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a313b6974656d5f6c6f636174696f6e7c733a313a2231223b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d),
('ospos_session:4ee108a1e30a114ee98c884363951797', '::1', '2026-05-04 06:33:56', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373837363433353b637372665f6f73706f735f76347c733a33323a223130653762613135396130373362316432633236373338353531336430613635223b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f637573746f6d657273223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:4fd5a3927f3cad7372be42366cfe7402', '::1', '2026-04-30 02:41:12', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531363837303b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226535393439363361386630376331646466666538313934313465306136653734223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:5216635106aabfda244533a9c8dc5261', '127.0.0.1', '2026-05-07 09:58:39', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383134373931363b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:587421d17a2b8ad89b9e8e4c042c7434', '::1', '2026-04-30 03:08:06', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531383438343b5f63695f70726576696f75735f75726c7c733a3134353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f636173687570732f7365617263683f6c696d69743d3530266f66667365743d30267365617263683d26736f72743d6361736875705f6964266f726465723d6173632673746172745f646174653d323032362d30342d303126656e645f646174653d323032362d30342d3330223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223531663335303838636365326332356231653632646164643238623362383961223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:5add555564c251a6f3666bc94664021b', '::1', '2026-04-30 03:12:25', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531383734313b5f63695f70726576696f75735f75726c7c733a35323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f636173687570732f766965772f31223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226139663863323731373762376363643735393939623436623464636261386663223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:5db9205a2b6fe75bf6be9e64492573ed', '::1', '2026-04-29 07:01:43', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434363039333b637372665f6f73706f735f76347c733a33323a223734393431613032663137353166666338643263663431383437613539653533223b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f737570706c69657273223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a303b6974656d5f6c6f636174696f6e7c733a313a2231223b),
('ospos_session:60654b336db831000e101530e555c627', '::1', '2026-04-29 07:01:31', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434363039303b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f737570706c69657273223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223532306339356261393538643862316164393863383361306638646261373830223b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a303b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d6974656d5f6c6f636174696f6e7c733a313a2231223b),
('ospos_session:69f26c057c22cc061c338ede832871f4', '::1', '2026-04-30 03:43:49', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373532303632393b637372665f6f73706f735f76347c733a33323a223339393461356565316265313261366665623139323962336432333635346564223b),
('ospos_session:6d1b5d274e7d6a1dfba8bea75e3c7985', '::1', '2026-05-20 03:01:07', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393234363036303b5f63695f70726576696f75735f75726c7c733a35353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f73757065722d61646d696e2f6c6f67696e223b637372665f6f73706f735f76347c733a33323a226132663030656431386336633837666365623761313934653833313539633331223b),
('ospos_session:6d57d6fde5b423cdafda43a4ecfb3a28', '127.0.0.1', '2026-05-06 09:16:36', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383035383939363b637372665f6f73706f735f76347c733a33323a226233343033386634323439303330663362383866633164363330653262626133223b),
('ospos_session:6fb02a90d87b4f336a7b6f154dfe429a', '127.0.0.1', '2026-05-16 10:01:53', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383932353638353b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f73616173223b637372665f6f73706f735f76347c733a33323a223738623361656339663866383835316262363236373564376137643636353335223b),
('ospos_session:722639b0251a3addaad112c98d8cc7ed', '::1', '2026-04-30 03:17:12', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531393033303b5f63695f70726576696f75735f75726c7c733a3131323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f656d706c6f796565732f7365617263683f6c696d69743d3530266f66667365743d30267365617263683d26736f72743d70656f706c652e706572736f6e5f6964266f726465723d617363223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223134383464333939623966663462373238333736316462633963396632643239223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:7518e13b4e2567802fbb43c1b1d91697', '::1', '2026-04-30 06:15:10', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373532393638303b637372665f6f73706f735f76347c733a33323a223038326365613338623137313864376439633766373862343161633733353962223b5f63695f70726576696f75735f75726c7c733a34353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f63617368757073223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a303b6974656d5f6c6f636174696f6e7c733a313a2231223b),
('ospos_session:7b5f0cc32c6f05a8829deecf9e91995c', '::1', '2026-04-30 04:31:55', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373532333531343b637372665f6f73706f735f76347c733a33323a223739313531623061613861336632353531326431353261323130396535306531223b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:7f5b46ff7898534b724747a584097557', '::1', '2026-04-30 02:49:32', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373336393b5f63695f70726576696f75735f75726c7c733a35353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6974656d5f6b6974732f766965772f2d31223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223631333336656433376233643138343466326335623063396639343665326466223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:814b4475ff4d443e6d2adb1cd17c30eb', '::1', '2026-04-29 06:57:24', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434353834323b5f63695f70726576696f75735f75726c7c733a3131313a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f737570706c696572732f7365617263683f6c696d69743d35266f66667365743d30267365617263683d26736f72743d70656f706c652e706572736f6e5f6964266f726465723d617363223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226234353434633261643136306237323437636561613637353136306534333637223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:836a4d3a09bb4556732607b2a844b17d', '::1', '2026-05-20 09:45:28', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737393237303236383b637372665f6f73706f735f76347c733a33323a226466313033623536653132353833343935323438663132643239393561303763223b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f73616c6573223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a313b6974656d5f6c6f636174696f6e7c733a313a2231223b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d),
('ospos_session:84aa1e2499dab1454e481745ff4e513f', '127.0.0.1', '2026-05-06 09:11:46', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383035383730313b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:8bc4669c24c0cf5b0b0854b87f4fb2d6', '::1', '2026-06-05 07:38:11', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738303634343933353b5f63695f70726576696f75735f75726c7c733a34343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6f6666696365223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223166643764373262313761646335613463616533653739396262626661396463223b6d656e755f67726f75707c733a363a226f6666696365223b616c6c6f775f74656d705f6974656d737c693a313b6974656d5f6c6f636174696f6e7c733a313a2231223b726563765f636172747c613a303a7b7d726563765f6d6f64657c733a373a2272656365697665223b726563765f737570706c6965727c693a2d313b73616c655f69647c693a2d313b636173685f726f756e64696e677c693a303b636173685f6d6f64657c693a303b73616c65735f636172747c613a303a7b7d73616c65735f637573746f6d65727c693a2d313b73616c65735f6d6f64657c733a343a2273616c65223b73616c65735f6c6f636174696f6e7c693a313b73616c65735f7061796d656e74737c613a303a7b7d),
('ospos_session:8e235525903f5a60452752671e077ef3', '::1', '2026-04-29 04:58:00', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373433383638303b637372665f6f73706f735f76347c733a33323a226262383366313436623966356630336430373164656235303734643063383834223b),
('ospos_session:91efc222a5a4d5d3d32e79a23596475d', '::1', '2026-04-30 02:49:02', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373333393b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223964333730396437333530336438343132323761306435346533626539636166223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:93013adc57d864cbe15792be501348ee', '::1', '2026-05-04 04:15:29', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373836383132393b637372665f6f73706f735f76347c733a33323a223136663138346465666466636464663334326466626338633236323139646531223b),
('ospos_session:930495c70879e41dcf4e23249e11254f', '::1', '2026-04-29 06:15:07', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434333132313b5f63695f70726576696f75735f75726c7c733a35353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f766965772f2d31223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226534306538323534623334373938383166363465653334396530656165643730223b),
('ospos_session:971b029158337253e8e4365e209ba06d', '127.0.0.1', '2026-05-16 10:06:23', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383932353938333b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6c6f67696e223b),
('ospos_session:9bdfad6274095ea76a5f0cd2d3987bc5', '::1', '2026-04-29 06:33:48', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343432353b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226663376462613735653562373462623839663138333037633139386263303233223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:a68cf9e58b5dde2c16c4f0bf92ecc3aa', '::1', '2026-04-30 03:06:39', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531383339373b5f63695f70726576696f75735f75726c7c733a3134353a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f636173687570732f7365617263683f6c696d69743d3230266f66667365743d30267365617263683d26736f72743d6361736875705f6964266f726465723d6173632673746172745f646174653d323032362d30342d303126656e645f646174653d323032362d30342d3330223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226162613062643635313737626264303363613666346232623137636430666463223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:aeb9a5cc1c8a66ae8f0ad59a82856dee', '::1', '2026-04-30 03:25:07', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531393530343b5f63695f70726576696f75735f75726c7c733a3131303a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f617474726962757465732f7365617263683f6c696d69743d3230266f66667365743d30267365617263683d26736f72743d646566696e6974696f6e5f6964266f726465723d617363223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226361303336646538363362653962643633346166356234646232613332396464223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:af099a8519d15a3032bdcccc8d259638', '::1', '2026-04-29 06:35:19', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343531363b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223039613662363266626566663863306630353439613236653465306262633736223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:b0d1427a725b74dceb90c88271f99fc4', '::1', '2026-05-04 06:28:07', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373837363031393b637372665f6f73706f735f76347c733a33323a226366666464353237386438613961343363313437613862366633393033653931223b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6e6f5f6163636573732f686f6d652f223b706572736f6e5f69647c733a323a223234223b74656e616e745f69647c693a333b),
('ospos_session:b8bdd0a0b4bf57fd57f0f61203ccf253', '::1', '2026-04-30 02:56:58', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373831353b5f63695f70726576696f75735f75726c7c733a3134373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f657870656e7365732f7365617263683f6c696d69743d3230266f66667365743d30267365617263683d26736f72743d657870656e73655f6964266f726465723d6173632673746172745f646174653d323032362d30342d323926656e645f646174653d323032362d30342d3330223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223236343131303162313432386430396266626538343438393164623638343737223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:c3d77cd34f7b58fcf91e2f6904f63b69', '::1', '2026-04-30 02:56:16', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373737333b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f657870656e7365732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226561633836663638306663663963623830323334316539346334613263636165223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:c7bee3614aba046bc01dcd8f157eefeb', '::1', '2026-04-29 04:57:50', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373433383637303b637372665f6f73706f735f76347c733a33323a223562663331616164343265393232633938636432353330326332613663306434223b),
('ospos_session:d6612df00d499f26969f14b64862e76d', '::1', '2026-04-29 06:43:00', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343937373b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f657870656e7365732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223630396565643931323136666437313736623238633838616165396337666361223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:d9f39ccd16ef59f5b00830960ae804cc', '::1', '2026-04-29 06:56:48', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434353830353b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f737570706c696572732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223330653262343963333663323330636164626530333964373430363063653266223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:e19c40f7e44a62ba60ede5e37bb7ce77', '::1', '2026-05-30 04:12:56', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738303131343337353b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f637573746f6d657273223b637372665f6f73706f735f76347c733a33323a226439343662613666613764306232333164306433323865616238343266306665223b706572736f6e5f69647c733a323a223132223b74656e616e745f69647c693a333b6d656e755f67726f75707c733a343a22686f6d65223b616c6c6f775f74656d705f6974656d737c693a303b6974656d5f6c6f636174696f6e7c733a313a2231223b),
('ospos_session:e5a16daf435c142f131d55c0e53a7dc5', '::1', '2026-05-04 04:21:12', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373836383432373b5f63695f70726576696f75735f75726c7c733a35333a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6e6f5f6163636573732f686f6d652f223b706572736f6e5f69647c733a323a223234223b74656e616e745f69647c693a333b637372665f6f73706f735f76347c733a33323a223332663730376132363137653130333630633361353233396239663130346534223b),
('ospos_session:effda2603d0be0c0301aff80532982a1', '::1', '2026-04-30 03:27:42', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531393635393b5f63695f70726576696f75735f75726c7c733a3131303a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f617474726962757465732f7365617263683f6c696d69743d3230266f66667365743d30267365617263683d26736f72743d646566696e6974696f6e5f6964266f726465723d617363223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223334666634643431363931653334346466393265653336313939316131666230223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:f33d7ea75bf91fe4e0b9ee9352bce74f', '::1', '2026-04-29 06:37:45', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343636313b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223033356264303765616437643436356334383834343964303832376132316133223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:f47c887a30e0c03c268d9d945918dca1', '::1', '2026-05-30 04:12:50', 0x5f5f63695f6c6173745f726567656e65726174657c693a313738303131343234333b637372665f6f73706f735f76347c733a33323a226535376430653235666361626135616166336561656236353761306333303332223b5f63695f70726576696f75735f75726c7c733a34373a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f637573746f6d657273223b706572736f6e5f69647c733a323a223235223b74656e616e745f69647c693a353b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:f7cdf5f87aef1d83ad6a038ec928e550', '::1', '2026-04-30 02:43:45', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373531373032333b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f686f6d65223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a223966343037353231316531316634333435373237633562663631336531316430223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:f8cd83183fa17c83c941868ac59133e3', '127.0.0.1', '2026-05-06 09:11:41', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737383035383639393b637372665f6f73706f735f76347c733a33323a226265336337626439623630386162353632326632323763613665353433383937223b),
('ospos_session:fb22fcc455c682e483f9081d89a9a455', '::1', '2026-04-29 06:38:59', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434343733353b5f63695f70726576696f75735f75726c7c733a35343a22687474703a2f2f6c6f63616c686f73742f6f70656e736f75726365706f732f7075626c69632f6769667463617264732f64656c657465223b706572736f6e5f69647c733a313a2231223b74656e616e745f69647c693a313b637372665f6f73706f735f76347c733a33323a226561623665383965313030653766363866613135346536363963306339326330223b6d656e755f67726f75707c733a343a22686f6d65223b),
('ospos_session:fdf45437fd07a5aa8d654d20e31479fb', '::1', '2026-04-29 06:08:54', 0x5f5f63695f6c6173745f726567656e65726174657c693a313737373434323933343b637372665f6f73706f735f76347c733a33323a223837363235643262623732303865353433313731656136326132653330636332223b);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_stock_locations`
--

CREATE TABLE `ospos_stock_locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ospos_stock_locations`
--

INSERT INTO `ospos_stock_locations` (`location_id`, `location_name`, `deleted`, `tenant_id`) VALUES
(1, 'stock', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_subscriptions`
--

CREATE TABLE `ospos_subscriptions` (
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
-- Dumping data for table `ospos_subscriptions`
--

INSERT INTO `ospos_subscriptions` (`subscription_id`, `tenant_id`, `plan_id`, `status`, `trial_ends_at`, `period_start`, `period_end`, `cancel_at_period_end`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', NULL, '2026-04-28 11:23:17', '2026-05-28 11:23:17', 0, '2026-04-28 04:23:17', NULL),
(2, 3, 1, 'active', NULL, '2026-04-28 03:02:13', '2026-05-28 03:02:13', 0, '2026-04-28 07:02:13', NULL),
(3, 4, 2, 'active', NULL, '2026-04-30 00:24:13', '2026-05-30 00:24:13', 0, '2026-04-30 04:24:13', NULL),
(4, 5, 1, 'active', NULL, '2026-05-16 06:55:17', '2026-06-16 06:55:17', 0, '2026-05-16 10:55:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_subscription_requests`
--

CREATE TABLE `ospos_subscription_requests` (
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
-- Dumping data for table `ospos_subscription_requests`
--

INSERT INTO `ospos_subscription_requests` (`request_id`, `company_name`, `tenant_code`, `owner_first_name`, `owner_last_name`, `owner_email`, `owner_phone`, `owner_username`, `owner_password_hash`, `plan_id`, `payment_reference`, `status`, `notes`, `reviewed_by_admin_id`, `created_at`, `reviewed_at`) VALUES
(1, 'My Company Retail', 'myco-retail', 'Srorn', 'Chansomphors', 'srornchansomphors@gmail.com', '085371346', 'myco_owner', '$2y$10$p/V7.0bW8JBORu9mv46cFO09mWnEpoPrW3HvnvjozRxGzjCHIUUH.', 1, 'KH-TRX-20260428-1001', 'approved', 'Submitted from website signup flow', 1, '2026-04-28 07:00:51', '2026-04-28 03:02:13'),
(2, 'Anh Keonho', 'trust', 'Anh', 'Keonho', 'anhkeonho@gmail.com', '085371346', 'anhkeonho', '$2y$10$aeJE1LX6XZj27CU5xRNiJ..pz5thkeyc517RGHaXpKngt8JxWo62S', 2, 'KH-TRX-20260428-1001', 'approved', 'Submitted from website signup flow', 1, '2026-04-30 04:22:48', '2026-04-30 00:24:13'),
(3, 'Sok sokunthea', 'sokunthea', 'Sok', 'sokunthea', 'sokunthea@gmail.com', '012345678', 'sokunthea', '$2y$10$jw5rOWB5zseZTJziLALmCueShz1O1lO3isIfOMOwE3PfTdRkysee6', 1, 'KH-TRX-20260428-1002', 'approved', 'Submitted from website signup flow', 1, '2026-05-16 10:54:46', '2026-05-16 06:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `ospos_suppliers`
--

CREATE TABLE `ospos_suppliers` (
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
-- Dumping data for table `ospos_suppliers`
--

INSERT INTO `ospos_suppliers` (`person_id`, `company_name`, `agency_name`, `account_number`, `tax_id`, `deleted`, `category`, `tenant_id`) VALUES
(17, 'My Company Retail', '', NULL, '', 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tax_categories`
--

CREATE TABLE `ospos_tax_categories` (
  `tax_category_id` int(10) NOT NULL,
  `tax_category` varchar(32) NOT NULL,
  `tax_group_sequence` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tax_codes`
--

CREATE TABLE `ospos_tax_codes` (
  `tax_code_id` int(11) NOT NULL,
  `tax_code` varchar(32) NOT NULL,
  `tax_code_name` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tax_jurisdictions`
--

CREATE TABLE `ospos_tax_jurisdictions` (
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
-- Table structure for table `ospos_tax_rates`
--

CREATE TABLE `ospos_tax_rates` (
  `tax_rate_id` int(11) NOT NULL,
  `rate_tax_code_id` int(11) NOT NULL,
  `rate_tax_category_id` int(10) NOT NULL,
  `rate_jurisdiction_id` int(11) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `tax_rounding_code` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tenants`
--

CREATE TABLE `ospos_tenants` (
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
-- Dumping data for table `ospos_tenants`
--

INSERT INTO `ospos_tenants` (`tenant_id`, `tenant_code`, `company_name`, `status`, `timezone`, `currency_code`, `db_hostname`, `db_port`, `db_name`, `db_username`, `db_password`, `db_prefix`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Default Tenant', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 04:23:17', NULL),
(2, '001', 'My company', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 04:44:29', NULL),
(3, 'myco-retail', 'My Company Retail', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28 07:02:13', '2026-05-30 03:04:23'),
(4, 'trust', 'Anh Keonho', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-30 04:24:13', NULL),
(5, 'sokunthea', 'Sok sokunthea', 'active', 'UTC', 'USD', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-16 10:55:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tenant_config`
--

CREATE TABLE `ospos_tenant_config` (
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ospos_tenant_config`
--

INSERT INTO `ospos_tenant_config` (`tenant_id`, `config_key`, `config_value`) VALUES
(2, 'company', 'My company'),
(2, 'currency_code', 'USD'),
(2, 'timezone', 'UTC'),
(3, 'company', 'My Company Retail'),
(3, 'currency_code', 'USD'),
(3, 'timezone', 'UTC'),
(4, 'company', 'Anh Keonho'),
(4, 'currency_code', 'USD'),
(4, 'timezone', 'UTC'),
(5, 'company', 'Sok sokunthea'),
(5, 'currency_code', 'USD'),
(5, 'timezone', 'UTC');

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tenant_domains`
--

CREATE TABLE `ospos_tenant_domains` (
  `domain_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_tenant_users`
--

CREATE TABLE `ospos_tenant_users` (
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `person_id` int(10) NOT NULL,
  `tenant_role` enum('owner','admin','manager','cashier') NOT NULL DEFAULT 'cashier',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ospos_tenant_users`
--

INSERT INTO `ospos_tenant_users` (`tenant_id`, `person_id`, `tenant_role`, `is_active`, `created_at`) VALUES
(2, 11, 'owner', 1, '2026-04-28 04:44:29'),
(3, 12, 'owner', 1, '2026-04-28 07:02:13'),
(4, 20, 'owner', 1, '2026-04-30 04:24:13'),
(5, 25, 'owner', 1, '2026-05-16 10:55:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ospos_app_config`
--
ALTER TABLE `ospos_app_config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `ospos_attribute_definitions`
--
ALTER TABLE `ospos_attribute_definitions`
  ADD PRIMARY KEY (`definition_id`),
  ADD KEY `definition_fk` (`definition_fk`),
  ADD KEY `definition_name` (`definition_name`),
  ADD KEY `definition_type` (`definition_type`),
  ADD KEY `idx_attribute_definitions_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_attribute_links`
--
ALTER TABLE `ospos_attribute_links`
  ADD UNIQUE KEY `attribute_links_uq1` (`attribute_id`,`definition_id`,`item_id`,`sale_id`,`receiving_id`),
  ADD UNIQUE KEY `attribute_links_uq2` (`item_id`,`receiving_id`,`sale_id`,`definition_id`,`attribute_id`),
  ADD UNIQUE KEY `attribute_links_uq3` (`generated_unique_column`),
  ADD KEY `attribute_id` (`attribute_id`),
  ADD KEY `definition_id` (`definition_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `receiving_id` (`receiving_id`);

--
-- Indexes for table `ospos_attribute_values`
--
ALTER TABLE `ospos_attribute_values`
  ADD PRIMARY KEY (`attribute_id`),
  ADD UNIQUE KEY `attribute_value` (`attribute_value`),
  ADD UNIQUE KEY `attribute_date` (`attribute_date`),
  ADD UNIQUE KEY `attribute_decimal` (`attribute_decimal`);

--
-- Indexes for table `ospos_cash_up`
--
ALTER TABLE `ospos_cash_up`
  ADD PRIMARY KEY (`cashup_id`),
  ADD KEY `open_employee_id` (`open_employee_id`),
  ADD KEY `close_employee_id` (`close_employee_id`),
  ADD KEY `idx_cash_up_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_customers`
--
ALTER TABLE `ospos_customers`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `sales_tax_code_id` (`sales_tax_code_id`),
  ADD KEY `account_number` (`account_number`),
  ADD KEY `company_name` (`company_name`),
  ADD KEY `idx_customers_tenant_id` (`tenant_id`),
  ADD KEY `idx_customers_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `ospos_customers_packages`
--
ALTER TABLE `ospos_customers_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `ospos_customers_points`
--
ALTER TABLE `ospos_customers_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `ospos_dinner_tables`
--
ALTER TABLE `ospos_dinner_tables`
  ADD PRIMARY KEY (`dinner_table_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `ospos_employees`
--
ALTER TABLE `ospos_employees`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_employees_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_expenses`
--
ALTER TABLE `ospos_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `expense_category_id` (`expense_category_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `date` (`date`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `amount` (`amount`),
  ADD KEY `ospos_expenses_ibfk_3` (`supplier_id`),
  ADD KEY `idx_expenses_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_expense_categories`
--
ALTER TABLE `ospos_expense_categories`
  ADD PRIMARY KEY (`expense_category_id`),
  ADD UNIQUE KEY `uk_expense_categories_tenant_name` (`tenant_id`,`category_name`),
  ADD KEY `category_description` (`category_description`),
  ADD KEY `idx_expense_categories_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_giftcards`
--
ALTER TABLE `ospos_giftcards`
  ADD PRIMARY KEY (`giftcard_id`),
  ADD UNIQUE KEY `uk_giftcards_tenant_number` (`tenant_id`,`giftcard_number`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `idx_giftcards_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_grants`
--
ALTER TABLE `ospos_grants`
  ADD PRIMARY KEY (`permission_id`,`person_id`),
  ADD KEY `ospos_grants_ibfk_2` (`person_id`);

--
-- Indexes for table `ospos_inventory`
--
ALTER TABLE `ospos_inventory`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `trans_items` (`trans_items`),
  ADD KEY `trans_user` (`trans_user`),
  ADD KEY `trans_location` (`trans_location`),
  ADD KEY `trans_date` (`trans_date`),
  ADD KEY `trans_items_trans_date` (`trans_items`,`trans_date`),
  ADD KEY `idx_inventory_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_invoices`
--
ALTER TABLE `ospos_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `uk_invoices_tenant_number` (`tenant_id`,`invoice_number`),
  ADD KEY `idx_invoices_subscription` (`subscription_id`),
  ADD KEY `idx_invoices_tenant_status` (`tenant_id`,`status`);

--
-- Indexes for table `ospos_invoice_payments`
--
ALTER TABLE `ospos_invoice_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_invoice_payments_tenant` (`tenant_id`),
  ADD KEY `idx_invoice_payments_invoice` (`invoice_id`),
  ADD KEY `idx_invoice_payments_provider_ref` (`provider`,`provider_payment_id`);

--
-- Indexes for table `ospos_items`
--
ALTER TABLE `ospos_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `items_uq1` (`supplier_id`,`item_id`,`deleted`,`item_type`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `item_number` (`item_number`),
  ADD KEY `deleted` (`deleted`,`item_type`),
  ADD KEY `item_id` (`item_id`,`deleted`),
  ADD KEY `idx_items_tenant_id` (`tenant_id`),
  ADD KEY `idx_items_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `ospos_items_taxes`
--
ALTER TABLE `ospos_items_taxes`
  ADD PRIMARY KEY (`item_id`,`name`,`percent`),
  ADD KEY `idx_items_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_item_kits`
--
ALTER TABLE `ospos_item_kits`
  ADD PRIMARY KEY (`item_kit_id`),
  ADD KEY `item_kit_number` (`item_kit_number`),
  ADD KEY `name` (`name`,`description`),
  ADD KEY `idx_item_kits_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_item_kit_items`
--
ALTER TABLE `ospos_item_kit_items`
  ADD PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`),
  ADD KEY `ospos_item_kit_items_ibfk_2` (`item_id`),
  ADD KEY `idx_item_kit_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_item_quantities`
--
ALTER TABLE `ospos_item_quantities`
  ADD PRIMARY KEY (`item_id`,`location_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `idx_item_quantities_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_migrations`
--
ALTER TABLE `ospos_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_modules`
--
ALTER TABLE `ospos_modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  ADD UNIQUE KEY `name_lang_key` (`name_lang_key`);

--
-- Indexes for table `ospos_people`
--
ALTER TABLE `ospos_people`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `email` (`email`),
  ADD KEY `first_name` (`first_name`,`last_name`,`email`,`phone_number`),
  ADD KEY `idx_people_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_permissions`
--
ALTER TABLE `ospos_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `ospos_permissions_ibfk_2` (`location_id`);

--
-- Indexes for table `ospos_plans`
--
ALTER TABLE `ospos_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD UNIQUE KEY `uk_plans_code` (`plan_code`);

--
-- Indexes for table `ospos_platform_admins`
--
ALTER TABLE `ospos_platform_admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uk_platform_admins_username` (`username`);

--
-- Indexes for table `ospos_receivings`
--
ALTER TABLE `ospos_receivings`
  ADD PRIMARY KEY (`receiving_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reference` (`reference`),
  ADD KEY `receiving_time` (`receiving_time`),
  ADD KEY `idx_receivings_tenant_id` (`tenant_id`),
  ADD KEY `idx_receivings_tenant_date` (`tenant_id`,`receiving_time`);

--
-- Indexes for table `ospos_receivings_items`
--
ALTER TABLE `ospos_receivings_items`
  ADD PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_receivings_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_sales`
--
ALTER TABLE `ospos_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `sale_time` (`sale_time`),
  ADD KEY `dinner_table_id` (`dinner_table_id`),
  ADD KEY `idx_sales_tenant_id` (`tenant_id`),
  ADD KEY `idx_sales_tenant_date` (`tenant_id`,`sale_time`);

--
-- Indexes for table `ospos_sales_items`
--
ALTER TABLE `ospos_sales_items`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_location` (`item_location`),
  ADD KEY `idx_sales_items_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_sales_items_taxes`
--
ALTER TABLE `ospos_sales_items_taxes`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_sales_items_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_sales_payments`
--
ALTER TABLE `ospos_sales_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_sale` (`sale_id`,`payment_type`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `payment_time` (`payment_time`),
  ADD KEY `idx_sales_payments_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_sales_reward_points`
--
ALTER TABLE `ospos_sales_reward_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `ospos_sales_taxes`
--
ALTER TABLE `ospos_sales_taxes`
  ADD PRIMARY KEY (`sales_taxes_id`),
  ADD KEY `print_sequence` (`sale_id`,`print_sequence`,`tax_group`),
  ADD KEY `idx_sales_taxes_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_sessions`
--
ALTER TABLE `ospos_sessions`
  ADD PRIMARY KEY (`id`,`ip_address`),
  ADD KEY `ospos_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `ospos_stock_locations`
--
ALTER TABLE `ospos_stock_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `idx_locations_tenant_id` (`tenant_id`);

--
-- Indexes for table `ospos_subscriptions`
--
ALTER TABLE `ospos_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_subscriptions_tenant_status` (`tenant_id`,`status`),
  ADD KEY `idx_subscriptions_plan` (`plan_id`);

--
-- Indexes for table `ospos_subscription_requests`
--
ALTER TABLE `ospos_subscription_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_subscription_requests_status` (`status`),
  ADD KEY `idx_subscription_requests_plan` (`plan_id`),
  ADD KEY `idx_subscription_requests_code` (`tenant_code`);

--
-- Indexes for table `ospos_suppliers`
--
ALTER TABLE `ospos_suppliers`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `category` (`category`),
  ADD KEY `company_name` (`company_name`,`deleted`),
  ADD KEY `idx_suppliers_tenant_id` (`tenant_id`),
  ADD KEY `idx_suppliers_tenant_deleted` (`tenant_id`,`deleted`);

--
-- Indexes for table `ospos_tax_categories`
--
ALTER TABLE `ospos_tax_categories`
  ADD PRIMARY KEY (`tax_category_id`);

--
-- Indexes for table `ospos_tax_codes`
--
ALTER TABLE `ospos_tax_codes`
  ADD PRIMARY KEY (`tax_code_id`);

--
-- Indexes for table `ospos_tax_jurisdictions`
--
ALTER TABLE `ospos_tax_jurisdictions`
  ADD PRIMARY KEY (`jurisdiction_id`),
  ADD UNIQUE KEY `tax_jurisdictions_uq1` (`tax_group`);

--
-- Indexes for table `ospos_tax_rates`
--
ALTER TABLE `ospos_tax_rates`
  ADD PRIMARY KEY (`tax_rate_id`),
  ADD KEY `rate_tax_category_id` (`rate_tax_category_id`),
  ADD KEY `rate_tax_code_id` (`rate_tax_code_id`),
  ADD KEY `rate_jurisdiction_id` (`rate_jurisdiction_id`);

--
-- Indexes for table `ospos_tenants`
--
ALTER TABLE `ospos_tenants`
  ADD PRIMARY KEY (`tenant_id`),
  ADD UNIQUE KEY `uk_tenants_code` (`tenant_code`);

--
-- Indexes for table `ospos_tenant_config`
--
ALTER TABLE `ospos_tenant_config`
  ADD PRIMARY KEY (`tenant_id`,`config_key`);

--
-- Indexes for table `ospos_tenant_domains`
--
ALTER TABLE `ospos_tenant_domains`
  ADD PRIMARY KEY (`domain_id`),
  ADD UNIQUE KEY `uk_tenant_domains_domain` (`domain`),
  ADD KEY `idx_tenant_domains_tenant` (`tenant_id`);

--
-- Indexes for table `ospos_tenant_users`
--
ALTER TABLE `ospos_tenant_users`
  ADD PRIMARY KEY (`tenant_id`,`person_id`),
  ADD KEY `idx_tenant_users_person` (`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ospos_attribute_definitions`
--
ALTER TABLE `ospos_attribute_definitions`
  MODIFY `definition_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ospos_attribute_values`
--
ALTER TABLE `ospos_attribute_values`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_cash_up`
--
ALTER TABLE `ospos_cash_up`
  MODIFY `cashup_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ospos_customers_packages`
--
ALTER TABLE `ospos_customers_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ospos_customers_points`
--
ALTER TABLE `ospos_customers_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_dinner_tables`
--
ALTER TABLE `ospos_dinner_tables`
  MODIFY `dinner_table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ospos_expenses`
--
ALTER TABLE `ospos_expenses`
  MODIFY `expense_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ospos_expense_categories`
--
ALTER TABLE `ospos_expense_categories`
  MODIFY `expense_category_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ospos_giftcards`
--
ALTER TABLE `ospos_giftcards`
  MODIFY `giftcard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ospos_inventory`
--
ALTER TABLE `ospos_inventory`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ospos_invoices`
--
ALTER TABLE `ospos_invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_invoice_payments`
--
ALTER TABLE `ospos_invoice_payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_items`
--
ALTER TABLE `ospos_items`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ospos_item_kits`
--
ALTER TABLE `ospos_item_kits`
  MODIFY `item_kit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ospos_migrations`
--
ALTER TABLE `ospos_migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `ospos_people`
--
ALTER TABLE `ospos_people`
  MODIFY `person_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `ospos_plans`
--
ALTER TABLE `ospos_plans`
  MODIFY `plan_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ospos_platform_admins`
--
ALTER TABLE `ospos_platform_admins`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ospos_receivings`
--
ALTER TABLE `ospos_receivings`
  MODIFY `receiving_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales`
--
ALTER TABLE `ospos_sales`
  MODIFY `sale_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales_payments`
--
ALTER TABLE `ospos_sales_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales_reward_points`
--
ALTER TABLE `ospos_sales_reward_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales_taxes`
--
ALTER TABLE `ospos_sales_taxes`
  MODIFY `sales_taxes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_stock_locations`
--
ALTER TABLE `ospos_stock_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ospos_subscriptions`
--
ALTER TABLE `ospos_subscriptions`
  MODIFY `subscription_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ospos_subscription_requests`
--
ALTER TABLE `ospos_subscription_requests`
  MODIFY `request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ospos_tax_categories`
--
ALTER TABLE `ospos_tax_categories`
  MODIFY `tax_category_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_tax_codes`
--
ALTER TABLE `ospos_tax_codes`
  MODIFY `tax_code_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_tax_jurisdictions`
--
ALTER TABLE `ospos_tax_jurisdictions`
  MODIFY `jurisdiction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_tax_rates`
--
ALTER TABLE `ospos_tax_rates`
  MODIFY `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_tenants`
--
ALTER TABLE `ospos_tenants`
  MODIFY `tenant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ospos_tenant_domains`
--
ALTER TABLE `ospos_tenant_domains`
  MODIFY `domain_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ospos_attribute_definitions`
--
ALTER TABLE `ospos_attribute_definitions`
  ADD CONSTRAINT `fk_ospos_attribute_definitions_ibfk_1` FOREIGN KEY (`definition_fk`) REFERENCES `ospos_attribute_definitions` (`definition_id`);

--
-- Constraints for table `ospos_attribute_links`
--
ALTER TABLE `ospos_attribute_links`
  ADD CONSTRAINT `ospos_attribute_links_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `ospos_attribute_definitions` (`definition_id`),
  ADD CONSTRAINT `ospos_attribute_links_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `ospos_attribute_values` (`attribute_id`),
  ADD CONSTRAINT `ospos_attribute_links_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`),
  ADD CONSTRAINT `ospos_attribute_links_ibfk_4` FOREIGN KEY (`receiving_id`) REFERENCES `ospos_receivings` (`receiving_id`),
  ADD CONSTRAINT `ospos_attribute_links_ibfk_5` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`);

--
-- Constraints for table `ospos_cash_up`
--
ALTER TABLE `ospos_cash_up`
  ADD CONSTRAINT `ospos_cash_up_ibfk_1` FOREIGN KEY (`open_employee_id`) REFERENCES `ospos_employees` (`person_id`),
  ADD CONSTRAINT `ospos_cash_up_ibfk_2` FOREIGN KEY (`close_employee_id`) REFERENCES `ospos_employees` (`person_id`);

--
-- Constraints for table `ospos_customers`
--
ALTER TABLE `ospos_customers`
  ADD CONSTRAINT `fk_customers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_customers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`),
  ADD CONSTRAINT `ospos_customers_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `ospos_customers_packages` (`package_id`),
  ADD CONSTRAINT `ospos_customers_ibfk_3` FOREIGN KEY (`sales_tax_code_id`) REFERENCES `ospos_tax_codes` (`tax_code_id`);

--
-- Constraints for table `ospos_customers_points`
--
ALTER TABLE `ospos_customers_points`
  ADD CONSTRAINT `ospos_customers_points_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ospos_customers` (`person_id`),
  ADD CONSTRAINT `ospos_customers_points_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `ospos_customers_packages` (`package_id`),
  ADD CONSTRAINT `ospos_customers_points_ibfk_3` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`);

--
-- Constraints for table `ospos_employees`
--
ALTER TABLE `ospos_employees`
  ADD CONSTRAINT `fk_employees_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_employees_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`);

--
-- Constraints for table `ospos_expenses`
--
ALTER TABLE `ospos_expenses`
  ADD CONSTRAINT `ospos_expenses_ibfk_1` FOREIGN KEY (`expense_category_id`) REFERENCES `ospos_expense_categories` (`expense_category_id`),
  ADD CONSTRAINT `ospos_expenses_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`),
  ADD CONSTRAINT `ospos_expenses_ibfk_3` FOREIGN KEY (`supplier_id`) REFERENCES `ospos_suppliers` (`person_id`);

--
-- Constraints for table `ospos_giftcards`
--
ALTER TABLE `ospos_giftcards`
  ADD CONSTRAINT `ospos_giftcards_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`);

--
-- Constraints for table `ospos_grants`
--
ALTER TABLE `ospos_grants`
  ADD CONSTRAINT `ospos_grants_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `ospos_permissions` (`permission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ospos_grants_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `ospos_employees` (`person_id`) ON DELETE CASCADE;

--
-- Constraints for table `ospos_inventory`
--
ALTER TABLE `ospos_inventory`
  ADD CONSTRAINT `ospos_inventory_ibfk_1` FOREIGN KEY (`trans_items`) REFERENCES `ospos_items` (`item_id`),
  ADD CONSTRAINT `ospos_inventory_ibfk_2` FOREIGN KEY (`trans_user`) REFERENCES `ospos_employees` (`person_id`),
  ADD CONSTRAINT `ospos_inventory_ibfk_3` FOREIGN KEY (`trans_location`) REFERENCES `ospos_stock_locations` (`location_id`);

--
-- Constraints for table `ospos_invoices`
--
ALTER TABLE `ospos_invoices`
  ADD CONSTRAINT `fk_invoices_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `ospos_subscriptions` (`subscription_id`),
  ADD CONSTRAINT `fk_invoices_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_invoice_payments`
--
ALTER TABLE `ospos_invoice_payments`
  ADD CONSTRAINT `fk_invoice_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `ospos_invoices` (`invoice_id`),
  ADD CONSTRAINT `fk_invoice_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_items`
--
ALTER TABLE `ospos_items`
  ADD CONSTRAINT `fk_items_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `ospos_suppliers` (`person_id`);

--
-- Constraints for table `ospos_items_taxes`
--
ALTER TABLE `ospos_items_taxes`
  ADD CONSTRAINT `ospos_items_taxes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `ospos_item_kits`
--
ALTER TABLE `ospos_item_kits`
  ADD CONSTRAINT `fk_item_kits_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_item_kit_items`
--
ALTER TABLE `ospos_item_kit_items`
  ADD CONSTRAINT `ospos_item_kit_items_ibfk_1` FOREIGN KEY (`item_kit_id`) REFERENCES `ospos_item_kits` (`item_kit_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ospos_item_kit_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `ospos_item_quantities`
--
ALTER TABLE `ospos_item_quantities`
  ADD CONSTRAINT `ospos_item_quantities_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`),
  ADD CONSTRAINT `ospos_item_quantities_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `ospos_stock_locations` (`location_id`);

--
-- Constraints for table `ospos_people`
--
ALTER TABLE `ospos_people`
  ADD CONSTRAINT `fk_people_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_permissions`
--
ALTER TABLE `ospos_permissions`
  ADD CONSTRAINT `ospos_permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `ospos_modules` (`module_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ospos_permissions_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `ospos_stock_locations` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `ospos_receivings`
--
ALTER TABLE `ospos_receivings`
  ADD CONSTRAINT `fk_receivings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_receivings_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`),
  ADD CONSTRAINT `ospos_receivings_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `ospos_suppliers` (`person_id`);

--
-- Constraints for table `ospos_receivings_items`
--
ALTER TABLE `ospos_receivings_items`
  ADD CONSTRAINT `ospos_receivings_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`),
  ADD CONSTRAINT `ospos_receivings_items_ibfk_2` FOREIGN KEY (`receiving_id`) REFERENCES `ospos_receivings` (`receiving_id`);

--
-- Constraints for table `ospos_sales`
--
ALTER TABLE `ospos_sales`
  ADD CONSTRAINT `fk_sales_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_sales_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`),
  ADD CONSTRAINT `ospos_sales_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `ospos_customers` (`person_id`),
  ADD CONSTRAINT `ospos_sales_ibfk_3` FOREIGN KEY (`dinner_table_id`) REFERENCES `ospos_dinner_tables` (`dinner_table_id`);

--
-- Constraints for table `ospos_sales_items`
--
ALTER TABLE `ospos_sales_items`
  ADD CONSTRAINT `ospos_sales_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`),
  ADD CONSTRAINT `ospos_sales_items_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`),
  ADD CONSTRAINT `ospos_sales_items_ibfk_3` FOREIGN KEY (`item_location`) REFERENCES `ospos_stock_locations` (`location_id`);

--
-- Constraints for table `ospos_sales_items_taxes`
--
ALTER TABLE `ospos_sales_items_taxes`
  ADD CONSTRAINT `ospos_sales_items_taxes_ibfk_1` FOREIGN KEY (`sale_id`,`item_id`,`line`) REFERENCES `ospos_sales_items` (`sale_id`, `item_id`, `line`),
  ADD CONSTRAINT `ospos_sales_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ospos_items` (`item_id`);

--
-- Constraints for table `ospos_sales_payments`
--
ALTER TABLE `ospos_sales_payments`
  ADD CONSTRAINT `ospos_sales_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`),
  ADD CONSTRAINT `ospos_sales_payments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `ospos_employees` (`person_id`);

--
-- Constraints for table `ospos_sales_reward_points`
--
ALTER TABLE `ospos_sales_reward_points`
  ADD CONSTRAINT `ospos_sales_reward_points_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `ospos_sales` (`sale_id`);

--
-- Constraints for table `ospos_stock_locations`
--
ALTER TABLE `ospos_stock_locations`
  ADD CONSTRAINT `fk_stock_locations_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_subscriptions`
--
ALTER TABLE `ospos_subscriptions`
  ADD CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `ospos_plans` (`plan_id`),
  ADD CONSTRAINT `fk_subscriptions_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_subscription_requests`
--
ALTER TABLE `ospos_subscription_requests`
  ADD CONSTRAINT `fk_subscription_requests_plan` FOREIGN KEY (`plan_id`) REFERENCES `ospos_plans` (`plan_id`);

--
-- Constraints for table `ospos_suppliers`
--
ALTER TABLE `ospos_suppliers`
  ADD CONSTRAINT `fk_suppliers_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`),
  ADD CONSTRAINT `ospos_suppliers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`);

--
-- Constraints for table `ospos_tax_rates`
--
ALTER TABLE `ospos_tax_rates`
  ADD CONSTRAINT `ospos_tax_rates_ibfk_1` FOREIGN KEY (`rate_tax_category_id`) REFERENCES `ospos_tax_categories` (`tax_category_id`),
  ADD CONSTRAINT `ospos_tax_rates_ibfk_2` FOREIGN KEY (`rate_tax_code_id`) REFERENCES `ospos_tax_codes` (`tax_code_id`),
  ADD CONSTRAINT `ospos_tax_rates_ibfk_3` FOREIGN KEY (`rate_jurisdiction_id`) REFERENCES `ospos_tax_jurisdictions` (`jurisdiction_id`);

--
-- Constraints for table `ospos_tenant_config`
--
ALTER TABLE `ospos_tenant_config`
  ADD CONSTRAINT `fk_tenant_config_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_tenant_domains`
--
ALTER TABLE `ospos_tenant_domains`
  ADD CONSTRAINT `fk_tenant_domains_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

--
-- Constraints for table `ospos_tenant_users`
--
ALTER TABLE `ospos_tenant_users`
  ADD CONSTRAINT `fk_tenant_users_person` FOREIGN KEY (`person_id`) REFERENCES `ospos_people` (`person_id`),
  ADD CONSTRAINT `fk_tenant_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `ospos_tenants` (`tenant_id`);

-- ============================================================
-- ALREADY HAVE THIS DATABASE? Do NOT re-import ospos.sql!
-- Import only: app/Database/demo_sample_data.sql
-- Or double-click: app/Database/import_demo_data.bat
-- Login: admin / pointofsale
-- ============================================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
