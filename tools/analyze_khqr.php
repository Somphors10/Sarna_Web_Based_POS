<?php

$path = $argv[1] ?? 'public/images/payment/aba-khqr.png';
$root = dirname(__DIR__);
$full = $root . '/' . ltrim(str_replace('\\', '/', $path), '/');
$img = imagecreatefrompng($full);
$w = imagesx($img);
$h = imagesy($img);
echo "size={$w}x{$h}\n";

// Scan for QR-like dense black region (square).
$best = ['score' => 0, 'x' => 0, 'y' => 0, 'size' => 0];
$sizes = [];
foreach ([0.28, 0.32, 0.36, 0.40, 0.44] as $ratio) {
    $sizes[] = (int) round($w * $ratio);
}

for ($size = min($w, $h); $size >= (int) round($w * 0.24); $size -= 2) {
    for ($y = 0; $y <= $h - $size; $y += 2) {
        for ($x = 0; $x <= $w - $size; $x += 2) {
            $dark = 0;
            $samples = 0;
            $step = max(2, (int) round($size / 40));
            for ($py = $y; $py < $y + $size; $py += $step) {
                for ($px = $x; $px < $x + $size; $px += $step) {
                    $rgb = imagecolorat($img, $px, $py);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $lum = ($r + $g + $b) / 3;
                    if ($lum < 128) {
                        $dark++;
                    }
                    $samples++;
                }
            }
            $ratio = $samples > 0 ? $dark / $samples : 0;
            if ($ratio < 0.18 || $ratio > 0.62) {
                continue;
            }
            $score = $ratio * $size;
            if ($score > $best['score']) {
                $best = ['score' => $score, 'x' => $x, 'y' => $y, 'size' => $size, 'ratio' => $ratio];
            }
        }
    }
    if ($best['size'] > 0) {
        break;
    }
}

echo 'best=' . json_encode($best) . PHP_EOL;
