-- WBPOS ERD for draw.io — Advanced → SQL...
-- IMPORTANT: draw.io SQL parser breaks on ENUM(...). Use VARCHAR instead.
-- After Insert, relationship LINES should appear (crow's foot).
-- If lines still missing: select all tables → Arrange → Layout → Horizontal Flow
--
-- How to use:
--   1. Delete old tables on canvas
--   2. Advanced → SQL...
--   3. Paste this whole file → Insert

CREATE TABLE wbpos_plans (
  plan_id BIGINT NOT NULL,
  plan_code VARCHAR(64),
  plan_name VARCHAR(120),
  price_monthly DECIMAL(12,2),
  max_users INT,
  max_locations INT,
  max_items INT,
  is_active TINYINT,
  created_at TIMESTAMP,
  PRIMARY KEY (plan_id)
);

CREATE TABLE wbpos_platform_admins (
  admin_id INT NOT NULL,
  username VARCHAR(64),
  password_hash VARCHAR(255),
  full_name VARCHAR(120),
  email VARCHAR(255),
  status VARCHAR(20),
  created_at TIMESTAMP,
  PRIMARY KEY (admin_id)
);

CREATE TABLE wbpos_tenants (
  tenant_id BIGINT NOT NULL,
  tenant_code VARCHAR(64),
  company_name VARCHAR(255),
  status VARCHAR(32),
  timezone VARCHAR(64),
  currency_code CHAR(3),
  db_hostname VARCHAR(190),
  db_port INT,
  db_name VARCHAR(190),
  db_username VARCHAR(190),
  db_password VARCHAR(255),
  db_prefix VARCHAR(50),
  template_version VARCHAR(32),
  isolated_at DATETIME,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY (tenant_id)
);

CREATE TABLE wbpos_subscriptions (
  subscription_id BIGINT NOT NULL,
  tenant_id BIGINT,
  plan_id BIGINT,
  status VARCHAR(20),
  trial_ends_at DATETIME,
  period_start DATETIME,
  period_end DATETIME,
  cancel_at_period_end TINYINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  PRIMARY KEY (subscription_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id),
  FOREIGN KEY (plan_id) REFERENCES wbpos_plans(plan_id)
);

CREATE TABLE wbpos_subscription_requests (
  request_id BIGINT NOT NULL,
  company_name VARCHAR(255),
  tenant_code VARCHAR(64),
  business_type VARCHAR(64),
  address VARCHAR(255),
  city VARCHAR(120),
  country VARCHAR(80),
  tax_id VARCHAR(64),
  owner_first_name VARCHAR(100),
  owner_last_name VARCHAR(100),
  owner_email VARCHAR(255),
  owner_phone VARCHAR(255),
  owner_username VARCHAR(50),
  owner_password_hash VARCHAR(255),
  plan_id BIGINT,
  payment_reference VARCHAR(100),
  payment_token VARCHAR(64),
  email_verify_token VARCHAR(64),
  email_verified_at DATETIME,
  status VARCHAR(20),
  notes TEXT,
  reviewed_by_admin_id INT,
  created_at TIMESTAMP,
  reviewed_at DATETIME,
  PRIMARY KEY (request_id),
  FOREIGN KEY (plan_id) REFERENCES wbpos_plans(plan_id),
  FOREIGN KEY (reviewed_by_admin_id) REFERENCES wbpos_platform_admins(admin_id)
);

CREATE TABLE wbpos_tenant_config (
  tenant_id BIGINT NOT NULL,
  config_key VARCHAR(100) NOT NULL,
  config_value TEXT,
  PRIMARY KEY (tenant_id, config_key),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_people (
  person_id INT NOT NULL,
  first_name VARCHAR(255),
  last_name VARCHAR(255),
  gender INT,
  phone_number VARCHAR(255),
  email VARCHAR(255),
  address_1 VARCHAR(255),
  address_2 VARCHAR(255),
  city VARCHAR(255),
  state VARCHAR(255),
  zip VARCHAR(255),
  country VARCHAR(255),
  comments TEXT,
  tenant_id BIGINT,
  PRIMARY KEY (person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_tenant_users (
  tenant_id BIGINT NOT NULL,
  person_id INT NOT NULL,
  tenant_role VARCHAR(20),
  is_active TINYINT,
  created_at TIMESTAMP,
  PRIMARY KEY (tenant_id, person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id),
  FOREIGN KEY (person_id) REFERENCES wbpos_people(person_id)
);

CREATE TABLE wbpos_employees (
  person_id INT NOT NULL,
  username VARCHAR(255),
  password VARCHAR(255),
  deleted TINYINT,
  hash_version TINYINT,
  language VARCHAR(48),
  language_code VARCHAR(8),
  tenant_id BIGINT,
  PRIMARY KEY (person_id),
  FOREIGN KEY (person_id) REFERENCES wbpos_people(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_customers (
  person_id INT NOT NULL,
  company_name VARCHAR(255),
  account_number VARCHAR(255),
  taxable TINYINT,
  tax_id VARCHAR(32),
  sales_tax_code_id INT,
  package_id INT,
  points INT,
  deleted TINYINT,
  discount DECIMAL(15,2),
  discount_type TINYINT,
  date TIMESTAMP,
  employee_id INT,
  consent TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (person_id),
  FOREIGN KEY (person_id) REFERENCES wbpos_people(person_id),
  FOREIGN KEY (employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_suppliers (
  person_id INT NOT NULL,
  company_name VARCHAR(255),
  agency_name VARCHAR(255),
  account_number VARCHAR(255),
  tax_id VARCHAR(32),
  deleted TINYINT,
  category TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (person_id),
  FOREIGN KEY (person_id) REFERENCES wbpos_people(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_stock_locations (
  location_id INT NOT NULL,
  location_name VARCHAR(255),
  deleted TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (location_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_items (
  item_id INT NOT NULL,
  name VARCHAR(255),
  category VARCHAR(255),
  supplier_id INT,
  item_number VARCHAR(255),
  description VARCHAR(255),
  cost_price DECIMAL(15,2),
  unit_price DECIMAL(15,2),
  reorder_level DECIMAL(15,3),
  receiving_quantity DECIMAL(15,3),
  deleted TINYINT,
  stock_type TINYINT,
  item_type TINYINT,
  tax_category_id INT,
  tenant_id BIGINT,
  PRIMARY KEY (item_id),
  FOREIGN KEY (supplier_id) REFERENCES wbpos_suppliers(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_item_quantities (
  item_id INT NOT NULL,
  location_id INT NOT NULL,
  quantity DECIMAL(15,3),
  tenant_id BIGINT,
  PRIMARY KEY (item_id, location_id),
  FOREIGN KEY (item_id) REFERENCES wbpos_items(item_id),
  FOREIGN KEY (location_id) REFERENCES wbpos_stock_locations(location_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_inventory (
  trans_id INT NOT NULL,
  trans_items INT,
  trans_user INT,
  trans_date TIMESTAMP,
  trans_comment TEXT,
  trans_location INT,
  trans_inventory DECIMAL(15,3),
  tenant_id BIGINT,
  PRIMARY KEY (trans_id),
  FOREIGN KEY (trans_items) REFERENCES wbpos_items(item_id),
  FOREIGN KEY (trans_user) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (trans_location) REFERENCES wbpos_stock_locations(location_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_sales (
  sale_id INT NOT NULL,
  sale_time TIMESTAMP,
  customer_id INT,
  employee_id INT,
  comment TEXT,
  invoice_number VARCHAR(32),
  dinner_table_id INT,
  quote_number VARCHAR(32),
  sale_status TINYINT,
  work_order_number VARCHAR(32),
  sale_type TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (sale_id),
  FOREIGN KEY (customer_id) REFERENCES wbpos_customers(person_id),
  FOREIGN KEY (employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_sales_items (
  sale_id INT NOT NULL,
  item_id INT NOT NULL,
  line INT NOT NULL,
  description VARCHAR(255),
  serialnumber VARCHAR(30),
  quantity_purchased DECIMAL(15,3),
  item_cost_price DECIMAL(15,2),
  item_unit_price DECIMAL(15,2),
  discount DECIMAL(15,2),
  discount_type TINYINT,
  item_location INT,
  print_option TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (sale_id, item_id, line),
  FOREIGN KEY (sale_id) REFERENCES wbpos_sales(sale_id),
  FOREIGN KEY (item_id) REFERENCES wbpos_items(item_id),
  FOREIGN KEY (item_location) REFERENCES wbpos_stock_locations(location_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_sales_payments (
  payment_id INT NOT NULL,
  sale_id INT,
  payment_type VARCHAR(40),
  payment_amount DECIMAL(15,2),
  cash_refund DECIMAL(15,2),
  cash_adjustment TINYINT,
  employee_id INT,
  payment_time TIMESTAMP,
  reference_code VARCHAR(40),
  tenant_id BIGINT,
  PRIMARY KEY (payment_id),
  FOREIGN KEY (sale_id) REFERENCES wbpos_sales(sale_id),
  FOREIGN KEY (employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_receivings (
  receiving_id INT NOT NULL,
  receiving_time TIMESTAMP,
  supplier_id INT,
  employee_id INT,
  comment TEXT,
  payment_type VARCHAR(20),
  reference VARCHAR(32),
  tenant_id BIGINT,
  PRIMARY KEY (receiving_id),
  FOREIGN KEY (supplier_id) REFERENCES wbpos_suppliers(person_id),
  FOREIGN KEY (employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_receivings_items (
  receiving_id INT NOT NULL,
  item_id INT NOT NULL,
  line INT NOT NULL,
  description VARCHAR(30),
  serialnumber VARCHAR(30),
  quantity_purchased DECIMAL(15,3),
  item_cost_price DECIMAL(15,2),
  item_unit_price DECIMAL(15,2),
  discount DECIMAL(15,2),
  discount_type TINYINT,
  item_location INT,
  receiving_quantity DECIMAL(15,3),
  tenant_id BIGINT,
  PRIMARY KEY (receiving_id, item_id, line),
  FOREIGN KEY (receiving_id) REFERENCES wbpos_receivings(receiving_id),
  FOREIGN KEY (item_id) REFERENCES wbpos_items(item_id),
  FOREIGN KEY (item_location) REFERENCES wbpos_stock_locations(location_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_expense_categories (
  expense_category_id INT NOT NULL,
  category_name VARCHAR(255),
  category_description VARCHAR(255),
  deleted TINYINT,
  tenant_id BIGINT,
  PRIMARY KEY (expense_category_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_expenses (
  expense_id INT NOT NULL,
  date TIMESTAMP,
  amount DECIMAL(15,2),
  payment_type VARCHAR(40),
  expense_category_id INT,
  description VARCHAR(255),
  employee_id INT,
  deleted TINYINT,
  supplier_tax_code VARCHAR(255),
  tax_amount DECIMAL(15,2),
  supplier_id INT,
  tenant_id BIGINT,
  PRIMARY KEY (expense_id),
  FOREIGN KEY (expense_category_id) REFERENCES wbpos_expense_categories(expense_category_id),
  FOREIGN KEY (employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (supplier_id) REFERENCES wbpos_suppliers(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_cash_up (
  cashup_id INT NOT NULL,
  open_date TIMESTAMP,
  close_date TIMESTAMP,
  open_amount_cash DECIMAL(15,2),
  transfer_amount_cash DECIMAL(15,2),
  note TINYINT,
  closed_amount_cash DECIMAL(15,2),
  closed_amount_card DECIMAL(15,2),
  closed_amount_check DECIMAL(15,2),
  closed_amount_total DECIMAL(15,2),
  description VARCHAR(255),
  open_employee_id INT,
  close_employee_id INT,
  deleted TINYINT,
  closed_amount_due DECIMAL(15,2),
  tenant_id BIGINT,
  PRIMARY KEY (cashup_id),
  FOREIGN KEY (open_employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (close_employee_id) REFERENCES wbpos_employees(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);

CREATE TABLE wbpos_giftcards (
  giftcard_id INT NOT NULL,
  record_time TIMESTAMP,
  giftcard_number VARCHAR(255),
  value DECIMAL(15,2),
  deleted TINYINT,
  person_id INT,
  tenant_id BIGINT,
  PRIMARY KEY (giftcard_id),
  FOREIGN KEY (person_id) REFERENCES wbpos_people(person_id),
  FOREIGN KEY (tenant_id) REFERENCES wbpos_tenants(tenant_id)
);
