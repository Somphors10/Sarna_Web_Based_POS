<?php
/**
 * Build wbpos.sql from ospos.sql (schema + system seed).
 * Run: php app/Database/build_wbpos_database.php
 */

$source = __DIR__ . DIRECTORY_SEPARATOR . 'ospos.sql';
$target = __DIR__ . DIRECTORY_SEPARATOR . 'wbpos.sql';

if (!is_file($source)) {
    fwrite(STDERR, "Missing source file: {$source}\n");
    exit(1);
}

$sql = file_get_contents($source);

$replacements = [
    '`ospos`' => '`wbpos`',
    'Database: `ospos`' => 'Database: `wbpos`',
    'ospos_' => 'wbpos_',
    'Web-Based POS' => 'WBPOS',
    'Default Tenant' => 'WBPOS Demo Store',
    'ALREADY HAVE THIS DATABASE? Do NOT re-import ospos.sql!' => 'FRESH INSTALL ONLY. For existing DB use wbpos_demo_data.sql after wbpos_clean_install.sql',
    'Import only: app/Database/demo_sample_data.sql' => 'Import: app/Database/wbpos_demo_data.sql',
    'import_demo_data.bat' => 'import_wbpos.bat',
    'Login: admin / pointofsale' => 'See wbpos_demo_data.sql header for all demo logins',
];

$sql = str_replace(array_keys($replacements), array_values($replacements), $sql);

$header = <<<'SQL'
-- ============================================================
-- WBPOS Database (generated from ospos.sql)
-- Database name : wbpos
-- Table prefix  : wbpos_
--
-- FRESH INSTALL (XAMPP):
--   1. Double-click app/Database/import_wbpos.bat
--   OR run manually:
--   2. mysql -u root < app/Database/wbpos.sql
--   3. mysql -u root wbpos < app/Database/wbpos_clean_install.sql
--   4. mysql -u root wbpos < app/Database/wbpos_demo_data.sql
--
-- Then set .env database to wbpos and DBPrefix to wbpos_
-- ============================================================

DROP DATABASE IF EXISTS `wbpos`;
CREATE DATABASE `wbpos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `wbpos`;

SQL;

$sql = preg_replace('/^-- phpMyAdmin SQL Dump.*?\n\n/s', '', $sql, 1);
$sql = preg_replace('/^SET SQL_MODE.*?\n\n/s', '', $sql, 1);

$output = $header . $sql;

if (file_put_contents($target, $output) === false) {
    fwrite(STDERR, "Failed to write {$target}\n");
    exit(1);
}

echo "Created {$target} (" . number_format(strlen($output)) . " bytes)\n";
