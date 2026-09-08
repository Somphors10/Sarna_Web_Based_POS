<?php
$enDir = __DIR__ . '/../app/Language/en';
$kmDir = __DIR__ . '/../app/Language/km';
foreach (glob($enDir . '/*.php') as $enFile) {
    $name = basename($enFile);
    $en = include $enFile;
    $km = include $kmDir . '/' . $name;
    if (!is_array($en) || !is_array($km)) {
        continue;
    }
    $empty = 0;
    $kh = 0;
    $enOnly = 0;
    foreach ($en as $k => $v) {
        if (!is_string($v) || trim($v) === '') {
            continue;
        }
        $kv = isset($km[$k]) ? trim((string)$km[$k]) : '';
        if ($kv === '') {
            $empty++;
        } elseif (preg_match('/\p{Khmer}/u', $kv)) {
            $kh++;
        } else {
            $enOnly++;
        }
    }
    if ($empty || $enOnly) {
        echo "$name empty=$empty english_only=$enOnly khmer=$kh\n";
    }
}
