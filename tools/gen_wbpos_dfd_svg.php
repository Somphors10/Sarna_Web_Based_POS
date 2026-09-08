<?php
/**
 * Generate clean WBPOS DFD Level-2 SVGs with correct arrow directions.
 * Run: php tools/gen_wbpos_dfd_svg.php
 *
 * Layout style: entity left, processes center column, data stores right
 * (same structure as shop-owner / cashier diagrams).
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

function title(string $t, float $cx = 520): string
{
    return sprintf(
        '<text x="%.0f" y="36" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="20" font-weight="700" fill="#111">%s</text>',
        $cx,
        e($t)
    );
}

function footnote(string $t, float $y, float $cx = 520): string
{
    return sprintf(
        '<text x="%.0f" y="%.0f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="12" font-style="italic" fill="#444">%s</text>',
        $cx,
        $y,
        e($t)
    );
}

function entity(float $x, float $y, float $w, float $h, string $label): string
{
    return sprintf(
        '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="4" ry="4" fill="#fff" stroke="#111" stroke-width="2"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="14" font-weight="700" fill="#111">%s</text>',
        $x,
        $y,
        $w,
        $h,
        $x + $w / 2,
        $y + $h / 2 + 5,
        e($label)
    );
}

function process(float $x, float $y, float $w, float $h, string $num, string $name): string
{
    $band = 24;
    return sprintf(
        '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="6" ry="6" fill="#fff" stroke="#111" stroke-width="1.8"/>' .
        '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#111" stroke-width="1.5"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" fill="#111">%s</text>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" fill="#111">%s</text>',
        $x,
        $y,
        $w,
        $h,
        $x,
        $y + $band,
        $x + $w,
        $y + $band,
        $x + $w / 2,
        $y + 16,
        e($num),
        $x + $w / 2,
        $y + $band + ($h - $band) / 2 + 5,
        e($name)
    );
}

function store(float $x, float $y, float $w, float $h, string $id, string $name): string
{
    $left = 30;
    return sprintf(
        '<path d="M %.1f %.1f H %.1f V %.1f H %.1f" fill="none" stroke="#111" stroke-width="1.8"/>' .
        '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#111" stroke-width="1.8"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" fill="#111">%s</text>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" fill="#111">%s</text>',
        $x,
        $y,
        $x + $w,
        $y + $h,
        $x,
        $x + $left,
        $y,
        $x + $left,
        $y + $h,
        $x + $left / 2,
        $y + $h / 2 + 5,
        e($id),
        $x + $left + ($w - $left) / 2,
        $y + $h / 2 + 5,
        e($name)
    );
}

/** Orthogonal polyline with arrow at end. Points: [[x,y], ...] */
function flow(array $pts, string $label, float $lx = 0, float $ly = -8): string
{
    $d = '';
    foreach ($pts as $i => $p) {
        $d .= ($i === 0 ? 'M' : 'L') . sprintf(' %.1f %.1f', $p[0], $p[1]);
    }
    // Label near middle segment
    $n = count($pts);
    $a = $pts[(int) floor(($n - 1) / 2)];
    $b = $pts[(int) ceil(($n - 1) / 2)];
    if ($a === $b && $n > 2) {
        $a = $pts[1];
        $b = $pts[min(2, $n - 1)];
    }
    $mx = ($a[0] + $b[0]) / 2 + $lx;
    $my = ($a[1] + $b[1]) / 2 + $ly;

    return sprintf(
        '<path d="%s" fill="none" stroke="#111" stroke-width="1.5" marker-end="url(#arrow)"/>' .
        '<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="11" fill="#222">%s</text>',
        $d,
        $mx,
        $my,
        e($label)
    );
}

function wrap_svg(int $w, int $h, array $parts): string
{
    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">%s%s</svg>',
        $w,
        $h,
        $w,
        $h,
        '<rect width="100%" height="100%" fill="#fff"/>',
        implode("\n", $parts)
    );
}

// =============================================================================
// A) Registrant — Process Registration (1.0)
// =============================================================================
$W = 1040;
$H = 820;

$ex = 40;
$ey = 340;
$ew = 160;
$eh = 64;
$bus = $ex + $ew / 2; // vertical trunk under entity

$px = 320;
$pw = 260;
$ph = 72;
$gap = 48;
$py = [];
$py[0] = 70;
for ($i = 1; $i < 4; $i++) {
    $py[$i] = $py[$i - 1] + $ph + $gap;
}

$sx = 700;
$sy = 280;
$sw = 280;
$sh = 100;

$parts = [defs(), title('DFD Level 2 – Process Registration (1.0)', $W / 2)];
$parts[] = entity($ex, $ey, $ew, $eh, 'New Shop Registrant');
$parts[] = process($px, $py[0], $pw, $ph, '1.1', 'Submit Registration');
$parts[] = process($px, $py[1], $pw, $ph, '1.2', 'Send Verify Email');
$parts[] = process($px, $py[2], $pw, $ph, '1.3', 'Confirm Email Verification');
$parts[] = process($px, $py[3], $pw, $ph, '1.4', 'Pay with KHQR');
$parts[] = store($sx, $sy, $sw, $sh, 'D1', 'Platform Database');

// Entity trunk (visual guide only — thin dashed)
$parts[] = sprintf(
    '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#bbb" stroke-width="1" stroke-dasharray="4 4"/>',
    $bus,
    $ey + $eh,
    $bus,
    $py[3] + $ph / 2
);

// E → 1.1 Registration data
$parts[] = flow(
    [[$ex + $ew, $ey + 22], [280, $ey + 22], [280, $py[0] + $ph / 2], [$px, $py[0] + $ph / 2]],
    'Registration data',
    0,
    -10
);

// 1.1 → D1 write (top)
$y11w = $py[0] + 22;
$parts[] = flow(
    [[$px + $pw, $y11w], [640, $y11w], [640, $sy + 22], [$sx, $sy + 22]],
    'Subscription request (pending)',
    20,
    -10
);
// D1 → 1.1 read (below write)
$y11r = $py[0] + 52;
$parts[] = flow(
    [[$sx, $sy + 45], [620, $sy + 45], [620, $y11r], [$px + $pw, $y11r]],
    'Saved request id',
    10,
    14
);

// 1.1 → 1.2
$parts[] = flow(
    [[$px + $pw / 2, $py[0] + $ph], [$px + $pw / 2, $py[1]]],
    'Pending request',
    70,
    0
);

// 1.2 → D1 email token (enter bottom of D1)
$parts[] = flow(
    [[$px + $pw, $py[1] + 22], [660, $py[1] + 22], [660, $sy + $sh], [$sx + 90, $sy + $sh]],
    'email_verify_token',
    0,
    -10
);

// 1.2 → Entity verify email (left side, above trunk join)
$parts[] = flow(
    [[$px, $py[1] + 36], [220, $py[1] + 36], [220, $ey + $eh + 8], [$bus, $ey + $eh]],
    'Verify email link',
    -55,
    -2
);

// Entity → 1.3 click verify
$parts[] = flow(
    [[$bus, $ey + $eh], [200, $ey + $eh + 50], [200, $py[2] + 28], [$px, $py[2] + 28]],
    'Click verify link + token',
    -70,
    0
);

// 1.3 → D1 lookup/mark
$parts[] = flow(
    [[$px + $pw, $py[2] + 22], [680, $py[2] + 22], [680, $sy + $sh + 28], [$sx + 50, $sy + $sh]],
    'Lookup / mark verified',
    10,
    14
);
// D1 → 1.3 request by token
$parts[] = flow(
    [[$sx + 140, $sy + $sh], [710, $sy + $sh + 55], [710, $py[2] + 52], [$px + $pw, $py[2] + 52]],
    'Request by token',
    35,
    12
);

// 1.3 → Entity success
$parts[] = flow(
    [[$px, $py[2] + 52], [175, $py[2] + 52], [175, $ey + $eh + 95], [$ex + 28, $ey + $eh]],
    'Verification success',
    -75,
    0
);

// Entity → 1.4 open pay page
$parts[] = flow(
    [[$ex + 36, $ey + $eh], [155, 560], [155, $py[3] + 28], [$px, $py[3] + 28]],
    'Open pay URL + payment ref',
    -75,
    0
);

// D1 → 1.4 read approved (must come from D1 down to 1.4)
$parts[] = flow(
    [[$sx + 200, $sy + $sh], [760, $sy + $sh + 70], [760, $py[3] + 22], [$px + $pw, $py[3] + 22]],
    'Approved request + payment_token',
    20,
    14
);
// 1.4 → D1 write payment/active
$parts[] = flow(
    [[$px + $pw, $py[3] + 52], [790, $py[3] + 52], [790, $sy + $sh + 100], [$sx + 230, $sy + $sh]],
    'payment_reference + tenant active',
    10,
    14
);

// 1.4 → Entity shop active
$parts[] = flow(
    [[$px, $py[3] + 52], [120, $py[3] + 52], [120, $ey + $eh + 130], [$ex + 18, $ey + $eh + 8]],
    'Payment success / shop active',
    -55,
    0
);

$parts[] = footnote('1.4 starts only after Super Admin Approve creates payment_token (Process 2.2).', $H - 24, $W / 2);

file_put_contents($outDir . '/wbpos-dfd-registrant.svg', wrap_svg($W, $H, $parts));

// =============================================================================
// B) Super Admin — Platform Management (2.0)
// =============================================================================
$W2 = 1080;
$H2 = 860;

$ex = 40;
$ey = 360;
$ew = 140;
$eh = 60;
$bus = $ex + $ew / 2;

$px = 300;
$pw = 270;
$ph = 70;
$gap = 50;
$py = [];
$py[0] = 70;
for ($i = 1; $i < 4; $i++) {
    $py[$i] = $py[$i - 1] + $ph + $gap;
}

$sx1 = 680;
$sy1 = 90;
$sw = 300;
$sh = 90;
$sx2 = 680;
$sy2 = 430;

$parts = [defs(), title('DFD Level 2 – Platform Management (2.0)', $W2 / 2)];
$parts[] = entity($ex, $ey, $ew, $eh, 'Super Admin');
$parts[] = process($px, $py[0], $pw, $ph, '2.1', 'Review Registration Requests');
$parts[] = process($px, $py[1], $pw, $ph, '2.2', 'Approve / Reject Request');
$parts[] = process($px, $py[2], $pw, $ph, '2.3', 'Send KHQR / Confirm Payment');
$parts[] = process($px, $py[3], $pw, $ph, '2.4', 'Manage Tenant Status');
$parts[] = store($sx1, $sy1, $sw, $sh, 'D1', 'Platform Database');
$parts[] = store($sx2, $sy2, $sw, $sh, 'D2', 'Tenant Shop Database');

$parts[] = sprintf(
    '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#bbb" stroke-width="1" stroke-dasharray="4 4"/>',
    $bus,
    $ey + $eh,
    $bus,
    $py[3] + $ph / 2
);

// D1 → 2.1 pending requests
$parts[] = flow(
    [[$sx1, $sy1 + 35], [600, $sy1 + 35], [600, $py[0] + 28], [$px + $pw, $py[0] + 28]],
    'Pending / verified requests',
    0,
    -10
);

// 2.1 → Super Admin list
$parts[] = flow(
    [[$px, $py[0] + 40], [230, $py[0] + 40], [230, $ey], [$bus, $ey]],
    'Request list / detail',
    -50,
    -8
);

// Super Admin → 2.2 approve/reject
$parts[] = flow(
    [[$ex + $ew, $ey + 18], [250, $ey + 18], [250, $py[1] + 24], [$px, $py[1] + 24]],
    'Approve or reject + reason',
    -10,
    -10
);

// 2.2 → D1 (reject notes OR approve tenant + token)
$parts[] = flow(
    [[$px + $pw, $py[1] + 22], [620, $py[1] + 22], [620, $sy1 + $sh], [$sx1 + 70, $sy1 + $sh]],
    'Reject notes OR tenant + payment_token',
    10,
    14
);

// 2.2 → D2 seed ONLY (approve)
$parts[] = flow(
    [[$px + $pw, $py[1] + 50], [600, $py[1] + 50], [600, $sy2 + 35], [$sx2, $sy2 + 35]],
    'Seed shop owner data (approve only)',
    10,
    -10
);

// 2.2 → 2.3
$parts[] = flow(
    [[$px + $pw / 2, $py[1] + $ph], [$px + $pw / 2, $py[2]]],
    'Approved + payment_token',
    85,
    0
);

// 2.3 → Super Admin KHQR
$parts[] = flow(
    [[$px, $py[2] + 24], [210, $py[2] + 24], [210, $ey + $eh], [$bus, $ey + $eh]],
    'KHQR / payment link',
    -55,
    8
);

// Super Admin → 2.3 confirm
$parts[] = flow(
    [[$ex + $ew, $ey + 42], [235, $ey + 42], [235, $py[2] + 48], [$px, $py[2] + 48]],
    'Confirm payment / activate',
    -5,
    14
);

// 2.3 → D1 active (NOT D2)
$parts[] = flow(
    [[$px + $pw, $py[2] + 35], [640, $py[2] + 35], [640, $sy1 + $sh + 45], [$sx1 + 160, $sy1 + $sh]],
    'payment_reference + tenant active',
    15,
    14
);

// Super Admin → 2.4
$parts[] = flow(
    [[$ex + 30, $ey + $eh], [165, 560], [165, $py[3] + 28], [$px, $py[3] + 28]],
    'Suspend / activate / cancel',
    -60,
    0
);

// D1 → 2.4 tenant profile (NOT D2) — route along right side away from D2
$parts[] = flow(
    [[$sx1 + 40, $sy1 + $sh], [820, $sy1 + $sh + 40], [820, $py[3] + 22], [$px + $pw, $py[3] + 22]],
    'Tenant profile / status',
    55,
    0
);

// 2.4 → D1 status update (NOT D2) — far right lane, then up to D1 bottom
$parts[] = flow(
    [[$px + $pw, $py[3] + 50], [860, $py[3] + 50], [860, $sy1 + $sh + 80], [$sx1 + 220, $sy1 + $sh]],
    'Update tenants.status',
    40,
    14
);

// 2.4 → Super Admin status view
$parts[] = flow(
    [[$px, $py[3] + 50], [130, $py[3] + 50], [130, $ey + $eh + 90], [$ex + 20, $ey + $eh]],
    'Business status view',
    -60,
    0
);

$parts[] = footnote('D1 = requests, tenants, payment_token, status.   D2 = shop seed only on approve (2.2).', $H2 - 24, $W2 / 2);

file_put_contents($outDir . '/wbpos-dfd-super-admin.svg', wrap_svg($W2, $H2, $parts));

echo "Wrote:\n";
echo " - {$outDir}/wbpos-dfd-registrant.svg\n";
echo " - {$outDir}/wbpos-dfd-super-admin.svg\n";
