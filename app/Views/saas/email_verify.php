<?php
/**
 * @var object $request
 * @var string $verify_url
 */
$first = esc((string)($request->owner_first_name ?? ''));
$company = esc((string)($request->company_name ?? ''));
$link = esc($verify_url, 'attr');
?>
<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.5;margin:0;padding:24px;background:#f8fafc;">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;padding:28px;border:1px solid #e2e8f0;">
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7c3aed;font-weight:700;">WBPOS</p>
    <h1 style="margin:0 0 12px;font-size:22px;">Verify your email</h1>
    <p>Hello <?= $first ?>,</p>
    <p>Confirm this email for <strong><?= $company ?></strong> so Super Admin can review your POS registration.</p>
    <p style="margin:28px 0;">
        <a href="<?= $link ?>" style="display:inline-block;background:#7c3aed;color:#fff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:10px;">Verify email</a>
    </p>
    <p style="font-size:13px;color:#64748b;">If the button does not work, copy this link:<br><?= esc($verify_url) ?></p>
</div>
</body>
</html>
