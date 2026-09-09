<?php
/**
 * @var string $company_name
 * @var string $owner_name
 * @var string $expires_label
 * @var string $stage
 * @var string $pay_url
 * @var int $days_left
 */
$company = esc($company_name);
$owner = esc($owner_name);
$expires = esc($expires_label);
$link = esc($pay_url, 'attr');
$days = (int)$days_left;

if ($stage === 'expired') {
    $headline = 'Your WBPOS subscription has expired';
    $body = 'Your shop <strong>' . $company . '</strong> is locked until you renew. Pay with KHQR to open login again.';
} elseif ($stage === '1') {
    $headline = 'Your WBPOS subscription ends tomorrow';
    $body = 'Your shop <strong>' . $company . '</strong> expires on <strong>' . $expires . '</strong> (about 1 day left). Renew now so staff can keep selling.';
} elseif ($stage === '3') {
    $headline = 'Your WBPOS subscription ends in a few days';
    $body = 'Your shop <strong>' . $company . '</strong> expires on <strong>' . $expires . '</strong> (' . max(0, $days) . ' day(s) left). Renew soon to avoid interruption.';
} else {
    $headline = 'Your WBPOS subscription ends in 7 days';
    $body = 'Your shop <strong>' . $company . '</strong> expires on <strong>' . $expires . '</strong> (' . max(0, $days) . ' day(s) left). Renew early — leftover days are kept.';
}
?>
<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.5;margin:0;padding:24px;background:#f8fafc;">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;padding:28px;border:1px solid #e2e8f0;">
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#ca8a04;font-weight:700;">WBPOS subscription</p>
    <h1 style="margin:0 0 12px;font-size:22px;"><?= esc($headline) ?></h1>
    <p>Hello <?= $owner !== '' ? $owner : 'Owner' ?>,</p>
    <p><?= $body ?></p>
    <p style="margin:28px 0;">
        <a href="<?= $link ?>" style="display:inline-block;background:#7c3aed;color:#fff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:10px;">Renew / pay with KHQR</a>
    </p>
    <p style="font-size:13px;color:#64748b;">If the button does not work, open this link:<br><?= esc($pay_url) ?></p>
    <p style="font-size:12px;color:#94a3b8;margin:24px 0 0;">You also see a yellow warning banner inside POS when you are logged in.</p>
</div>
</body>
</html>
