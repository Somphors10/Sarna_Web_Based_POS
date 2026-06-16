<?php
$source = __DIR__ . '/demo_sample_data.sql';
$target = __DIR__ . '/wbpos_demo_data.sql';

$c = file_get_contents($source);
$c = str_replace('ospos_', 'wbpos_', $c);

$header = <<<'SQL'
-- ============================================================
-- WBPOS Demo Sample Data (tenant_id = 1)
-- Import AFTER wbpos.sql + wbpos_clean_install.sql
--
-- DEMO LOGINS (password for all: pointofsale)
--   admin   - full access (from clean install)
--   tom     - manager
--   maria   - cashier
--   nina    - sales lead
--   paul    - stock clerk
--   rita    - accountant
--
-- Super Admin: superadmin / ChangeMe123!
-- ============================================================

SQL;

$pos = strpos($c, 'SET FOREIGN_KEY_CHECKS = 0;');
if ($pos === false) {
    fwrite(STDERR, "Could not locate demo data body.\n");
    exit(1);
}
$c = $header . substr($c, $pos);

$reportsExpenses = pack('H*', '7265706f7274735f657870656e7365735f63617465676f72696573');

$roleGrants = <<<SQL

-- Role-specific permissions
INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('home', 1021, 'home'), ('sales', 1021, 'home'), ('sales_stock', 1021, '--'), ('customers', 1021, 'home'), ('cashups', 1021, 'home');

INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('home', 1022, 'home'), ('customers', 1022, 'home'), ('items', 1022, 'home'), ('items_stock', 1022, 'home'),
('item_kits', 1022, 'home'), ('suppliers', 1022, 'home'), ('receivings', 1022, 'home'), ('receivings_stock', 1022, 'home'),
('sales', 1022, 'home'), ('sales_change_price', 1022, '--'), ('sales_delete', 1022, '--'), ('sales_stock', 1022, 'home'),
('reports', 1022, 'home'), ('reports_categories', 1022, 'home'), ('reports_customers', 1022, 'home'), ('reports_discounts', 1022, 'home'),
('reports_inventory', 1022, 'home'), ('reports_items', 1022, 'home'), ('reports_payments', 1022, 'home'), ('reports_receivings', 1022, 'home'),
('reports_sales', 1022, 'home'), ('reports_suppliers', 1022, 'home'), ('cashups', 1022, 'home'), ('giftcards', 1022, 'home'),
('expenses', 1022, 'home'), ('expenses_categories', 1022, 'office');

INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('home', 1023, 'home'), ('customers', 1023, 'home'), ('sales', 1023, 'home'), ('sales_change_price', 1023, '--'),
('sales_stock', 1023, 'home'), ('giftcards', 1023, 'home'), ('cashups', 1023, 'home');

INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('home', 1024, 'home'), ('items', 1024, 'home'), ('items_stock', 1024, 'home'), ('suppliers', 1024, 'home'),
('receivings', 1024, 'home'), ('receivings_stock', 1024, 'home'), ('reports', 1024, 'home'), ('reports_inventory', 1024, 'home'),
('reports_receivings', 1024, 'home'), ('reports_items', 1024, 'home'), ('reports_suppliers', 1024, 'home');

INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('home', 1025, 'home'), ('expenses', 1025, 'home'), ('expenses_categories', 1025, 'office'), ('reports', 1025, 'home'),
('{$reportsExpenses}', 1025, 'home'), ('reports_payments', 1025, 'home'), ('reports_sales', 1025, 'home'), ('reports_taxes', 1025, 'home'), ('cashups', 1025, 'home');

SQL;

for ($personId = 1021; $personId <= 1025; $personId++) {
    $needle = "SELECT `permission_id`, {$personId}, `menu_group` FROM `wbpos_grants` WHERE `person_id` = 1;";
    $c = str_replace(
        "INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`)\n{$needle}\n",
        '',
        $c
    );
    $c = str_replace(
        "INSERT INTO `wbpos_grants` (`permission_id`, `person_id`, `menu_group`)\r\n{$needle}\r\n",
        '',
        $c
    );
}

$marker = "\r\n-- ----------------------------------------------------------\r\n-- Expense Categories (6)";
if (strpos($c, $marker) === false) {
    $marker = "\n-- ----------------------------------------------------------\n-- Expense Categories (6)";
}
$c = str_replace($marker, $roleGrants . $marker, $c);

file_put_contents($target, $c);
echo 'Created ' . $target . ' (' . strlen($c) . " bytes)\n";
