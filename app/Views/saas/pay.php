<?php
/**
 * @var object|null $request
 * @var bool $already_paid
 * @var bool $has_errors
 * @var string $token
 * @var string $qr_image_path
 */
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$qr_image_exists = is_file(FCPATH . ($qr_image_path ?? 'images/payment/aba-khqr-code.png'));
$monthly_price = saas_monthly_price((float)($request?->price_monthly ?? 0));
$plan_name = (string)($request?->plan_name ?? 'POS');
$field_errors = ($has_errors ?? false) ? $validation->getErrors() : [];
$invalid = $request === null || (string)($request->status ?? '') !== 'approved';
$is_renewal = !empty($is_renewal);
$already_paid = !empty($already_paid);
$shop_name = esc((string)($request->company_name ?? 'Your shop'));

if ($invalid) {
    $page_title = 'Payment link problem';
} elseif ($already_paid) {
    $page_title = 'Already active';
} elseif ($is_renewal) {
    $page_title = 'Renew subscription';
} else {
    $page_title = 'Complete payment';
}
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | <?= esc($page_title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=45">
</head>
<body class="saas-modern saas-landing-body lp-checkout-page">

<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--outline" href="<?= site_url() ?>">
                <span class="lp-nav__label-full">Back to home</span>
                <span class="lp-nav__label-short">Home</span>
            </a>
        </div>
    </div>
</header>

<main class="lp-checkout lp-pay">
    <?php if ($invalid): ?>
        <div class="lp-pay-status saas-shell">
            <div class="lp-pay-status__card lp-pay-status__card--warn">
                <p class="lp-pay-status__eyebrow">Payment link</p>
                <h1 class="lp-pay-status__title">This link is not valid</h1>
                <p class="lp-pay-status__text">Ask Super Admin to send a new KHQR payment link, or find your shop again with company code and email.</p>
                <div class="lp-pay-status__actions">
                    <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('saas/checkout') ?>">Find my shop</a>
                    <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back to home</a>
                </div>
            </div>
        </div>

    <?php elseif ($already_paid): ?>
        <div class="lp-pay-status saas-shell">
            <div class="lp-pay-status__card lp-pay-status__card--ok">
                <span class="lp-pay-status__icon" aria-hidden="true">✓</span>
                <p class="lp-pay-status__eyebrow">Subscription active</p>
                <h1 class="lp-pay-status__title">You’re all set</h1>
                <p class="lp-pay-status__shop"><?= $shop_name ?></p>
                <p class="lp-pay-status__text">
                    Payment is already on file for this shop. No need to scan KHQR again right now.
                    Sign in with the username and password you registered.
                </p>
                <div class="lp-pay-status__actions">
                    <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('login') ?>">Go to POS login</a>
                    <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back to home</a>
                </div>
                <p class="lp-pay-status__hint">
                    When the yellow renew banner appears (last 7 days), come back here via
                    <a href="<?= site_url('saas/checkout') ?>">Renew / pay</a>.
                </p>
            </div>
        </div>

    <?php else: ?>
        <div class="lp-checkout__shell saas-shell lp-pay__shell">
            <aside class="lp-checkout__intro">
                <p class="lp-checkout__eyebrow"><?= $is_renewal ? 'Renewal' : 'POS payment' ?></p>
                <h1 class="lp-checkout__title"><?= $is_renewal ? 'Renew your subscription' : 'Scan KHQR to pay' ?></h1>
                <p class="lp-checkout__lead">
                    <?= $is_renewal
                        ? 'Pay with ABA KHQR, then enter the receipt number. Expiry moves forward by 1 month (leftover days are kept).'
                        : 'Pay in the ABA app on your phone. After you pay, enter the receipt number so we can activate your shop.' ?>
                </p>

                <div class="lp-pay__meta">
                    <div>
                        <span>Shop</span>
                        <strong><?= $shop_name ?></strong>
                    </div>
                    <div>
                        <span>Plan</span>
                        <strong><?= esc($plan_name) ?> · $<?= number_format($monthly_price, 0) ?>/mo</strong>
                    </div>
                </div>

                <div class="lp-reg__qr lp-pay__qr">
                    <div class="lp-reg__qr-head">
                        <span class="lp-reg__qr-badge">Step 1 · Scan to pay</span>
                        <p class="lp-reg__qr-price">$<?= number_format($monthly_price, 0) ?><span>/month</span></p>
                    </div>
                    <?php if ($qr_image_exists): ?>
                        <?= khqr_scan_card_markup(base_url($qr_image_path) . '?v=8') ?>
                    <?php else: ?>
                        <div class="lp-reg__qr-missing">
                            <p><strong>QR image not found</strong></p>
                            <p><code>public/images/payment/aba-khqr-code.png</code></p>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

            <div class="lp-checkout__panel">
                <div class="lp-checkout__panel-head">
                    <h2>Step 2 · Enter receipt</h2>
                    <p>After paying in ABA, paste the receipt / transaction number below.</p>
                </div>

                <?php if ($has_errors): ?>
                    <div class="lp-checkout__alert lp-checkout__alert--danger" role="alert">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <p><?= esc($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?= form_open('saas/pay/' . $token, ['class' => 'lp-checkout__form']) ?>
                    <div class="lp-checkout__field">
                        <label class="lp-field-label" for="payment_reference">ABA receipt number <span class="lp-field-required">*</span></label>
                        <input
                            class="lp-field-input<?= isset($field_errors['payment_reference']) ? ' is-invalid' : '' ?>"
                            id="payment_reference"
                            name="payment_reference"
                            placeholder="from your ABA app after paying"
                            value="<?= set_value('payment_reference') ?>"
                            required
                            minlength="3"
                            maxlength="100"
                            autocomplete="off"
                        >
                    </div>
                    <div class="lp-checkout__actions">
                        <button class="lp-btn lp-btn--primary lp-btn--lg lp-checkout__submit" type="submit">Submit receipt</button>
                    </div>
                <?= form_close() ?>

                <p class="lp-checkout__footnote">
                    Wrong shop?
                    <a href="<?= site_url('saas/checkout') ?>">Find shop again</a>
                </p>
            </div>
        </div>
    <?php endif; ?>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
