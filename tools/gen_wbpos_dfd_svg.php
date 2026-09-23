<?php
/**
 * WBPOS DFD Level 2 — Gane–Sarson SVGs in the assignment picture style,
 * with data flows corrected from live WBPOS code.
 *
 * Run: php tools/gen_wbpos_dfd_svg.php
 */

$outDir = __DIR__;

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function defs(): string
{
    return <<<'XML'
  <defs>
    <marker id="arrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto">
      <path d="M 0 1.5 L 9 5 L 0 8.5 z" fill="#111"/>
    </marker>
  </defs>
XML;
}

function entity(float $x, float $y, float $w, float $h, string $label): string
{
    return sprintf(
        '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="#fff" stroke="#111" stroke-width="2"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" fill="#111">%s</text>',
        $x, $y, $w, $h,
        $x + $w / 2, $y + $h / 2 + 5,
        e($label)
    );
}

function process(float $x, float $y, float $w, float $h, string $num, string $name): string
{
    $band = 26;
    $out = sprintf(
        '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="14" ry="14" fill="#fff" stroke="#111" stroke-width="1.8"/>' .
        '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#111" stroke-width="1.5"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" fill="#111">%s</text>',
        $x, $y, $w, $h,
        $x, $y + $band, $x + $w, $y + $band,
        $x + $w / 2, $y + 18,
        e($num)
    );
    $lines = explode("\n", $name);
    $lineH = 15;
    $bodyMid = $y + $band + ($h - $band) / 2;
    $start = $bodyMid - ((count($lines) - 1) * $lineH) / 2 + 5;
    foreach ($lines as $i => $line) {
        $out .= sprintf(
            '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" fill="#111">%s</text>',
            $x + $w / 2,
            $start + $i * $lineH,
            e($line)
        );
    }

    return $out;
}

function store(float $x, float $y, float $w, float $h, string $id, string $name): string
{
    $left = 36;
    return sprintf(
        '<path d="M %.1f %.1f H %.1f V %.1f H %.1f" fill="none" stroke="#111" stroke-width="1.8"/>' .
        '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#111" stroke-width="1.8"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" fill="#111">%s</text>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" fill="#111">%s</text>',
        $x, $y, $x + $w, $y + $h, $x,
        $x + $left, $y, $x + $left, $y + $h,
        $x + $left / 2, $y + $h / 2 + 5, e($id),
        $x + $left + ($w - $left) / 2, $y + $h / 2 + 5, e($name)
    );
}

function flow(array $pts, string $label, float $lx = 0, float $ly = -10): string
{
    $d = '';
    foreach ($pts as $i => $p) {
        $d .= ($i === 0 ? 'M' : 'L') . sprintf(' %.1f %.1f', $p[0], $p[1]);
    }
    $n = count($pts);
    $a = $pts[(int) floor(($n - 1) / 2)];
    $b = $pts[(int) ceil(($n - 1) / 2)];
    if ($a === $b && $n > 2) {
        $a = $pts[1];
        $b = $pts[min(2, $n - 1)];
    }
    $mx = ($a[0] + $b[0]) / 2 + $lx;
    $my = ($a[1] + $b[1]) / 2 + $ly;
    $labelXml = $label === '' ? '' : sprintf(
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="12" fill="#222">%s</text>',
        $mx, $my, e($label)
    );

    return sprintf(
        '<path d="%s" fill="none" stroke="#111" stroke-width="1.5" marker-end="url(#arrow)"/>%s',
        $d,
        $labelXml
    );
}

function wrap_svg(int $w, int $h, array $parts): string
{
    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">%s%s</svg>',
        $w, $h, $w, $h,
        '<rect width="100%" height="100%" fill="#fff"/>',
        implode("\n", $parts)
    );
}

// =============================================================================
// 1.0 New Shop Registrant
// =============================================================================
$parts = [defs()];
$parts[] = entity(36, 36, 176, 210, 'New Shop Registrant');
$parts[] = entity(36, 288, 176, 84, 'Super Admin');
$parts[] = entity(36, 414, 176, 84, 'New Shop Registrant');
$parts[] = process(268, 36, 230, 84, '1.1', "Submit Registration\nData");
$parts[] = process(268, 162, 230, 84, '1.2', 'Verify Email');
$parts[] = process(268, 288, 230, 84, '1.3', "Process Subscription\nRequest");
$parts[] = process(268, 414, 230, 84, '1.4', 'Pay with KHQR');
$parts[] = store(640, 150, 270, 230, 'D1', 'Platform Database');

$parts[] = flow([[212, 58], [268, 58]], 'Registration data', 0, -12);
$parts[] = flow([[268, 92], [212, 92]], 'Verify email link', 0, 16);
$parts[] = flow([[212, 204], [268, 204]], 'Click verify link', 0, -12);
$parts[] = flow([[212, 330], [268, 330]], 'Approve', 0, -12);
$parts[] = flow([[268, 350], [232, 350], [232, 456], [212, 456]], 'KHQR / payment link', -90, 0);
$parts[] = flow([[212, 476], [268, 476]], 'Payment reference', 0, 16);

$parts[] = flow([[498, 58], [640, 58], [640, 150]], 'Pending request', 50, -12);
$parts[] = flow([[498, 204], [640, 204]], 'Email verified', 0, -12);
$parts[] = flow([[640, 318], [498, 318]], 'Verified request', 0, -12);
$parts[] = flow([[498, 354], [640, 354]], 'Tenant + payment token', 0, 16);
$parts[] = flow([[640, 438], [498, 438]], 'Approved request', 0, -12);
$parts[] = flow([[498, 476], [640, 476], [640, 380]], 'Payment + shop active', 50, 16);

file_put_contents($outDir . '/wbpos-dfd-1.0-registration.svg', wrap_svg(940, 540, $parts));

// =============================================================================
// 2.0 Super Admin
// =============================================================================
$parts = [defs()];
$parts[] = entity(40, 24, 150, 64, 'Super Admin');
$parts[] = process(70, 140, 230, 88, '2.1', "Review Registration\nRequest");
$parts[] = process(400, 140, 230, 88, '2.2', "Approve / Reject\nRequest");
$parts[] = process(400, 310, 230, 88, '2.3', "Manage Shop\nStatus");
$parts[] = store(70, 430, 280, 52, 'D1', 'Platform Database');
$parts[] = store(720, 154, 250, 52, 'D2', 'Tenant Shop Database');

$parts[] = flow([[115, 88], [115, 140]], 'Review', 32, 0);
$parts[] = flow([[190, 140], [190, 88]], 'Request list', 48, 0);
$parts[] = flow([[160, 430], [160, 228]], 'Verified request', 70, 0);
$parts[] = flow([[190, 56], [515, 56], [515, 140]], 'Approve or reject', 80, -12);
$parts[] = flow([[300, 184], [400, 184]], 'Selected request', 0, -12);
$parts[] = flow([[515, 228], [515, 268], [340, 268], [340, 430]], 'Tenant + token / rejected', -8, -12);
$parts[] = flow([[630, 166], [720, 166]], 'Shop owner & grants', 0, -12);
$parts[] = flow([[40, 56], [18, 56], [18, 354], [400, 354]], 'Active / suspend / cancel', 100, -12);
$parts[] = flow([[350, 456], [415, 456], [415, 398]], 'Tenant status', 0, 16);
$parts[] = flow([[470, 398], [470, 482], [350, 482]], 'Updated status', 0, 16);
$parts[] = flow([[400, 332], [30, 332], [30, 88], [40, 88]], 'Live status view', 80, -12);

file_put_contents($outDir . '/wbpos-dfd-2.0-super-admin.svg', wrap_svg(1000, 520, $parts));

// =============================================================================
// 3.0 Shop Owner — same layout as the picture, labels corrected
// =============================================================================
$parts = [defs()];
$parts[] = entity(40, 210, 140, 90, 'Shop Owner');
$parts[] = process(330, 36, 210, 86, '3.1', "Manage Items\n& Staff");
$parts[] = process(330, 200, 210, 86, '3.2', "Configure Store\nSettings");
$parts[] = process(400, 380, 210, 86, '3.3', "Generate Shop\nReports");
$parts[] = store(700, 208, 250, 56, 'D2', 'Tenant Shop Database');

$parts[] = flow([[110, 210], [110, 79], [330, 79]], 'Items, staff', 80, -12);
$parts[] = flow([[180, 255], [330, 255]], 'Store settings', 0, -12);
$parts[] = flow([[540, 58], [830, 58], [830, 208]], 'Items & staff records', 20, -12);
$parts[] = flow([[700, 220], [620, 220], [620, 100], [540, 100]], 'Shop records', 0, -12);
$parts[] = flow([[540, 228], [700, 228]], 'Shop config', 0, -12);
$parts[] = flow([[700, 250], [540, 250]], 'Shop records', 0, 16);
$parts[] = flow([[830, 264], [830, 423], [610, 423]], 'Shop records', 0, -12);
$parts[] = flow([[400, 423], [40, 423], [40, 300]], 'Shop reports', 80, -12);

file_put_contents($outDir . '/wbpos-dfd-3.0-shop-owner.svg', wrap_svg(980, 520, $parts));

// =============================================================================
// 4.0 Cashier — same layout as the picture, lookup return arrow corrected
// =============================================================================
$parts = [defs()];
$parts[] = entity(40, 200, 130, 80, 'Cashier');
$parts[] = process(270, 186, 210, 86, '4.1', "Record Sales\nTransaction");
$parts[] = process(560, 36, 210, 86, '4.2', "Process Item\nLookup");
$parts[] = process(300, 360, 210, 86, '4.3', "Generate Sales\nReceipt");
$parts[] = store(760, 198, 200, 52, 'D2', 'Tenant Shop Database');

$parts[] = flow([[170, 240], [270, 240]], 'Item code, payment', 0, -12);
$parts[] = flow([[375, 186], [375, 79], [560, 79]], 'Item code', 70, -12);
$parts[] = flow([[860, 198], [860, 79], [770, 79]], 'Item lookup', 0, -12);
$parts[] = flow([[560, 100], [500, 100], [500, 186]], 'Item & price data', -78, -12);
$parts[] = flow([[480, 229], [760, 229]], 'Sales & stock update', 0, -12);
$parts[] = flow([[375, 272], [375, 360]], 'Recorded sale', 70, 0);
$parts[] = flow([[300, 403], [40, 403], [40, 280]], 'Receipt', 50, -12);

file_put_contents($outDir . '/wbpos-dfd-4.0-cashier.svg', wrap_svg(990, 500, $parts));

echo "Wrote:\n";
echo " - {$outDir}/wbpos-dfd-1.0-registration.svg\n";
echo " - {$outDir}/wbpos-dfd-2.0-super-admin.svg\n";
echo " - {$outDir}/wbpos-dfd-3.0-shop-owner.svg\n";
echo " - {$outDir}/wbpos-dfd-4.0-cashier.svg\n";
