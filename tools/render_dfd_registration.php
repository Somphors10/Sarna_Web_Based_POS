<?php
/**
 * WBPOS DFD Level 2 — 1.0 Registration (Gane–Sarson, Visio style)
 * Run: php tools/render_dfd_registration.php
 */

$font = 'C:\\Windows\\Fonts\\arial.ttf';
$fontB = 'C:\\Windows\\Fonts\\arialbd.ttf';
if (!is_file($fontB)) {
    $fontB = $font;
}

$W = 1480;
$H = 860;
$im = imagecreatetruecolor($W, $H);
imagealphablending($im, true);
imageantialias($im, true);

$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 17, 17, 17);
$ink = imagecolorallocate($im, 34, 34, 34);
$muted = imagecolorallocate($im, 90, 90, 90);
$band = imagecolorallocate($im, 250, 250, 250);
imagefilledrectangle($im, 0, 0, $W, $H, $white);

function ttf($im, $size, $x, $y, $color, $font, $text, $center = false): void
{
    $box = imagettfbbox($size, 0, $font, $text);
    $w = $box[2] - $box[0];
    if ($center) {
        $x -= (int) ($w / 2);
    }
    imagettftext($im, $size, 0, (int) $x, (int) $y, $color, $font, $text);
}

function roundrect($im, $x1, $y1, $x2, $y2, $r, $col, $fill = null): void
{
    if ($fill !== null) {
        imagefilledrectangle($im, $x1 + $r, $y1, $x2 - $r, $y2, $fill);
        imagefilledrectangle($im, $x1, $y1 + $r, $x2, $y2 - $r, $fill);
        imagefilledellipse($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, $fill);
        imagefilledellipse($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, $fill);
        imagefilledellipse($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, $fill);
        imagefilledellipse($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, $fill);
    }
    imageline($im, $x1 + $r, $y1, $x2 - $r, $y1, $col);
    imageline($im, $x1 + $r, $y2, $x2 - $r, $y2, $col);
    imageline($im, $x1, $y1 + $r, $x1, $y2 - $r, $col);
    imageline($im, $x2, $y1 + $r, $x2, $y2 - $r, $col);
    imagearc($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $col);
    imagearc($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $col);
    imagearc($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $col);
    imagearc($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $col);
}

function thickline($im, $x1, $y1, $x2, $y2, $col): void
{
    imageline($im, $x1, $y1, $x2, $y2, $col);
    imageline($im, $x1, $y1 + 1, $x2, $y2 + 1, $col);
}

function arrow($im, $x1, $y1, $x2, $y2, $col): void
{
    thickline($im, $x1, $y1, $x2, $y2, $col);
    $ang = atan2($y2 - $y1, $x2 - $x1);
    $s = 12;
    $p1x = (int) round($x2 - $s * cos($ang - 0.38));
    $p1y = (int) round($y2 - $s * sin($ang - 0.38));
    $p2x = (int) round($x2 - $s * cos($ang + 0.38));
    $p2y = (int) round($y2 - $s * sin($ang + 0.38));
    imagefilledpolygon($im, [$x2, $y2, $p1x, $p1y, $p2x, $p2y], $col);
}

function process_box($im, $x, $y, $w, $h, $num, $lines, $black, $white, $band, $font, $fontB): void
{
    roundrect($im, $x, $y, $x + $w, $y + $h, 16, $black, $white);
    $by = $y + 34;
    imagefilledrectangle($im, $x + 2, $y + 2, $x + $w - 2, $by, $band);
    imageline($im, $x, $by, $x + $w, $by, $black);
    ttf($im, 15, $x + $w / 2, $y + 24, $black, $fontB, $num, true);
    $start = $y + 58;
    foreach ($lines as $i => $line) {
        ttf($im, 14, $x + $w / 2, $start + $i * 20, $black, $font, $line, true);
    }
}

function store_box($im, $x, $y, $w, $h, $id, $name, $black, $font, $fontB): void
{
    $bar = 44;
    thickline($im, $x, $y, $x + $w, $y, $black);
    thickline($im, $x, $y + $h, $x + $w, $y + $h, $black);
    thickline($im, $x, $y, $x, $y + $h, $black);
    thickline($im, $x + $bar, $y, $x + $bar, $y + $h, $black);
    ttf($im, 16, $x + $bar / 2, $y + $h / 2 + 6, $black, $fontB, $id, true);
    ttf($im, 15, $x + $bar + ($w - $bar) / 2, $y + $h / 2 + 6, $black, $font, $name, true);
}

function entity_box($im, $x, $y, $w, $h, $label, $black, $fontB): void
{
    imagerectangle($im, $x, $y, $x + $w, $y + $h, $black);
    imagerectangle($im, $x + 1, $y + 1, $x + $w - 1, $y + $h - 1, $black);
    ttf($im, 14, $x + $w / 2, $y + $h / 2 + 5, $black, $fontB, $label, true);
}

ttf($im, 20, $W / 2, 42, $black, $fontB, 'Data Flow Diagram Level 2: Process 1.0 Registration', true);

// Left entities
entity_box($im, 40, 70, 200, 80, 'New Shop Registrant', $black, $fontB);
entity_box($im, 40, 430, 200, 80, 'Super Admin', $black, $fontB);
entity_box($im, 40, 650, 200, 80, 'New Shop Registrant', $black, $fontB);

// Processes
process_box($im, 400, 66, 280, 88, '1.1', ['Submit Registration', 'Data'], $black, $white, $band, $font, $fontB);
process_box($im, 400, 250, 280, 88, '1.2', ['Verify Email'], $black, $white, $band, $font, $fontB);
process_box($im, 400, 426, 280, 88, '1.3', ['Process Subscription', 'Request'], $black, $white, $band, $font, $fontB);
process_box($im, 400, 646, 280, 88, '1.4', ['Pay with KHQR'], $black, $white, $band, $font, $fontB);

// D1 — one tall store so every arrow is a short horizontal
store_box($im, 1040, 66, 380, 668, 'D1', 'Platform Database', $black, $font, $fontB);

// ---- left flows ----
arrow($im, 240, 90, 400, 90, $black);
ttf($im, 12, 320, 80, $ink, $font, 'Registration data', true);

arrow($im, 400, 126, 240, 126, $black);
ttf($im, 12, 320, 148, $ink, $font, 'Verify email link', true);

arrow($im, 80, 150, 80, 294, $black);
arrow($im, 80, 294, 400, 294, $black);
ttf($im, 12, 240, 284, $ink, $font, 'Click verify link', true);

arrow($im, 240, 470, 400, 470, $black);
ttf($im, 12, 320, 460, $ink, $font, 'Approve', true);

arrow($im, 400, 492, 270, 492, $black);
arrow($im, 270, 492, 270, 690, $black);
arrow($im, 270, 690, 240, 690, $black);
ttf($im, 12, 148, 580, $ink, $font, 'KHQR / payment link', false);

arrow($im, 240, 710, 400, 710, $black);
ttf($im, 12, 320, 700, $ink, $font, 'Payment reference', true);

// ---- right flows (short horizontals into D1) ----
$pr = 680;
$dl = 1040;
$mid = ($pr + $dl) / 2;

arrow($im, $pr, 90, $dl, 90, $black);
ttf($im, 12, $mid, 80, $ink, $font, 'Pending request', true);

arrow($im, $pr, 294, $dl, 294, $black);
ttf($im, 12, $mid, 284, $ink, $font, 'Email verified', true);

arrow($im, $dl, 450, $pr, 450, $black);
ttf($im, 12, $mid, 440, $ink, $font, 'Verified request', true);

arrow($im, $pr, 490, $dl, 490, $black);
ttf($im, 12, $mid, 512, $ink, $font, 'Tenant + payment token', true);

arrow($im, $dl, 670, $pr, 670, $black);
ttf($im, 12, $mid, 660, $ink, $font, 'Approved request', true);

arrow($im, $pr, 710, $dl, 710, $black);
ttf($im, 12, $mid, 732, $ink, $font, 'Payment + shop active', true);

$png = __DIR__ . '/wbpos-dfd-1.0-registration.png';
imagepng($im, $png, 9);
imagedestroy($im);
echo "Wrote $png\n";
