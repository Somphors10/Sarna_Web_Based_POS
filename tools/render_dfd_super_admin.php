<?php
/**
 * WBPOS DFD Level 2 — 2.0 Super Admin (correct: 2.3 ↔ D1)
 * Run: php tools/render_dfd_super_admin.php
 */

$font = 'C:\\Windows\\Fonts\\arial.ttf';
$fontB = 'C:\\Windows\\Fonts\\arialbd.ttf';
if (!is_file($fontB)) {
    $fontB = $font;
}

$W = 1280;
$H = 680;
$im = imagecreatetruecolor($W, $H);
imagealphablending($im, true);
imageantialias($im, true);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 20, 20, 20);
$ink = imagecolorallocate($im, 40, 40, 40);
imagefilledrectangle($im, 0, 0, $W, $H, $white);

function ttf($im, $size, $x, $y, $color, $font, $text, $center = false): void
{
    $box = imagettfbbox($size, 0, $font, $text);
    $w = $box[2] - $box[0];
    if ($center) {
        $x -= (int)($w / 2);
    }
    imagettftext($im, $size, 0, (int)$x, (int)$y, $color, $font, $text);
}

function thickline($im, $x1, $y1, $x2, $y2, $col): void
{
    imageline($im, $x1, $y1, $x2, $y2, $col);
    imageline($im, $x1, $y1 + 1, $x2, $y2 + 1, $col);
}

function arrowhead($im, $x1, $y1, $x2, $y2, $col): void
{
    $ang = atan2($y2 - $y1, $x2 - $x1);
    $s = 11;
    $p1x = (int)round($x2 - $s * cos($ang - 0.40));
    $p1y = (int)round($y2 - $s * sin($ang - 0.40));
    $p2x = (int)round($x2 - $s * cos($ang + 0.40));
    $p2y = (int)round($y2 - $s * sin($ang + 0.40));
    imagefilledpolygon($im, [$x2, $y2, $p1x, $p1y, $p2x, $p2y], $col);
}

function polyarrow($im, array $pts, $col): void
{
    $n = count($pts);
    for ($i = 0; $i < $n - 1; $i++) {
        thickline($im, $pts[$i][0], $pts[$i][1], $pts[$i + 1][0], $pts[$i + 1][1], $col);
    }
    $a = $pts[$n - 2];
    $b = $pts[$n - 1];
    arrowhead($im, $a[0], $a[1], $b[0], $b[1], $col);
}

function process_box($im, $x, $y, $w, $h, $num, $title, $black, $white, $font, $fontB): void
{
    $r = 14;
    imagefilledrectangle($im, $x + $r, $y, $x + $w - $r, $y + $h, $white);
    imagefilledrectangle($im, $x, $y + $r, $x + $w, $y + $h - $r, $white);
    imagefilledellipse($im, $x + $r, $y + $r, $r * 2, $r * 2, $white);
    imagefilledellipse($im, $x + $w - $r, $y + $r, $r * 2, $r * 2, $white);
    imagefilledellipse($im, $x + $r, $y + $h - $r, $r * 2, $r * 2, $white);
    imagefilledellipse($im, $x + $w - $r, $y + $h - $r, $r * 2, $r * 2, $white);
    imageline($im, $x + $r, $y, $x + $w - $r, $y, $black);
    imageline($im, $x + $r, $y + $h, $x + $w - $r, $y + $h, $black);
    imageline($im, $x, $y + $r, $x, $y + $h - $r, $black);
    imageline($im, $x + $w, $y + $r, $x + $w, $y + $h - $r, $black);
    imagearc($im, $x + $r, $y + $r, $r * 2, $r * 2, 180, 270, $black);
    imagearc($im, $x + $w - $r, $y + $r, $r * 2, $r * 2, 270, 360, $black);
    imagearc($im, $x + $r, $y + $h - $r, $r * 2, $r * 2, 90, 180, $black);
    imagearc($im, $x + $w - $r, $y + $h - $r, $r * 2, $r * 2, 0, 90, $black);
    imageline($im, $x, $y + 28, $x + $w, $y + 28, $black);
    ttf($im, 13, $x + $w / 2, $y + 20, $black, $fontB, $num, true);
    ttf($im, 13, $x + $w / 2, $y + 52, $black, $font, $title, true);
}

function store_box($im, $x, $y, $w, $h, $id, $name, $black, $font, $fontB): void
{
    $bar = 40;
    thickline($im, $x, $y, $x + $w, $y, $black);
    thickline($im, $x, $y + $h, $x + $w, $y + $h, $black);
    thickline($im, $x, $y, $x, $y + $h, $black);
    thickline($im, $x + $bar, $y, $x + $bar, $y + $h, $black);
    ttf($im, 13, $x + $bar / 2, $y + $h / 2 + 5, $black, $fontB, $id, true);
    ttf($im, 13, $x + $bar + ($w - $bar) / 2, $y + $h / 2 + 5, $black, $font, $name, true);
}

function entity_box($im, $x, $y, $w, $h, $label, $black, $fontB): void
{
    imagerectangle($im, $x, $y, $x + $w, $y + $h, $black);
    imagerectangle($im, $x + 1, $y + 1, $x + $w - 1, $y + $h - 1, $black);
    ttf($im, 12, $x + $w / 2, $y + $h / 2 + 5, $black, $fontB, $label, true);
}

ttf($im, 18, $W / 2, 34, $black, $fontB, 'Data Flow Diagram Level 2: Process 2.0 Super Admin', true);

$ex = 50;
$ew = 160;
$eh = 56;
$px = 380;
$pw = 270;
$ph = 70;
$p1y = 70;
$p2y = 270;
$p3y = 500;

entity_box($im, $ex, 270, $ew, $eh, 'Super Admin', $black, $fontB);
process_box($im, $px, $p1y, $pw, $ph, '2.1', 'View Applications', $black, $white, $font, $fontB);
process_box($im, $px, $p2y, $pw, $ph, '2.2', 'Process Approval / Rejection', $black, $white, $font, $fontB);
process_box($im, $px, $p3y, $pw, $ph, '2.3', 'Manage Tenant Status', $black, $white, $font, $fontB);

$d1x = 960;
$d1y = 78;
$d2x = 960;
$d2y = 278;
$sw = 270;
$sh = 54;
store_box($im, $d1x, $d1y, $sw, $sh, 'D1', 'Platform Database', $black, $font, $fontB);
store_box($im, $d2x, $d2y, $sw, $sh, 'D2', 'Tenant Shop Database', $black, $font, $fontB);

$er = $ex + $ew;
$pl = $px;
$pr = $px + $pw;
$d1l = $d1x;
$d2l = $d2x;
$d1mid = $d1y + (int)($sh / 2);
$d2mid = $d2y + (int)($sh / 2);
$rail = 780;
$status_rail = 880;

// 2.1
polyarrow($im, [[$pl, $p1y + 35], [250, $p1y + 35], [250, 282], [$er, 282]], $black);
ttf($im, 11, 200, $p1y + 26, $ink, $font, 'Request list', true);

polyarrow($im, [[$d1l, $d1mid], [$pr, $d1mid]], $black);
ttf($im, 11, 820, $d1y - 8, $ink, $font, 'Verified request', true);

// 2.2
polyarrow($im, [[$er, 298], [$pl, 298]], $black);
ttf($im, 11, ($er + $pl) / 2, 290, $ink, $font, 'Approve or reject', true);

polyarrow($im, [[$pr, $p2y + 22], [$rail, $p2y + 22], [$rail, $d1mid + 14], [$d1l, $d1mid + 14]], $black);
ttf($im, 11, 700, $p2y + 14, $ink, $font, 'Tenant + token / rejected', false);

polyarrow($im, [[$pr, $d2mid], [$d2l, $d2mid]], $black);
ttf($im, 11, 820, $d2y - 8, $ink, $font, 'Shop owner & grants', true);

// 2.3 left
polyarrow($im, [[125, 326], [125, $p3y + 35], [$pl, $p3y + 35]], $black);
ttf($im, 11, 175, 420, $ink, $font, 'Active / suspend / cancel', false);

polyarrow($im, [[$pl, $p3y + 52], [250, $p3y + 52], [250, 340], [125, 340]], $black);
ttf($im, 11, 175, $p3y + 64, $ink, $font, 'Live status view', false);

// 2.3 <-> D1 (NOT D2)
polyarrow($im, [[$pr, $p3y + 28], [$status_rail, $p3y + 28], [$status_rail, $d1y + $sh], [$d1x + 40, $d1y + $sh]], $black);
ttf($im, 11, 720, $p3y + 18, $ink, $font, 'Tenant status', false);

polyarrow($im, [[$d1x + 20, $d1y + $sh], [820, $d1y + $sh], [820, $p3y + 48], [$pr, $p3y + 48]], $black);
ttf($im, 11, 720, $p3y + 70, $ink, $font, 'Tenant status', false);

$png = __DIR__ . '/wbpos-dfd-2.0-super-admin-clean.png';
imagepng($im, $png, 9);
imagedestroy($im);
echo "Wrote $png\n";
