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

// ACLEDA KHQR card layout: QR is centered in the lower half of the white card.
$size = (int) round($width * 0.44);
$left = (int) round(($width - $size) / 2);
$top = (int) round($height * 0.528);

$cropped = imagecrop($image, [
    'x' => max(0, $left),
    'y' => max(0, $top),
    'width' => min($size, $width - max(0, $left)),
    'height' => min($size, $height - max(0, $top)),
]);

if ($cropped === false) {
    fwrite(STDERR, "Crop failed.\n");
    exit(1);
}

$size = max(imagesx($cropped), imagesy($cropped));
$target = 512;
$square = imagecreatetruecolor($target, $target);
$white = imagecolorallocate($square, 255, 255, 255);
imagefilledrectangle($square, 0, 0, $target, $target, $white);
imagecopyresampled(
    $square,
    $cropped,
    0,
    0,
    0,
    0,
    $target,
    $target,
    imagesx($cropped),
    imagesy($cropped)
);

imagesavealpha($square, true);
imagepng($square, $output, 9);

fwrite(STDOUT, "Saved {$output} ({$target}x{$target}) from {$width}x{$height}\n");

imagedestroy($image);
imagedestroy($cropped);
imagedestroy($square);
