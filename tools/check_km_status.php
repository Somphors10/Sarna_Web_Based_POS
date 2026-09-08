<?php
$j = json_decode(file_get_contents(__DIR__ . '/km_translations.json'), true);
echo 'json_keys=' . count($j) . PHP_EOL;
$i = 0;
foreach ($j as $k => $v) {
    echo $k . ' => ' . $v . PHP_EOL;
    if (++$i >= 10) {
        break;
    }
}
echo "--- remaining ---\n";
passthru('php ' . escapeshellarg(__DIR__ . '/find_untranslated_km.php'));
