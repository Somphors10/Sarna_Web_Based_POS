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
$from_pos = !empty($from_pos);
$pos_home = site_url('home');
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
    <?= view('partial/favicon') ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=47">
</head>
<body class="saas-modern saas-landing-body lp-checkout-page">

<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <?php if ($from_pos): ?>
                <a class="lp-btn lp-btn--outline" href="<?= $pos_home ?>">Back to POS</a>
            <?php else: ?>
                <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
                <a class="lp-btn lp-btn--outline" href="<?= site_url() ?>">
                    <span class="lp-nav__label-full">Back to home</span>
                    <span class="lp-nav__label-short">Home</span>
                </a>
            <?php endif; ?>
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
                    <?php if ($from_pos): ?>
                        <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= $pos_home ?>">Back to POS</a>
                    <?php else: ?>
                        <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('saas/checkout') ?>">Find my shop</a>
                        <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back to home</a>
                    <?php endif; ?>
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
                    <?php if ($from_pos): ?>
                        Payment is already on file for this shop. You can go back and keep using POS.
                    <?php else: ?>
                        Payment is already on file for this shop. No need to scan KHQR again right now.
                        Sign in with the username and password you registered.
                    <?php endif; ?>
                </p>
                <div class="lp-pay-status__actions">
                    <?php if ($from_pos): ?>
                        <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= $pos_home ?>">Back to POS</a>
                    <?php else: ?>
                        <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('login') ?>">Go to POS login</a>
                        <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back to home</a>
                    <?php endif; ?>
                </div>
                <?php if (!$from_pos): ?>
                <p class="lp-pay-status__hint">
                    When the yellow renew banner appears (last 7 days), come back here via
                    <a href="<?= site_url('saas/checkout') ?>">Renew / pay</a>.
                </p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="saas-shell lp-pay-page">
            <header class="lp-pay-page__head">
                <div class="lp-pay-page__copy">
                    <p class="lp-checkout__eyebrow"><?= $is_renewal ? 'Renewal' : 'POS payment' ?></p>
                    <h1 class="lp-checkout__title"><?= $is_renewal ? 'Renew your subscription' : 'Scan KHQR to pay' ?></h1>
                    <p class="lp-checkout__lead">
                        <?= $is_renewal
                            ? 'Scan the KHQR in ABA, then enter the receipt number. Expiry moves forward by 1 month.'
                            : 'Scan the KHQR in ABA, then enter the receipt number to activate your shop.' ?>
                    </p>
                </div>
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
            </header>

            <div class="lp-pay-page__steps">
                <section class="lp-pay-step">
                    <div class="lp-pay-step__head">
                        <span class="lp-pay-step__num">1</span>
                        <div>
                            <h2>Scan to pay</h2>
                            <p>Open ABA and scan this KHQR.</p>
                        </div>
                        <p class="lp-pay-step__price">$<?= number_format($monthly_price, 0) ?><span>/mo</span></p>
                    </div>
                    <div class="lp-pay-step__body">
                        <?php if ($qr_image_exists): ?>
                            <?= khqr_scan_card_markup(base_url($qr_image_path) . '?v=8') ?>
                        <?php else: ?>
                            <div class="lp-reg__qr-missing">
                                <p><strong>QR image not found</strong></p>
                                <p><code>public/images/payment/aba-khqr-code.png</code></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="lp-pay-step lp-pay-step--form">
                    <div class="lp-pay-step__head">
                        <span class="lp-pay-step__num">2</span>
                        <div>
                            <h2>Enter receipt</h2>
                            <p>Paste the ABA receipt number after you pay.</p>
                        </div>
                    </div>
                    <div class="lp-pay-step__body">
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

                        <?php if ($from_pos): ?>
                        <p class="lp-checkout__footnote">
                            <a href="<?= $pos_home ?>">Back to POS</a>
                        </p>
                        <?php else: ?>
                        <p class="lp-checkout__footnote">
                            Wrong shop?
                            <a href="<?= site_url('saas/checkout') ?>">Find shop again</a>
                        </p>
                        <?php endif; ?>
                    </div>
                </section>
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
