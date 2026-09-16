<?php
$enDir = __DIR__ . '/../app/Language/en';
$kmDir = __DIR__ . '/../app/Language/km';
$enFiles = glob($enDir . '/*.php');
$report = [];
$total = 0;

foreach ($enFiles as $enFile) {
    $name = basename($enFile);
    $kmFile = $kmDir . '/' . $name;
    $en = include $enFile;
    if (!is_array($en)) {
        continue;
    }
    $km = file_exists($kmFile) ? include $kmFile : [];
    if (!is_array($km)) {
        $km = [];
    }

    foreach ($en as $k => $v) {
        if (!is_string($v)) {
            continue;
        }
        $v = trim($v);
        if ($v === '') {
            continue;
        }
        $kv = array_key_exists($k, $km) ? trim((string)$km[$k]) : null;
        $missing = $kv === null || $kv === '';
        $same = !$missing && $kv === $v;
        $looksEnglish = $same && preg_match('/[A-Za-z]{3,}/', $v);
        // Keep intentional English tech tokens out of noise later if needed
        if ($missing || $looksEnglish) {
            $report[$name][] = [
                'key' => $k,
                'en' => $v,
                'km' => $kv,
                'type' => $missing ? 'missing' : 'same',
            ];
            $total++;
        }
    }
}

foreach ($report as $f => $items) {
    echo "=== $f (" . count($items) . ") ===\n";
    foreach ($items as $i) {
        $en = str_replace(["\n", "\r"], ' ', $i['en']);
        echo $i['type'] . "\t" . $i['key'] . "\t" . $en . "\n";
    }
}
echo "TOTAL=$total\n";
