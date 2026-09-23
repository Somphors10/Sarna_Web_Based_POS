<?php
/**
 * @var object $request
 * @var object|null $plan
 * @var object|null $tenant
 * @var string $pay_url
 * @var string $qr_image_path
 */
$qr_exists = is_file(FCPATH . $qr_image_path);
$price = saas_monthly_price((float)($plan?->price_monthly ?? 0));
$plan_name = (string)($plan?->plan_name ?? 'POS');
$tenant_status = strtolower((string)($tenant->status ?? ''));
$already_paid = $tenant_status === 'active' || !empty($paid);
$owner_email = (string)$request->owner_email;
$email_sent = !empty($email_sent);
$email_failed = !empty($email_failed);
$email_error = trim((string)($email_error ?? ''));
$request_id = (int)$request->request_id;
$preview_url = site_url('super-admin/preview-khqr-email/' . $request_id);
$mailhog_url = 'http://localhost:8025';
$delivery = $delivery ?? \App\Libraries\PlatformMail::deliveryInfo();
$using_gmail = ($delivery['mode'] ?? '') === 'gmail';
$using_mailhog = ($delivery['mode'] ?? '') === 'mailhog' && \App\Libraries\PlatformMail::isLocalDevHost();
$company = (string)$request->company_name;
$company_parts = preg_split('/\s+/', trim($company)) ?: [];
$initials = strtoupper(mb_substr((string)($company_parts[0] ?? 'W'), 0, 1) . mb_substr((string)($company_parts[1] ?? ''), 0, 1));
$has_outbox = \App\Libraries\PlatformMail::hasOutbox($request_id);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>WBPOS | Super Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('images/favicon.svg?v=5') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png?v=5') ?>">
    <link rel="icon" href="<?= base_url('favicon.ico?v=5') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/super-admin.css?v=42') ?>">
</head>
<body class="sa-dashboard sa-khqr">
<main class="sa-khqr__page">
    <header class="sa-khqr__hero">
        <a class="sa-khqr__back" href="<?= site_url('super-admin/requests') ?>">← Requests</a>
        <div class="sa-khqr__hero-row">
            <div>
                <p class="sa-khqr__eyebrow">Super Admin · Payment</p>
                <h1>Send KHQR to the owner</h1>
                <p class="sa-khqr__lead">Shop is approved. Email the ABA QR, then mark $20 received so they can log in.</p>
            </div>
            <span class="sa-khqr__status <?= $already_paid ? 'is-paid' : 'is-wait' ?>">
                <?= $already_paid ? 'Paid · login allowed' : 'Approved · waiting $20' ?>
            </span>
        </div>
    </header>

    <?php if ($already_paid): ?>
        <div class="sa-khqr__banner sa-khqr__banner--ok">Payment confirmed. The owner can log in to POS now.</div>
    <?php elseif ($email_sent && $using_gmail): ?>
        <div class="sa-khqr__banner sa-khqr__banner--ok">KHQR emailed to <strong><?= esc($owner_email) ?></strong>. Ask them to check inbox and spam.</div>
    <?php elseif ($email_sent && $using_mailhog): ?>
        <div class="sa-khqr__banner sa-khqr__banner--info">
            Email is on this computer only (MailHog) — not in Gmail.
            <a href="<?= esc($mailhog_url, 'attr') ?>" target="_blank" rel="noopener">Open local inbox</a>
        </div>
    <?php elseif ($email_sent): ?>
        <div class="sa-khqr__banner sa-khqr__banner--ok">KHQR emailed to <strong><?= esc($owner_email) ?></strong>.</div>
    <?php elseif ($email_failed): ?>
        <div class="sa-khqr__banner sa-khqr__banner--err">
            Could not send to <strong><?= esc($owner_email) ?></strong>.
            <?php if ($email_error !== ''): ?>
                <span><?= esc(mb_substr($email_error, 0, 220)) ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="sa-khqr__grid">
        <aside class="sa-khqr-qr">
            <div class="sa-khqr-qr__top">
                <span class="sa-khqr-qr__badge">ABA KHQR</span>
                <p class="sa-khqr-qr__plan"><?= esc($plan_name) ?></p>
            </div>
            <p class="sa-khqr-qr__price">$<?= number_format($price, 0) ?><span>/month</span></p>
            <?php if ($qr_exists): ?>
                <?= khqr_scan_card_markup(base_url($qr_image_path) . '?v=8') ?>
            <?php endif; ?>
        </aside>

        <section class="sa-khqr-card">
            <div class="sa-khqr-shop">
                <span class="sa-khqr-shop__avatar" aria-hidden="true"><?= esc($initials) ?></span>
                <div>
                    <h2><?= esc($company) ?></h2>
                    <p><?= esc($owner_email) ?></p>
                </div>
            </div>

            <ol class="sa-khqr-steps">
                <li class="is-done">
                    <span>1</span>
                    <div>
                        <strong>Registered</strong>
                        <em>Owner signed up — no payment yet</em>
                    </div>
                </li>
                <li class="is-done">
                    <span>2</span>
                    <div>
                        <strong>Approved</strong>
                        <em>You approved the shop</em>
                    </div>
                </li>
                <li class="<?= $already_paid || $using_gmail ? 'is-done' : 'is-now' ?>">
                    <span>3</span>
                    <div>
                        <strong>Email KHQR</strong>
                        <em><?= $using_gmail ? 'Sent to the owner’s Gmail' : 'Send or preview the payment QR' ?></em>
                    </div>
                </li>
                <li class="<?= $already_paid ? 'is-done' : '' ?>">
                    <span>4</span>
                    <div>
                        <strong>Mark $20 received (unlock login)</strong>
                        <em>Then they can log in to POS</em>
                    </div>
                </li>
            </ol>

            <div class="sa-khqr-actions">
                <?php if ($has_outbox): ?>
                    <a class="sa-khqr-btn sa-khqr-btn--ghost" href="<?= esc($preview_url, 'attr') ?>" target="_blank" rel="noopener">Preview email</a>
                <?php endif; ?>
                <?php if ($using_mailhog): ?>
                    <a class="sa-khqr-btn sa-khqr-btn--ghost" href="<?= esc($mailhog_url, 'attr') ?>" target="_blank" rel="noopener">Local inbox</a>
                <?php endif; ?>
            </div>

            <?= form_open('super-admin/resend-payment-email/' . $request_id, ['class' => 'sa-khqr-form']) ?>
            <button class="sa-khqr-btn sa-khqr-btn--primary sa-khqr-btn--block" type="submit">Resend QR to email</button>
            <?= form_close() ?>

            <?php if (!$already_paid): ?>
                <div class="sa-khqr-paid">
                    <?= form_open('super-admin/confirm-payment/' . $request_id) ?>
                    <p class="sa-khqr-paid__title">Money arrived?</p>
                    <label class="sa-khqr-label" for="payment_reference">ABA receipt <span>(optional)</span></label>
                    <input class="sa-khqr-input" id="payment_reference" name="payment_reference" placeholder="Receipt number">
                    <button class="sa-khqr-btn sa-khqr-btn--success sa-khqr-btn--block" type="submit">They have paid — allow login</button>
                    <?= form_close() ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php if (!$using_gmail): ?>
        <section class="sa-khqr-gmail">
            <div class="sa-khqr-gmail__intro">
                <span class="sa-khqr-gmail__icon" aria-hidden="true">@</span>
                <div>
                    <h3>Send to a real Gmail inbox</h3>
                    <p>This computer cannot deliver to <strong><?= esc($owner_email) ?></strong> by itself. Use a Google <strong>App Password</strong> from the Gmail that will send the message — not your normal password.</p>
                    <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">Create an App Password →</a>
                </div>
            </div>
            <?= form_open('super-admin/save-gmail-smtp/' . $request_id, ['class' => 'sa-khqr-gmail__form']) ?>
            <div class="sa-khqr-gmail__fields">
                <div>
                    <label class="sa-khqr-label" for="gmail_user">Sending Gmail</label>
                    <input class="sa-khqr-input" id="gmail_user" name="gmail_user" type="email" value="<?= esc((string)($delivery['user'] ?? ''), 'attr') ?>" placeholder="you@gmail.com" required>
                </div>
                <div>
                    <label class="sa-khqr-label" for="gmail_app_password">App Password</label>
                    <input class="sa-khqr-input" id="gmail_app_password" name="gmail_app_password" type="password" autocomplete="new-password" placeholder="xxxx xxxx xxxx xxxx" required>
                </div>
            </div>
            <button class="sa-khqr-btn sa-khqr-btn--primary" type="submit">Save and send KHQR</button>
            <?= form_close() ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
