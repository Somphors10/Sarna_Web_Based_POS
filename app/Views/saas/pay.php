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
$monthly_price = saas_monthly_price((float)($request->price_monthly ?? 0));
$plan_name = (string)($request->plan_name ?? 'POS');
$field_errors = ($has_errors ?? false) ? $validation->getErrors() : [];
$invalid = $request === null || (string)($request->status ?? '') !== 'approved';
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Complete payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=33">
</head>
<body class="saas-modern saas-landing-body">

<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
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

<main class="lp-reg">
    <div class="lp-reg__header saas-shell">
        <p class="lp-label">POS payment</p>
        <h1 class="lp-reg__title">Scan KHQR to pay</h1>
        <p class="lp-reg__subtitle">Pay in the ABA app on your phone. After you pay, enter the receipt number below.</p>
    </div>

    <div class="lp-reg__layout lp-reg__layout--pay saas-shell">
        <?php if ($invalid): ?>
            <div class="lp-reg__form-wrap">
                <div class="lp-reg__alert">
                    <p>This payment link is invalid or expired. Ask Super Admin to send a new one.</p>
                </div>
            </div>
        <?php elseif (!empty($already_paid)): ?>
            <div class="lp-reg__form-wrap">
                <section class="lp-reg__section">
                    <h2>Payment already received</h2>
                    <p>Your shop is active. You can sign in with the username and password you registered.</p>
                    <div class="lp-reg__actions">
                        <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('login') ?>">Go to POS login</a>
                    </div>
                </section>
            </div>
        <?php else: ?>
            <aside class="lp-reg__aside">
                <div class="lp-reg__qr">
                    <div class="lp-reg__qr-head">
                        <span class="lp-reg__qr-badge">Step 1 · Scan to pay</span>
                        <p class="lp-reg__qr-price">$<?= number_format($monthly_price, 0) ?><span>/month</span></p>
                    </div>
                    <div class="lp-reg__qr-tile">
                        <div class="lp-reg__qr-tile-inner">
                            <div class="lp-reg__qr-khqr">KHQR</div>
                            <?php if ($qr_image_exists): ?>
                            <img
                                src="<?= base_url($qr_image_path) ?>?v=4"
                                alt="Scan this KHQR with ABA on your phone"
                                width="512"
                                height="512"
                            >
                            <?php else: ?>
                            <div class="lp-reg__qr-missing">
                                <p><strong>QR image not found</strong></p>
                                <p><code>public/images/payment/aba-khqr-code.png</code></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="lp-reg__qr-hint">Open ABA on your phone and scan this picture. Do not type anything to pay.</p>
                </div>
            </aside>

            <div class="lp-reg__form-wrap">
                <?php if ($has_errors): ?>
                    <div class="lp-reg__alert">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <p><?= esc($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?= form_open('saas/pay/' . $token, ['class' => 'lp-reg__form']) ?>
                <section class="lp-reg__section">
                    <div class="lp-reg__section-head">
                        <span class="lp-reg__step">2</span>
                        <div>
                            <h2><?= esc((string)$request->company_name) ?></h2>
                            <p>After you paid in ABA, paste the receipt number so we can open your login.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="lp-field-label" for="payment_reference">ABA receipt number <span class="lp-field-required">*</span></label>
                            <input class="lp-field-input<?= isset($field_errors['payment_reference']) ? ' is-invalid' : '' ?>" id="payment_reference" name="payment_reference" placeholder="from your ABA app after paying" value="<?= set_value('payment_reference') ?>" required minlength="3" maxlength="100">
                        </div>
                    </div>
                </section>
                <div class="lp-reg__actions">
                    <button class="lp-btn lp-btn--primary lp-btn--lg" type="submit">Submit receipt</button>
                </div>
                <?= form_close() ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
