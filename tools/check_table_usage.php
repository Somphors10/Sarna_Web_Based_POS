<?php

/**
 * Compare database tables vs actual PHP usage (Models/Controllers/Libraries/Helpers/Views).
 */

$root = dirname(__DIR__);
$app = $root . DIRECTORY_SEPARATOR . 'app';

$tablesFromSql = [];
$sql = file_get_contents($app . '/Database/wbpos.sql');
preg_match_all('/CREATE TABLE `wbpos_([^`]+)`/', $sql, $m);
foreach ($m[1] as $name) {
    $tablesFromSql[$name] = true;
}

$extra = [
    'tenant_logins',
    'plan_features',
    'platform_template_meta',
    'password_reset_requests',
    'platform_payments',
    'platform_alerts',
];
foreach ($extra as $name) {
    $tablesFromSql[$name] = true;
}

$aliases = [
    'employees' => ['Employees'],
    'customers_packages' => ['customer_packages'],
    'items_taxes' => ['item_taxes'],
];

$live = [];
mysqli_report(MYSQLI_REPORT_OFF);
try {
    $mysqli = @new mysqli('localhost', 'root', '', 'wbpos', 3306);
    if ($mysqli instanceof mysqli && !$mysqli->connect_error) {
        $res = $mysqli->query('SHOW TABLES');
        if ($res) {
            while ($row = $res->fetch_row()) {
                $live[] = preg_replace('/^(wbpos_|ospos_)/', '', $row[0]);
            }
        }
        $mysqli->close();
    }
} catch (Throwable $e) {
    $live = [];
}

$tables = $live !== [] ? $live : array_keys($tablesFromSql);
sort($tables);

$scanDirs = ['Controllers', 'Models', 'Libraries', 'Helpers', 'Views', 'Config', 'Filters', 'Commands'];
$files = [];
foreach ($scanDirs as $dir) {
    $path = $app . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($path)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (strtolower($file->getExtension()) === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

$contents = [];
foreach ($files as $file) {
    $contents[$file] = file_get_contents($file);
}

$used = [];
$unused = [];
$hits = [];

foreach ($tables as $table) {
    $names = array_merge([$table], $aliases[$table] ?? []);
    $patterns = [];
    foreach ($names as $name) {
        $patterns[] = "'" . $name . "'";
        $patterns[] = '"' . $name . '"';
        $patterns[] = '`' . $name . '`';
        $patterns[] = 'wbpos_' . $name;
        $patterns[] = 'ospos_' . $name;
        $patterns[] = '->table(\'' . $name . '\'';
        $patterns[] = '->table("' . $name . '"';
        $patterns[] = "protected \$table = '" . $name . "'";
        $patterns[] = 'FROM ' . $name;
        $patterns[] = 'JOIN ' . $name;
        $patterns[] = 'INTO ' . $name;
        $patterns[] = 'UPDATE ' . $name;
    }
    $count = 0;
    $sample = [];
    foreach ($contents as $file => $text) {
        foreach ($patterns as $p) {
            $c = substr_count($text, $p);
            if ($c > 0) {
                $count += $c;
                if (count($sample) < 3) {
                    $sample[] = str_replace($app . DIRECTORY_SEPARATOR, '', $file);
                }
            }
        }
    }
    $hits[$table] = ['count' => $count, 'files' => array_unique($sample)];
    if ($count > 0) {
        $used[] = $table;
    } else {
        $unused[] = $table;
    }
}

echo "Source: " . ($live !== [] ? 'local MySQL wbpos (' . count($live) . ' tables)' : 'wbpos.sql + later tables (' . count($tables) . ' tables)') . PHP_EOL;
echo 'USED: ' . count($used) . PHP_EOL;
echo 'NOT USED / no app code hit: ' . count($unused) . PHP_EOL;
echo PHP_EOL . '=== USED ===' . PHP_EOL;
foreach ($used as $t) {
    echo sprintf("  %-32s  hits=%-4d  %s\n", $t, $hits[$t]['count'], implode(', ', $hits[$t]['files']));
}
echo PHP_EOL . '=== NOT USED (no Models/Controllers/Libraries/Helpers/Views/Config reference) ===' . PHP_EOL;
foreach ($unused as $t) {
    echo "  $t\n";
}
