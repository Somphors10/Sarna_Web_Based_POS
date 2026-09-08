<?php

$source = $argv[1] ?? '';
$output = $argv[2] ?? '';

if ($source === '' || $output === '' || !is_file($source)) {
    fwrite(STDERR, "Usage: php crop_khqr.php <source.png> <output.png>\n");
    exit(1);
}

$bytes = file_get_contents($source);
if ($bytes === false) {
    fwrite(STDERR, "Could not read source image.\n");
    exit(1);
}

$image = @imagecreatefromstring($bytes);
if ($image === false) {
    fwrite(STDERR, "Could not decode source image.\n");
    exit(1);
}

$width = imagesx($image);
$height = imagesy($image);
$box = detect_qr_square($image);
$left = $box['x'];
$top = $box['y'];
$size = $box['size'];

$cropped = imagecrop($image, [
    'x' => $left,
    'y' => $top,
    'width' => $size,
    'height' => $size,
]);

if ($cropped === false) {
    fwrite(STDERR, "Crop failed.\n");
    exit(1);
}

$inner = min(imagesx($cropped), imagesy($cropped));
$padding = max(24, (int) round($inner * 0.1));
$outputSize = min(512, max(280, $inner + 2 * $padding));

$square = imagecreatetruecolor($outputSize, $outputSize);
$white = imagecolorallocate($square, 255, 255, 255);
imagefilledrectangle($square, 0, 0, $outputSize, $outputSize, $white);

$drawSize = min($inner, $outputSize - 2 * $padding);
$destX = (int) round(($outputSize - $drawSize) / 2);
$destY = (int) round(($outputSize - $drawSize) / 2);

imagecopyresampled(
    $square,
    $cropped,
    $destX,
    $destY,
    0,
    0,
    $drawSize,
    $drawSize,
    imagesx($cropped),
    imagesy($cropped)
);

imagepng($square, $output, 9);

fwrite(STDOUT, "Saved {$output} ({$outputSize}x{$outputSize}) from {$width}x{$height} crop {$left},{$top},{$size}\n");

imagedestroy($image);
imagedestroy($cropped);
imagedestroy($square);

/**
 * Find the QR code square only (below name / KHQR header, above account rows).
 *
 * @return array{x:int,y:int,size:int}
 */
function detect_qr_square(GdImage $image): array
{
    $width = imagesx($image);
    $height = imagesy($image);
    $bestScore = 0.0;
    $best = null;

    $minSize = (int) max(72, round(min($width, $height) * 0.30));
    $maxSize = (int) round(min($width, $height) * 0.46);
    $centerX = (int) round($width / 2);
    $yStart = (int) round($height * 0.40);
    $yEndMax = (int) round($height * 0.66);

    for ($size = $maxSize; $size >= $minSize; $size -= 2) {
        $yEnd = min($yEndMax, $height - $size);
        $xStart = max(0, $centerX - (int) round($size / 2) - 16);
        $xEnd = min($width - $size, $centerX - (int) round($size / 2) + 16);

        for ($y = $yStart; $y <= $yEnd; $y += 2) {
            for ($x = $xStart; $x <= $xEnd; $x += 2) {
                $stats = sample_square($image, $x, $y, $size);
                $dark = $stats['dark_ratio'];
                if ($dark < 0.32 || $dark > 0.56) {
                    continue;
                }
                if ($stats['variance'] < 2200) {
                    continue;
                }

                $top = sample_band($image, $x, $y, $size, 0.0, 0.22);
                $bottom = sample_band($image, $x, $y, $size, 0.78, 1.0);

                // Skip name / red header (pale top) or account grey rows (pale bottom).
                if ($top['dark_ratio'] < 0.14 || $top['avg_lum'] > 215) {
                    continue;
                }
                if ($bottom['avg_lum'] > 195 && $bottom['dark_ratio'] < 0.12) {
                    continue;
                }

                $centered = 1.0 - abs($x + $size / 2 - $width / 2) / ($width / 2);
                $score = $stats['variance'] * $dark * $size * (0.75 + 0.25 * $centered);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = ['x' => $x, 'y' => $y, 'size' => $size];
                }
            }
        }

        if ($best !== null) {
            break;
        }
    }

    if ($best !== null) {
        return $best;
    }

    $size = (int) round($width * 0.38);
    $size = min($size, $width, $height - (int) round($height * 0.52));
    return [
        'x' => (int) round(($width - $size) / 2),
        'y' => (int) round($height * 0.52),
        'size' => $size,
    ];
}

/**
 * @return array{dark_ratio:float,variance:float,avg_lum:float}
 */
function sample_band(GdImage $image, int $x, int $y, int $size, float $startPct, float $endPct): array
{
    $bandY = $y + (int) round($size * $startPct);
    $bandH = max(1, (int) round($size * ($endPct - $startPct)));
    $step = max(2, (int) round($size / 20));
    $dark = 0;
    $samples = 0;
    $sum = 0.0;

    for ($py = $bandY; $py < $bandY + $bandH && $py < $y + $size; $py += $step) {
        for ($px = $x; $px < $x + $size; $px += $step) {
            $rgb = imagecolorat($image, $px, $py);
            $lum = (($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF);
            $lum /= 3.0;
            if ($lum < 140) {
                $dark++;
            }
            $sum += $lum;
            $samples++;
        }
    }

    if ($samples === 0) {
        return ['dark_ratio' => 0.0, 'variance' => 0.0, 'avg_lum' => 255.0];
    }

    return [
        'dark_ratio' => $dark / $samples,
        'variance' => 0.0,
        'avg_lum' => $sum / $samples,
    ];
}

/**
 * @return array{dark_ratio:float,variance:float}
 */
function sample_square(GdImage $image, int $x, int $y, int $size): array
{
    $step = max(2, (int) round($size / 36));
    $dark = 0;
    $samples = 0;
    $sum = 0.0;
    $sumSq = 0.0;

    for ($py = $y; $py < $y + $size; $py += $step) {
        for ($px = $x; $px < $x + $size; $px += $step) {
            $rgb = imagecolorat($image, $px, $py);
            $lum = (($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF);
            $lum /= 3.0;
            if ($lum < 140) {
                $dark++;
            }
            $sum += $lum;
            $sumSq += $lum * $lum;
            $samples++;
        }
    }

    if ($samples === 0) {
        return ['dark_ratio' => 0.0, 'variance' => 0.0];
    }

    $mean = $sum / $samples;
    $variance = max(0.0, ($sumSq / $samples) - ($mean * $mean));

    return [
        'dark_ratio' => $dark / $samples,
        'variance' => $variance,
    ];
}
