<?php
/**
 * Gmail-safe KHQR payment email.
 *
 * @var object $request
 * @var string $pay_url
 * @var float $price
 * @var bool $qr_cid
 */
$first = esc((string)($request->owner_first_name ?? ''));
$company = esc((string)($request->company_name ?? ''));
$code = esc((string)($request->tenant_code ?? ''));
$amount = number_format((float)$price, 0);
$pay = esc((string)$pay_url, 'attr');
$has_qr = !empty($qr_cid);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WBPOS KHQR</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">
    Pay $<?= $amount ?>/month for <?= $company ?> — scan ABA KHQR to finish activating your shop.
</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef2f7;">
    <tr>
        <td align="center" style="padding:28px 12px;">
            <table role="presentation" width="520" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                <tr>
                    <td align="center" bgcolor="#0b1f4d" style="background:#0b1f4d;padding:22px 24px;">
                        <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#93c5fd;font-weight:700;">WBPOS</p>
                        <p style="margin:8px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:22px;line-height:1.25;color:#ffffff;font-weight:700;">Scan to pay</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 28px 8px;font-family:Arial,Helvetica,sans-serif;color:#0f172a;font-size:15px;line-height:1.55;">
                        <p style="margin:0 0 10px;">Hello <?= $first ?>,</p>
                        <p style="margin:0 0 10px;">Your shop <strong><?= $company ?></strong> is activated.</p>
                        <p style="margin:0;">Pay <strong>$<?= $amount ?> / month</strong> with ABA, then Super Admin will open your login.</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:8px 28px 20px;">
                        <table role="presentation" width="280" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:280px;border:1px solid #e8edf5;border-radius:16px;overflow:hidden;background:#ffffff;">
                            <tr>
                                <td align="center" bgcolor="#e11d2e" style="background:#e11d2e;padding:11px 12px;">
                                    <span style="font-family:Arial,Helvetica,sans-serif;color:#ffffff;font-size:13px;font-weight:800;letter-spacing:0.16em;">KHQR</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 16px;">
                                    <div style="border-bottom:2px dashed #e2e8f0;line-height:0;font-size:0;">&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding:18px 16px 20px;background:#ffffff;">
                                    <?php if ($has_qr): ?>
                                        <img src="cid:wbpos-khqr" width="240" height="240" alt="ABA KHQR" style="display:block;border:0;width:240px;height:240px;margin:0 auto;">
                                    <?php else: ?>
                                        <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#64748b;">Open the KHQR image in this email and scan it in ABA.</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px 28px 24px;font-family:Arial,Helvetica,sans-serif;color:#475569;font-size:14px;line-height:1.55;">
                        <p style="margin:0 0 10px;">Open the <strong>ABA</strong> app and scan the QR code above.</p>
                        <p style="margin:0 0 10px;">Company code: <strong style="color:#0f172a;"><?= $code ?></strong></p>
                        <p style="margin:0;">After you pay, tell Super Admin. They will open your POS login.</p>
                        <?php if ($pay_url !== ''): ?>
                            <p style="margin:18px 0 0;">
                                <a href="<?= $pay ?>" style="display:inline-block;background:#0b1f4d;color:#ffffff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:8px;">Open payment page</a>
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:14px 24px 20px;border-top:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#94a3b8;">
                        WBPOS · ABA KHQR
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
