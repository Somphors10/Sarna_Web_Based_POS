<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

echo 'PHP ' . PHP_VERSION . PHP_EOL;
foreach (['intl', 'mysqli', 'mbstring', 'gd', 'curl', 'openssl', 'json', 'xml', 'bcmath'] as $ext) {
    echo $ext . ': ' . (extension_loaded($ext) ? 'ON' : 'OFF') . PHP_EOL;
}

$root = realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');
echo PHP_EOL . 'root: ' . $root . PHP_EOL;
echo 'locale_helper: ' . (is_file($root . '/app/Helpers/locale_helper.php') ? 'yes' : 'NO') . PHP_EOL;
echo '.env: ' . (is_file($root . '/.env') ? 'yes' : 'NO') . PHP_EOL;
echo 'vendor: ' . (is_dir($root . '/vendor') ? 'yes' : 'NO') . PHP_EOL;
$w = $root . '/writable';
echo 'writable/session: ' . (is_writable($w . '/session') ? 'yes' : 'no') . PHP_EOL;
echo 'writable/cache: ' . (is_writable($w . '/cache') ? 'yes' : 'no') . PHP_EOL;
echo 'NumberFormatter: ' . (class_exists('NumberFormatter') ? 'yes' : 'no') . PHP_EOL;

$helper = $root . '/app/Helpers/locale_helper.php';
if (is_file($helper)) {
    $src = file_get_contents($helper);
    echo 'locale fallback: ' . (str_contains($src, 'locale_intl_available') ? 'yes' : 'NO (old file)') . PHP_EOL;
}

$envFile = $root . '/.env';
if (is_file($envFile)) {
    $env = file_get_contents($envFile);
    $val = static function (string $key) use ($env): string {
        if (preg_match('/' . preg_quote($key, '/') . "\s*=\s*'([^']*)'/", $env, $m)) {
            return $m[1];
        }
        return '';
    };
    $host = $val('database.default.hostname') ?: 'localhost';
    $db = $val('database.default.database');
    $user = $val('database.default.username');
    $pass = $val('database.default.password');
    echo 'session.useFile: ' . (str_contains($env, 'session.useFile') ? 'yes' : 'NO') . PHP_EOL;
    echo 'DB name: ' . $db . PHP_EOL;
    echo 'DB user: ' . $user . PHP_EOL;

    if (function_exists('mysqli_connect') && $db !== '') {
        mysqli_report(MYSQLI_REPORT_OFF);
        $mysqli = @mysqli_connect($host, $user, $pass, $db);
        if (!$mysqli) {
            echo 'DB connect: FAIL ' . mysqli_connect_error() . PHP_EOL;
        } else {
            echo 'DB connect: OK' . PHP_EOL;
            $res = $mysqli->query("SHOW TABLES LIKE 'wbpos_%'");
            echo 'wbpos tables: ' . ($res ? (string) $res->num_rows : '0') . PHP_EOL;
            $mysqli->close();
        }
    }
}

echo PHP_EOL . 'Done. This page does not start login. Next open /login' . PHP_EOL;
