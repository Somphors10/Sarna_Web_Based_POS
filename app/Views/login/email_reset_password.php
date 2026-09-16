<?php

/**
 * @var object $employee
 * @var string $reset_url
 * @var string $company_name
 */
$first = esc(trim((string)($employee->first_name ?? '')));
if ($first === '') {
    $first = esc((string)($employee->username ?? 'there'));
}
$company = esc($company_name);
$link = esc($reset_url, 'attr');
?>
<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.5;margin:0;padding:24px;background:#f8fafc;">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;padding:28px;border:1px solid #e2e8f0;">
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7c3aed;font-weight:700;">WBPOS</p>
    <h1 style="margin:0 0 12px;font-size:22px;">Reset your password</h1>
    <p>Hello <?= $first ?>,</p>
    <p>We received a request to reset the password for your account at <strong><?= $company ?></strong>.</p>
    <p style="margin:28px 0;">
        <a href="<?= $link ?>" style="display:inline-block;background:#7c3aed;color:#fff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:10px;">Choose a new password</a>
    </p>
    <p style="font-size:13px;color:#64748b;">This link expires in 1 hour and can only be used once.</p>
    <p style="font-size:13px;color:#64748b;">If you did not request this, you can ignore this email. Your password will stay the same.</p>
    <p style="font-size:13px;color:#64748b;">If the button does not work, copy this link:<br><?= esc($reset_url) ?></p>
</div>
</body>
</html>
