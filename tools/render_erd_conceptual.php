<?php
/**
 * Mermaid-style conceptual ERD — exact WBPOS names and flow.
 * Run: php tools/render_erd_conceptual.php
 */

$font  = 'C:\\Windows\\Fonts\\arial.ttf';
$fontB = 'C:\\Windows\\Fonts\\arialbd.ttf';
if (!is_file($fontB)) {
    $fontB = $font;
}

$W = 1780;
$H = 820;
$im = imagecreatetruecolor($W, $H);
imagealphablending($im, true);
imageantialias($im, true);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$ink   = imagecolorallocate($im, 20, 20, 20);
imagefilledrectangle($im, 0, 0, $W, $H, $white);

function ttf($im, $size, $x, $y, $color, $font, $text, $center = false): void
{
    $box = imagettfbbox($size, 0, $font, $text);
    $tw = $box[2] - $box[0];
    if ($center) {
        $x -= (int) ($tw / 2);
    }
    imagettftext($im, $size, 0, (int) $x, (int) $y, $color, $font, $text);
}

function capsule($im, $cx, $cy, $label, $black, $white, $fontB, $w = 248, $h = 34): array
{
    $x = (int) ($cx - $w / 2);
    $y = (int) ($cy - $h / 2);
    $r = (int) ($h / 2);
    imagefilledellipse($im, $x + $r, $cy, $h, $h, $white);
    imagefilledellipse($im, $x + $w - $r, $cy, $h, $h, $white);
    imagefilledrectangle($im, $x + $r, $y, $x + $w - $r, $y + $h, $white);
    imagearc($im, $x + $r, $cy, $h, $h, 90, 270, $black);
    imagearc($im, $x + $w - $r, $cy, $h, $h, 270, 90, $black);
    imageline($im, $x + $r, $y, $x + $w - $r, $y, $black);
    imageline($im, $x + $r, $y + $h, $x + $w - $r, $y + $h, $black);
    ttf($im, 10, $cx, $cy + 4, $black, $fontB, $label, true);
    return ['cx' => $cx, 'cy' => $cy, 'l' => $x, 'r' => $x + $w, 't' => $y, 'b' => $y + $h];
}

function ln($im, $x1, $y1, $x2, $y2, $col): void
{
    imageline($im, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $col);
}

function crow_v($im, $x, $y, $up, $col): void
{
    $d = $up ? -1 : 1;
    ln($im, $x, $y, $x - 6, $y + $d * 10, $col);
    ln($im, $x, $y, $x + 6, $y + $d * 10, $col);
}

function bar_v($im, $x, $y, $col): void
{
    ln($im, $x - 7, $y, $x + 7, $y, $col);
}

function crow_h($im, $x, $y, $left, $col): void
{
    $d = $left ? -1 : 1;
    ln($im, $x, $y, $x + $d * 10, $y - 6, $col);
    ln($im, $x, $y, $x + $d * 10, $y + 6, $col);
}

function bar_h($im, $x, $y, $col): void
{
    ln($im, $x, $y - 7, $x, $y + 7, $col);
}

function v_one_many($im, $top, $bot, $col, $label, $font, $ink, $off = 8): void
{
    $x = $top['cx'];
    ln($im, $x, $top['b'], $x, $bot['t'], $col);
    bar_v($im, $x, $top['b'] + 2, $col);
    crow_v($im, $x, $bot['t'], true, $col);
    ttf($im, 9, $x + $off, (int) (($top['b'] + $bot['t']) / 2) + 3, $ink, $font, $label);
}

function v_one_one($im, $top, $bot, $col, $label, $font, $ink, $off = 8): void
{
    $x = $top['cx'];
    ln($im, $x, $top['b'], $x, $bot['t'], $col);
    bar_v($im, $x, $top['b'] + 2, $col);
    bar_v($im, $x, $bot['t'] - 2, $col);
    ttf($im, 9, $x + $off, (int) (($top['b'] + $bot['t']) / 2) + 3, $ink, $font, $label);
}

function h_one_many($im, $left, $right, $col, $label, $font, $ink, $yoff = -10): void
{
    $y = $left['cy'];
    ln($im, $left['r'], $y, $right['l'], $y, $col);
    bar_h($im, $left['r'] + 2, $y, $col);
    crow_h($im, $right['l'], $y, true, $col);
    ttf($im, 9, (int) (($left['r'] + $right['l']) / 2), $y + $yoff, $ink, $font, $label, true);
}

ttf($im, 20, $W / 2, 32, $black, $fontB, 'ERD WBPOS', true);

// Row 1
$ten  = capsule($im, 150, 70, 'WBPOS_TENANTS', $black, $white, $fontB);
$plan = capsule($im, 720, 70, 'WBPOS_PLANS', $black, $white, $fontB);
$req  = capsule($im, 1280, 70, 'WBPOS_SUBSCRIPTION_REQUESTS', $black, $white, $fontB, 280);
$adm  = capsule($im, 1620, 70, 'WBPOS_PLATFORM_ADMINS', $black, $white, $fontB, 250);

// Row 2
$sub  = capsule($im, 150, 190, 'WBPOS_SUBSCRIPTIONS', $black, $white, $fontB);
$peop = capsule($im, 720, 190, 'WBPOS_PEOPLE', $black, $white, $fontB);

// Row 3
$cust = capsule($im, 150, 330, 'WBPOS_CUSTOMERS', $black, $white, $fontB);
$emp  = capsule($im, 520, 330, 'WBPOS_EMPLOYEES', $black, $white, $fontB);
$supp = capsule($im, 900, 330, 'WBPOS_SUPPLIERS', $black, $white, $fontB);
$role = capsule($im, 1280, 330, 'WBPOS_RBAC_ROLES', $black, $white, $fontB);
$perm = capsule($im, 1600, 330, 'WBPOS_RBAC_PERMISSIONS', $black, $white, $fontB, 260);

// Row 4
$sale = capsule($im, 150, 470, 'WBPOS_SALES', $black, $white, $fontB);
$item = capsule($im, 720, 470, 'WBPOS_ITEMS', $black, $white, $fontB);
$loc  = capsule($im, 1100, 470, 'WBPOS_STOCK_LOCATIONS', $black, $white, $fontB, 250);
$recv = capsule($im, 1500, 470, 'WBPOS_RECEIVINGS', $black, $white, $fontB);

// Row 5
$sitm = capsule($im, 150, 610, 'WBPOS_SALES_ITEMS', $black, $white, $fontB);
$spay = capsule($im, 430, 610, 'WBPOS_SALES_PAYMENTS', $black, $white, $fontB);
$qty  = capsule($im, 800, 610, 'WBPOS_ITEM_QUANTITIES', $black, $white, $fontB);
$ritm = capsule($im, 1280, 610, 'WBPOS_RECEIVINGS_ITEMS', $black, $white, $fontB, 260);

// Row 6
$ecat = capsule($im, 150, 750, 'WBPOS_EXPENSE_CATEGORIES', $black, $white, $fontB, 260);
$exp  = capsule($im, 520, 750, 'WBPOS_EXPENSES', $black, $white, $fontB);

// Platform
v_one_many($im, $ten, $sub, $black, 'has', $font, $ink);
h_one_many($im, $plan, $req, $black, 'chosen_by', $font, $ink, -12);
h_one_many($im, $req, $adm, $black, 'reviews', $font, $ink, -12);

$rail = 132;
ln($im, $plan['cx'], $plan['b'], $plan['cx'], $rail, $black);
bar_v($im, $plan['cx'], $plan['b'] + 2, $black);
ln($im, $sub['r'] + 12, $rail, $plan['cx'], $rail, $black);
ln($im, $sub['r'] + 12, $rail, $sub['r'] + 12, $sub['cy'], $black);
ln($im, $sub['r'] + 12, $sub['cy'], $sub['r'], $sub['cy'], $black);
crow_h($im, $sub['r'], $sub['cy'], true, $black);
ttf($im, 9, 430, $rail - 6, $ink, $font, 'has');

// People 1:1
v_one_one($im, $peop, $emp, $black, 'employee', $font, $ink, 8);

$yP = 260;
ln($im, $peop['l'], $peop['cy'], $cust['cx'], $peop['cy'], $black);
bar_h($im, $peop['l'] - 1, $peop['cy'], $black);
ln($im, $cust['cx'], $peop['cy'], $cust['cx'], $cust['t'], $black);
bar_v($im, $cust['cx'], $cust['t'] - 2, $black);
ttf($im, 9, 360, $peop['cy'] - 10, $ink, $font, 'customer');

ln($im, $peop['r'], $peop['cy'], $supp['cx'], $peop['cy'], $black);
bar_h($im, $peop['r'] + 1, $peop['cy'], $black);
ln($im, $supp['cx'], $peop['cy'], $supp['cx'], $supp['t'], $black);
bar_v($im, $supp['cx'], $supp['t'] - 2, $black);
ttf($im, 9, 980, $peop['cy'] - 10, $ink, $font, 'supplier');

$yA = 290;
ln($im, $emp['cx'] + 70, $emp['t'], $emp['cx'] + 70, $yA, $black);
crow_v($im, $emp['cx'] + 70, $emp['t'], false, $black);
ln($im, $emp['cx'] + 70, $yA, $role['cx'], $yA, $black);
ln($im, $role['cx'], $yA, $role['cx'], $role['t'], $black);
bar_v($im, $role['cx'], $role['t'] - 2, $black);
ttf($im, 9, 980, $yA - 6, $ink, $font, 'assigned');

h_one_many($im, $role, $perm, $black, 'has', $font, $ink, -12);

// Sales
v_one_many($im, $cust, $sale, $black, 'buys', $font, $ink);

$yPr = 400;
ln($im, $emp['cx'], $emp['b'], $emp['cx'], $yPr, $black);
bar_v($im, $emp['cx'], $emp['b'] + 2, $black);
ln($im, $sale['r'] + 14, $yPr, $emp['cx'], $yPr, $black);
ln($im, $sale['r'] + 14, $yPr, $sale['r'] + 14, $sale['cy'], $black);
ln($im, $sale['r'] + 14, $sale['cy'], $sale['r'], $sale['cy'], $black);
crow_h($im, $sale['r'], $sale['cy'], true, $black);
ttf($im, 9, 300, $yPr - 6, $ink, $font, 'processes');

v_one_many($im, $sale, $sitm, $black, 'contains', $font, $ink);

$yPay = 542;
ln($im, $sale['cx'] + 46, $sale['b'], $sale['cx'] + 46, $yPay, $black);
bar_v($im, $sale['cx'] + 46, $sale['b'] + 2, $black);
ln($im, $sale['cx'] + 46, $yPay, $spay['cx'], $yPay, $black);
ln($im, $spay['cx'], $yPay, $spay['cx'], $spay['t'], $black);
crow_v($im, $spay['cx'], $spay['t'], true, $black);
ttf($im, 9, 280, $yPay - 6, $ink, $font, 'paid_by');

$ySold = 542;
ln($im, $item['l'], $item['cy'], 400, $item['cy'], $black);
bar_h($im, $item['l'] - 1, $item['cy'], $black);
ln($im, 400, $item['cy'], 400, $sitm['cy'], $black);
ln($im, 400, $sitm['cy'], $sitm['r'], $sitm['cy'], $black);
crow_h($im, $sitm['r'], $sitm['cy'], true, $black);
ttf($im, 9, 450, 458, $ink, $font, 'sold');

// Stock / receiving
$ySup = 400;
ln($im, $supp['cx'] - 30, $supp['b'], $supp['cx'] - 30, $ySup, $black);
bar_v($im, $supp['cx'] - 30, $supp['b'] + 2, $black);
ln($im, $supp['cx'] - 30, $ySup, $item['cx'], $ySup, $black);
ln($im, $item['cx'], $ySup, $item['cx'], $item['t'], $black);
crow_v($im, $item['cx'], $item['t'], true, $black);
ttf($im, 9, 780, $ySup - 6, $ink, $font, 'supplies');

v_one_many($im, $item, $qty, $black, 'stocked', $font, $ink);

$ySt = 542;
ln($im, $loc['cx'], $loc['b'], $loc['cx'], $ySt, $black);
bar_v($im, $loc['cx'], $loc['b'] + 2, $black);
ln($im, $qty['r'] + 16, $ySt, $loc['cx'], $ySt, $black);
ln($im, $qty['r'] + 16, $ySt, $qty['r'] + 16, $qty['cy'], $black);
ln($im, $qty['r'] + 16, $qty['cy'], $qty['r'], $qty['cy'], $black);
crow_h($im, $qty['r'], $qty['cy'], true, $black);
ttf($im, 9, 980, $ySt - 6, $ink, $font, 'stores');

$yPv = 400;
ln($im, $supp['cx'] + 40, $supp['b'], $supp['cx'] + 40, $yPv, $black);
bar_v($im, $supp['cx'] + 40, $supp['b'] + 2, $black);
ln($im, $supp['cx'] + 40, $yPv, $recv['cx'], $yPv, $black);
ln($im, $recv['cx'], $yPv, $recv['cx'], $recv['t'], $black);
crow_v($im, $recv['cx'], $recv['t'], true, $black);
ttf($im, 9, 1180, $yPv - 6, $ink, $font, 'provides');

v_one_many($im, $recv, $ritm, $black, 'contains', $font, $ink, -72);

$yRv = 542;
ln($im, $item['r'], $item['cy'] + 8, $ritm['cx'] - 30, $item['cy'] + 8, $black);
bar_h($im, $item['r'] + 2, $item['cy'] + 8, $black);
ln($im, $ritm['cx'] - 30, $item['cy'] + 8, $ritm['cx'] - 30, $ritm['t'], $black);
crow_v($im, $ritm['cx'] - 30, $ritm['t'], true, $black);
ttf($im, 9, 980, 500, $ink, $font, 'received');

// Expenses
h_one_many($im, $ecat, $exp, $black, 'classifies', $font, $ink, -12);

$xR = 520;
ln($im, $emp['cx'] + 24, $emp['b'], $emp['cx'] + 24, 700, $black);
bar_v($im, $emp['cx'] + 24, $emp['b'] + 2, $black);
ln($im, $emp['cx'] + 24, 700, $xR, 700, $black);
ln($im, $xR, 700, $xR, $exp['t'], $black);
crow_v($im, $xR, $exp['t'], true, $black);
ttf($im, 9, 400, 694, $ink, $font, 'records');

$png = __DIR__ . '/wbpos-erd-conceptual-ok.png';
imagepng($im, $png, 9);
imagedestroy($im);
echo "Wrote $png\n";
