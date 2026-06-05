-- ============================================================
-- OSPOS Demo Sample Data
-- 6 example records per feature (tenant_id = 1, admin user)
-- Uses ID range 1001+ to avoid conflicts with existing data.
--
-- YOU ALREADY HAVE A DATABASE? Use this file only!
--   phpMyAdmin: database "ospos" -> Import -> this file
--   OR double-click: app/Database/import_demo_data.bat
-- Login: admin / pointofsale
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Remove previous demo run (IDs 1001-1999)
DELETE FROM `ospos_sales_payments` WHERE `payment_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_sales_items_taxes` WHERE `sale_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_sales_taxes` WHERE `sale_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_sales_items` WHERE `sale_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_sales_reward_points` WHERE `sale_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_sales` WHERE `sale_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_receivings_items` WHERE `receiving_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_receivings` WHERE `receiving_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_expenses` WHERE `expense_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_giftcards` WHERE `giftcard_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_cash_up` WHERE `cashup_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_inventory` WHERE `trans_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_item_kit_items` WHERE `item_kit_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_item_kits` WHERE `item_kit_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_item_quantities` WHERE `item_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_items_taxes` WHERE `item_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_items` WHERE `item_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_customers` WHERE `person_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_suppliers` WHERE `person_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_expense_categories` WHERE `expense_category_id` BETWEEN 1001 AND 1999;
DELETE `g` FROM `ospos_grants` `g` WHERE `g`.`person_id` BETWEEN 1021 AND 1999;
DELETE FROM `ospos_employees` WHERE `person_id` BETWEEN 1021 AND 1999;
DELETE FROM `ospos_people` WHERE `person_id` BETWEEN 1001 AND 1999;
DELETE FROM `ospos_stock_locations` WHERE `location_id` BETWEEN 1002 AND 1999 AND `tenant_id` = 1;
DELETE FROM `ospos_dinner_tables` WHERE `dinner_table_id` BETWEEN 1001 AND 1999;

-- Clear old tenant 1 transactional rows (low IDs from earlier imports)
DELETE FROM `ospos_sales_payments` WHERE `tenant_id` = 1 AND `sale_id` < 1001;
DELETE FROM `ospos_sales_items` WHERE `tenant_id` = 1 AND `sale_id` < 1001;
DELETE FROM `ospos_sales` WHERE `tenant_id` = 1 AND `sale_id` < 1001;
DELETE FROM `ospos_receivings_items` WHERE `tenant_id` = 1;
DELETE FROM `ospos_receivings` WHERE `tenant_id` = 1;
DELETE FROM `ospos_expenses` WHERE `tenant_id` = 1 AND `expense_id` < 1001;
DELETE FROM `ospos_giftcards` WHERE `tenant_id` = 1 AND `giftcard_id` < 1001;
DELETE FROM `ospos_cash_up` WHERE `tenant_id` = 1 AND `cashup_id` < 1001;
DELETE FROM `ospos_inventory` WHERE `tenant_id` = 1 AND `trans_id` < 1001;
DELETE FROM `ospos_item_kit_items` WHERE `tenant_id` = 1;
DELETE FROM `ospos_item_kits` WHERE `tenant_id` = 1 AND `item_kit_id` < 1001;
DELETE FROM `ospos_item_quantities` WHERE `tenant_id` = 1;
DELETE FROM `ospos_items_taxes` WHERE `tenant_id` = 1;
DELETE FROM `ospos_items` WHERE `tenant_id` = 1;
DELETE FROM `ospos_customers` WHERE `tenant_id` = 1;
DELETE FROM `ospos_suppliers` WHERE `tenant_id` = 1;
DELETE FROM `ospos_expense_categories` WHERE `tenant_id` = 1;
DELETE `g` FROM `ospos_grants` `g` INNER JOIN `ospos_people` `p` ON `g`.`person_id` = `p`.`person_id` WHERE `p`.`tenant_id` = 1 AND `p`.`person_id` > 1 AND `p`.`person_id` < 1001;
DELETE FROM `ospos_employees` WHERE `tenant_id` = 1 AND `person_id` > 1 AND `person_id` < 1001;
DELETE FROM `ospos_people` WHERE `tenant_id` = 1 AND `person_id` > 1 AND `person_id` < 1001;

-- Ensure main stock location exists for tenant 1
INSERT INTO `ospos_stock_locations` (`location_id`, `location_name`, `deleted`, `tenant_id`) VALUES
(1, 'Main Store', 0, 1)
ON DUPLICATE KEY UPDATE `location_name` = 'Main Store', `deleted` = 0, `tenant_id` = 1;

INSERT INTO `ospos_stock_locations` (`location_id`, `location_name`, `deleted`, `tenant_id`) VALUES
(1002, 'Warehouse A', 0, 1),
(1003, 'Warehouse B', 0, 1),
(1004, 'Front Counter', 0, 1),
(1005, 'Back Room', 0, 1),
(1006, 'Online Stock', 0, 1);

-- ----------------------------------------------------------
-- Suppliers (6) - person_id 1001-1006
-- ----------------------------------------------------------
INSERT INTO `ospos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('James', 'Miller', 1, '555-1001', 'james@freshfoods.com', '100 Supply Road', '', 'Springfield', 'IL', '62701', 'USA', 'Beverage supplier', 1001, 1),
('Sarah', 'Johnson', 0, '555-1002', 'sarah@bakeryco.com', '200 Baker St', '', 'Springfield', 'IL', '62702', 'USA', 'Bakery supplier', 1002, 1),
('David', 'Brown', 1, '555-1003', 'david@dairyfarm.com', '300 Farm Lane', '', 'Springfield', 'IL', '62703', 'USA', 'Dairy supplier', 1003, 1),
('Emily', 'Davis', 0, '555-1004', 'emily@groceryhub.com', '400 Market Ave', '', 'Springfield', 'IL', '62704', 'USA', 'Grocery supplier', 1004, 1),
('Robert', 'Wilson', 1, '555-1005', 'robert@homegoods.com', '500 Trade Blvd', '', 'Springfield', 'IL', '62705', 'USA', 'Household supplier', 1005, 1),
('Lisa', 'Taylor', 0, '555-1006', 'lisa@techparts.com', '600 Tech Park', '', 'Springfield', 'IL', '62706', 'USA', 'Electronics supplier', 1006, 1);

INSERT INTO `ospos_suppliers` (`person_id`, `company_name`, `agency_name`, `account_number`, `tax_id`, `deleted`, `category`, `tenant_id`) VALUES
(1001, 'Fresh Foods Ltd', 'Fresh Foods', 'SUP-001', '', 0, 0, 1),
(1002, 'Bakery Co', 'Bakery Co', 'SUP-002', '', 0, 0, 1),
(1003, 'Dairy Farm Inc', 'Dairy Farm', 'SUP-003', '', 0, 0, 1),
(1004, 'Grocery Hub', 'Grocery Hub', 'SUP-004', '', 0, 0, 1),
(1005, 'Home Goods Supply', 'Home Goods', 'SUP-005', '', 0, 0, 1),
(1006, 'Tech Parts LLC', 'Tech Parts', 'SUP-006', '', 0, 0, 1);

-- ----------------------------------------------------------
-- Customers (6) - person_id 1011-1016
-- ----------------------------------------------------------
INSERT INTO `ospos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('Alice', 'Walker', 0, '555-2001', 'alice@email.com', '10 Oak Street', '', 'Springfield', 'IL', '62710', 'USA', 'Regular customer', 1011, 1),
('Brian', 'Hall', 1, '555-2002', 'brian@email.com', '20 Pine Road', '', 'Springfield', 'IL', '62711', 'USA', 'Regular customer', 1012, 1),
('Carol', 'Allen', 0, '555-2003', 'carol@email.com', '30 Maple Ave', '', 'Springfield', 'IL', '62712', 'USA', 'Regular customer', 1013, 1),
('Daniel', 'Young', 1, '555-2004', 'daniel@email.com', '40 Cedar Lane', '', 'Springfield', 'IL', '62713', 'USA', 'Regular customer', 1014, 1),
('Eva', 'King', 0, '555-2005', 'eva@email.com', '50 Birch Way', '', 'Springfield', 'IL', '62714', 'USA', 'Regular customer', 1015, 1),
('Frank', 'Scott', 1, '555-2006', 'frank@email.com', '60 Elm Court', '', 'Springfield', 'IL', '62715', 'USA', 'Regular customer', 1016, 1);

INSERT INTO `ospos_customers` (`person_id`, `company_name`, `account_number`, `taxable`, `tax_id`, `sales_tax_code_id`, `package_id`, `points`, `deleted`, `discount`, `discount_type`, `date`, `employee_id`, `consent`, `tenant_id`) VALUES
(1011, NULL, 'CUST-001', 1, '', NULL, 2, 120, 0, 0.00, 0, '2026-05-01 09:00:00', 1, 1, 1),
(1012, 'Hall Trading', 'CUST-002', 1, '', NULL, 3, 80, 0, 5.00, 0, '2026-05-02 09:00:00', 1, 1, 1),
(1013, NULL, 'CUST-003', 1, '', NULL, 2, 45, 0, 0.00, 0, '2026-05-03 09:00:00', 1, 1, 1),
(1014, 'Young Corp', 'CUST-004', 1, '', NULL, 4, 200, 0, 10.00, 1, '2026-05-04 09:00:00', 1, 1, 1),
(1015, NULL, 'CUST-005', 1, '', NULL, 1, 15, 0, 0.00, 0, '2026-05-05 09:00:00', 1, 1, 1),
(1016, 'Scott Retail', 'CUST-006', 1, '', NULL, 5, 300, 0, 0.00, 0, '2026-05-06 09:00:00', 1, 1, 1);

-- ----------------------------------------------------------
-- Employees (5 + admin = 6) - person_id 1021-1025, password: pointofsale
-- ----------------------------------------------------------
INSERT INTO `ospos_people` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`, `tenant_id`) VALUES
('Maria', 'Cashier', 0, '555-3001', 'maria@store.com', '1 Staff Lane', '', 'Springfield', 'IL', '62720', 'USA', 'Cashier', 1021, 1),
('Tom', 'Manager', 1, '555-3002', 'tom@store.com', '2 Staff Lane', '', 'Springfield', 'IL', '62721', 'USA', 'Manager', 1022, 1),
('Nina', 'Sales', 0, '555-3003', 'nina@store.com', '3 Staff Lane', '', 'Springfield', 'IL', '62722', 'USA', 'Sales staff', 1023, 1),
('Paul', 'Stock', 1, '555-3004', 'paul@store.com', '4 Staff Lane', '', 'Springfield', 'IL', '62723', 'USA', 'Stock clerk', 1024, 1),
('Rita', 'Supervisor', 0, '555-3005', 'rita@store.com', '5 Staff Lane', '', 'Springfield', 'IL', '62724', 'USA', 'Supervisor', 1025, 1);

INSERT INTO `ospos_employees` (`username`, `password`, `person_id`, `deleted`, `hash_version`, `language`, `language_code`, `tenant_id`) VALUES
('maria', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1021, 0, 2, 'english', 'en', 1),
('tom', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1022, 0, 2, 'english', 'en', 1),
('nina', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1023, 0, 2, 'english', 'en', 1),
('paul', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1024, 0, 2, 'english', 'en', 1),
('rita', '$2y$10$vJBSMlD02EC7ENSrKfVQXuvq9tNRHMtcOA8MSK2NYS748HHWm.gcG', 1025, 0, 2, 'english', 'en', 1);

INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
SELECT `permission_id`, 1021, `menu_group` FROM `ospos_grants` WHERE `person_id` = 1;
INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
SELECT `permission_id`, 1022, `menu_group` FROM `ospos_grants` WHERE `person_id` = 1;
INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
SELECT `permission_id`, 1023, `menu_group` FROM `ospos_grants` WHERE `person_id` = 1;
INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
SELECT `permission_id`, 1024, `menu_group` FROM `ospos_grants` WHERE `person_id` = 1;
INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`)
SELECT `permission_id`, 1025, `menu_group` FROM `ospos_grants` WHERE `person_id` = 1;

-- ----------------------------------------------------------
-- Expense Categories (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_expense_categories` (`expense_category_id`, `category_name`, `category_description`, `deleted`, `tenant_id`) VALUES
(1001, 'Rent', 'Store rent payments', 0, 1),
(1002, 'Utilities', 'Electricity, water, internet', 0, 1),
(1003, 'Salaries', 'Staff payroll', 0, 1),
(1004, 'Marketing', 'Ads and promotions', 0, 1),
(1005, 'Supplies', 'Office and store supplies', 0, 1),
(1006, 'Maintenance', 'Repairs and upkeep', 0, 1);

-- ----------------------------------------------------------
-- Items (6) - item_id 1001-1006
-- ----------------------------------------------------------
INSERT INTO `ospos_items` (`name`, `category`, `supplier_id`, `item_number`, `description`, `cost_price`, `unit_price`, `reorder_level`, `receiving_quantity`, `item_id`, `pic_filename`, `allow_alt_description`, `is_serialized`, `deleted`, `stock_type`, `item_type`, `tax_category_id`, `qty_per_pack`, `pack_name`, `low_sell_item_id`, `hsn_code`, `tenant_id`) VALUES
('Coca Cola 330ml', 'Beverages', 1001, 'SKU-1001', 'Soft drink can', 0.80, 2.00, 20.000, 1.000, 1001, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('White Bread Loaf', 'Bakery', 1002, 'SKU-1002', 'Fresh white bread', 1.20, 4.00, 50.000, 1.000, 1002, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Fresh Milk 1L', 'Dairy', 1003, 'SKU-1003', 'Whole milk carton', 2.50, 5.50, 30.000, 1.000, 1003, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Large Eggs 12pk', 'Grocery', 1004, 'SKU-1004', 'Grade A eggs', 3.00, 6.00, 30.000, 1.000, 1004, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('Dish Soap 500ml', 'Household', 1005, 'SKU-1005', 'Liquid dish soap', 1.50, 4.50, 25.000, 1.000, 1005, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1),
('USB Cable Type-C', 'Electronics', 1006, 'SKU-1006', 'Charging cable', 4.00, 12.00, 15.000, 1.000, 1006, NULL, 0, 0, 0, 0, 0, NULL, 1.000, 'Each', 0, '', 1);

INSERT INTO `ospos_item_quantities` (`item_id`, `location_id`, `quantity`, `tenant_id`) VALUES
(1001, 1, 120.000, 1),
(1002, 1, 45.000, 1),
(1003, 1, 80.000, 1),
(1004, 1, 25.000, 1),
(1005, 1, 60.000, 1),
(1006, 1, 35.000, 1);

INSERT INTO `ospos_items_taxes` (`item_id`, `name`, `percent`, `tenant_id`) VALUES
(1001, 'Sales Tax', 8.000, 1),
(1002, 'Sales Tax', 8.000, 1),
(1003, 'Sales Tax', 8.000, 1),
(1004, 'Sales Tax', 8.000, 1),
(1005, 'Sales Tax', 8.000, 1),
(1006, 'Sales Tax', 8.000, 1);

-- ----------------------------------------------------------
-- Item Kits (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_item_kits` (`item_kit_id`, `item_kit_number`, `name`, `description`, `item_id`, `kit_discount`, `kit_discount_type`, `price_option`, `print_option`, `tenant_id`) VALUES
(1001, 'KIT-001', 'Breakfast Pack', 'Bread and milk combo', 0, 0.00, 0, 0, 0, 1),
(1002, 'KIT-002', 'Drink Bundle', 'Six cola cans', 0, 1.00, 0, 0, 0, 1),
(1003, 'KIT-003', 'Cleaning Kit', 'Dish soap pack', 0, 0.00, 0, 0, 0, 1),
(1004, 'KIT-004', 'Tech Starter', 'USB cable bundle', 0, 2.00, 0, 0, 0, 1),
(1005, 'KIT-005', 'Grocery Essentials', 'Eggs and milk', 0, 0.50, 0, 0, 0, 1),
(1006, 'KIT-006', 'Party Pack', 'Cola and bread', 0, 0.00, 0, 0, 0, 1);

INSERT INTO `ospos_item_kit_items` (`item_kit_id`, `item_id`, `quantity`, `kit_sequence`, `tenant_id`) VALUES
(1001, 1002, 1.000, 1, 1), (1001, 1003, 1.000, 2, 1),
(1002, 1001, 6.000, 1, 1),
(1003, 1005, 2.000, 1, 1),
(1004, 1006, 2.000, 1, 1),
(1005, 1003, 1.000, 1, 1), (1005, 1004, 1.000, 2, 1),
(1006, 1001, 4.000, 1, 1), (1006, 1002, 2.000, 2, 1);

-- ----------------------------------------------------------
-- Dinner Tables (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_dinner_tables` (`dinner_table_id`, `name`, `status`, `deleted`) VALUES
(1001, 'Table 1', 0, 0),
(1002, 'Table 2', 0, 0),
(1003, 'Table 3', 0, 0),
(1004, 'Take Away', 0, 0),
(1005, 'Delivery', 0, 0),
(1006, 'VIP Room', 0, 0);

-- ----------------------------------------------------------
-- Receivings (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_receivings` (`receiving_time`, `supplier_id`, `employee_id`, `comment`, `receiving_id`, `payment_type`, `reference`, `tenant_id`) VALUES
('2026-05-01 08:00:00', 1001, 1, 'Monthly beverage stock', 1001, 'Cash', 'PO-001', 1),
('2026-05-05 08:30:00', 1002, 1, 'Bakery delivery', 1002, 'Cash', 'PO-002', 1),
('2026-05-10 09:00:00', 1003, 1, 'Dairy restock', 1003, 'Check', 'PO-003', 1),
('2026-05-15 09:30:00', 1004, 1, 'Grocery order', 1004, 'Cash', 'PO-004', 1),
('2026-05-20 10:00:00', 1005, 1, 'Household supplies', 1005, 'Cash', 'PO-005', 1),
('2026-05-25 10:30:00', 1006, 1, 'Electronics order', 1006, 'Credit', 'PO-006', 1);

INSERT INTO `ospos_receivings_items` (`receiving_id`, `item_id`, `description`, `serialnumber`, `line`, `quantity_purchased`, `item_cost_price`, `item_unit_price`, `discount`, `discount_type`, `item_location`, `receiving_quantity`, `tenant_id`) VALUES
(1001, 1001, NULL, NULL, 1, 100.000, 0.80, 2.00, 0.00, 0, 1, 1.000, 1),
(1002, 1002, NULL, NULL, 1, 50.000, 1.20, 4.00, 0.00, 0, 1, 1.000, 1),
(1003, 1003, NULL, NULL, 1, 60.000, 2.50, 5.50, 0.00, 0, 1, 1.000, 1),
(1004, 1004, NULL, NULL, 1, 40.000, 3.00, 6.00, 0.00, 0, 1, 1.000, 1),
(1005, 1005, NULL, NULL, 1, 30.000, 1.50, 4.50, 0.00, 0, 1, 1.000, 1),
(1006, 1006, NULL, NULL, 1, 20.000, 4.00, 12.00, 0.00, 0, 1, 1.000, 1);

-- ----------------------------------------------------------
-- Sales (6) - powers dashboard charts
-- ----------------------------------------------------------
INSERT INTO `ospos_sales` (`sale_time`, `customer_id`, `employee_id`, `comment`, `invoice_number`, `dinner_table_id`, `sale_id`, `quote_number`, `sale_status`, `work_order_number`, `sale_type`, `tenant_id`) VALUES
('2026-05-07 10:15:00', 1011, 1, 'Morning sale', NULL, 1001, 1001, NULL, 0, NULL, 0, 1),
('2026-05-12 11:30:00', 1012, 1, 'Lunch rush', NULL, 1002, 1002, NULL, 0, NULL, 0, 1),
('2026-05-17 14:00:00', 1013, 1, 'Afternoon order', NULL, 1003, 1003, NULL, 0, NULL, 0, 1),
('2026-05-22 16:45:00', 1014, 1, 'Corporate purchase', NULL, 1004, 1004, NULL, 0, NULL, 0, 1),
('2026-05-28 18:20:00', 1015, 1, 'Evening sale', NULL, 1005, 1005, NULL, 0, NULL, 0, 1),
('2026-06-05 12:00:00', 1016, 1, 'Today sale', NULL, 1006, 1006, NULL, 0, NULL, 0, 1);

INSERT INTO `ospos_sales_items` (`sale_id`, `item_id`, `description`, `serialnumber`, `line`, `quantity_purchased`, `item_cost_price`, `item_unit_price`, `discount`, `discount_type`, `item_location`, `print_option`, `tenant_id`) VALUES
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

INSERT INTO `ospos_sales_payments` (`payment_id`, `sale_id`, `payment_type`, `payment_amount`, `cash_refund`, `cash_adjustment`, `employee_id`, `payment_time`, `reference_code`, `tenant_id`) VALUES
(1001, 1001, 'Cash', 8.00, 0.00, 0, 1, '2026-05-07 10:15:00', '', 1),
(1002, 1002, 'Debit', 12.00, 0.00, 0, 1, '2026-05-12 11:30:00', '', 1),
(1003, 1003, 'Credit', 17.00, 0.00, 0, 1, '2026-05-17 14:00:00', '', 1),
(1004, 1004, 'Cash', 46.50, 0.00, 0, 1, '2026-05-22 16:45:00', '', 1),
(1005, 1005, 'Cash', 40.00, 0.00, 0, 1, '2026-05-28 18:20:00', '', 1),
(1006, 1006, 'Cash', 23.00, 0.00, 0, 1, '2026-06-05 12:00:00', '', 1);

-- ----------------------------------------------------------
-- Expenses (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_expenses` (`expense_id`, `date`, `amount`, `payment_type`, `expense_category_id`, `description`, `employee_id`, `deleted`, `supplier_tax_code`, `tax_amount`, `supplier_id`, `tenant_id`) VALUES
(1001, '2026-05-01 08:00:00', 1200.00, 'Cash', 1001, 'May store rent', 1, 0, '', 0.00, NULL, 1),
(1002, '2026-05-05 09:00:00', 180.00, 'Cash', 1002, 'Electricity bill', 1, 0, '', 0.00, NULL, 1),
(1003, '2026-05-10 09:00:00', 2500.00, 'Check', 1003, 'Staff payroll', 1, 0, '', 0.00, NULL, 1),
(1004, '2026-05-15 10:00:00', 350.00, 'Cash', 1004, 'Facebook ads', 1, 0, '', 0.00, NULL, 1),
(1005, '2026-05-20 10:30:00', 95.00, 'Cash', 1005, 'Receipt paper', 1, 0, '', 0.00, NULL, 1),
(1006, '2026-05-25 11:00:00', 420.00, 'Credit', 1006, 'AC repair', 1, 0, '', 0.00, NULL, 1);

-- ----------------------------------------------------------
-- Gift Cards (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_giftcards` (`record_time`, `giftcard_id`, `giftcard_number`, `value`, `deleted`, `person_id`, `tenant_id`) VALUES
('2026-05-01 10:00:00', 1001, 'GC-10001', 25.00, 0, 1011, 1),
('2026-05-05 10:00:00', 1002, 'GC-10002', 50.00, 0, 1012, 1),
('2026-05-10 10:00:00', 1003, 'GC-10003', 100.00, 0, 1013, 1),
('2026-05-15 10:00:00', 1004, 'GC-10004', 75.00, 0, 1014, 1),
('2026-05-20 10:00:00', 1005, 'GC-10005', 30.00, 0, 1015, 1),
('2026-05-25 10:00:00', 1006, 'GC-10006', 150.00, 0, 1016, 1);

-- ----------------------------------------------------------
-- Cash Ups (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_cash_up` (`cashup_id`, `open_date`, `close_date`, `open_amount_cash`, `transfer_amount_cash`, `note`, `closed_amount_cash`, `closed_amount_card`, `closed_amount_check`, `closed_amount_total`, `description`, `open_employee_id`, `close_employee_id`, `deleted`, `closed_amount_due`, `tenant_id`) VALUES
(1001, '2026-05-07 08:00:00', '2026-05-07 20:00:00', 100.00, 0.00, 0, 180.00, 0.00, 0.00, 180.00, 'Day 1 close', 1, 1, 0, 0.00, 1),
(1002, '2026-05-12 08:00:00', '2026-05-12 20:00:00', 100.00, 0.00, 0, 50.00, 12.00, 0.00, 62.00, 'Day 2 close', 1, 1, 0, 0.00, 1),
(1003, '2026-05-17 08:00:00', '2026-05-17 20:00:00', 100.00, 0.00, 0, 0.00, 17.00, 0.00, 17.00, 'Day 3 close', 1, 1, 0, 0.00, 1),
(1004, '2026-05-22 08:00:00', '2026-05-22 20:00:00', 100.00, 0.00, 0, 146.50, 0.00, 0.00, 146.50, 'Day 4 close', 1, 1, 0, 0.00, 1),
(1005, '2026-05-28 08:00:00', '2026-05-28 20:00:00', 100.00, 0.00, 0, 240.00, 0.00, 0.00, 240.00, 'Day 5 close', 1, 1, 0, 0.00, 1),
(1006, '2026-06-05 08:00:00', '2026-06-05 20:00:00', 100.00, 0.00, 0, 123.00, 0.00, 0.00, 123.00, 'Today close', 1, 1, 0, 0.00, 1);

-- ----------------------------------------------------------
-- Inventory Transactions (6)
-- ----------------------------------------------------------
INSERT INTO `ospos_inventory` (`trans_id`, `trans_items`, `trans_user`, `trans_date`, `trans_comment`, `trans_location`, `trans_inventory`, `tenant_id`) VALUES
(1001, 1001, 1, '2026-05-01 07:30:00', 'Opening stock count', 1, 120.000, 1),
(1002, 1002, 1, '2026-05-05 07:30:00', 'Bread restock', 1, 45.000, 1),
(1003, 1003, 1, '2026-05-10 07:30:00', 'Milk restock', 1, 80.000, 1),
(1004, 1004, 1, '2026-05-15 07:30:00', 'Eggs restock', 1, 25.000, 1),
(1005, 1005, 1, '2026-05-20 07:30:00', 'Soap restock', 1, 60.000, 1),
(1006, 1006, 1, '2026-05-25 07:30:00', 'Cable restock', 1, 35.000, 1);

SET FOREIGN_KEY_CHECKS = 1;
