<?php
header('Content-Type: text/plain; charset=utf-8');
echo 'PHP ' . PHP_VERSION . PHP_EOL;
foreach (['intl', 'mysqli', 'mbstring', 'gd', 'curl', 'openssl'] as $ext) {
    echo $ext . ': ' . (extension_loaded($ext) ? 'ON' : 'OFF') . PHP_EOL;
}
$writable = realpath(__DIR__ . '/../writable') ?: (__DIR__ . '/../writable');
echo 'writable: ' . $writable . PHP_EOL;
echo 'writable/session: ' . (is_writable($writable . '/session') ? 'yes' : 'no') . PHP_EOL;
echo 'writable/cache: ' . (is_writable($writable . '/cache') ? 'yes' : 'no') . PHP_EOL;
echo 'NumberFormatter: ' . (class_exists('NumberFormatter') ? 'yes' : 'no') . PHP_EOL;
