<?php
$enDir = __DIR__ . '/../app/Language/en';
$kmDir = __DIR__ . '/../app/Language/km';
foreach (glob($enDir . '/*.php') as $enFile) {
    $name = basename($enFile);
    $en = include $enFile;
    $kmFile = $kmDir . '/' . $name;
    if (!is_file($kmFile)) {
        echo "MISSING FILE $name\n";
        continue;
    }
    $km = include $kmFile;
    foreach ($en as $k => $v) {
        if (!is_string($v) || trim($v) === '') {
            continue;
        }
        $kv = isset($km[$k]) ? trim((string)$km[$k]) : '';
        if ($kv === '' || !preg_match('/\p{Khmer}/u', $kv)) {
            echo "$name|$k => [$kv]\n";
        }
    }
}
