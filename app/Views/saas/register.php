<?php
/**
 * @var array $config
 * @var array|null $plan
 * @var bool $has_errors
 * @var $validation
 */
$monthly_price = 10.00;
$plan_id = (int)($plan['plan_id'] ?? 0);
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Start Subscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=11">
</head>
<body class="saas-modern saas-landing-body">

<header class="lp-nav lp-nav--scrolled" id="lp-nav">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--outline" href="<?= site_url() ?>">Back to home</a>
        </div>
    </div>
</header>

<main class="lp-reg">
    <div class="lp-reg__header saas-shell">
        <p class="lp-label">POS Subscription</p>
        <h1 class="lp-reg__title">Start Your POS Subscription</h1>
        <p class="lp-reg__subtitle">Full access to sales, inventory, reports &amp; staff — <strong>$<?= number_format($monthly_price, 0) ?>/month</strong></p>
    </div>

    <div class="lp-reg__layout saas-shell">
        <div class="lp-reg__form-wrap">
            <?php if ($has_errors): ?>
                <div class="lp-reg__alert">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= form_open('saas/register', ['class' => 'lp-reg__form']) ?>
            <input type="hidden" name="plan_id" value="<?= esc($plan_id) ?>">

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">1</span>
                    <div>
                        <h2>Business information</h2>
                        <p>Tell us about your store</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="lp-field-label" for="company_name">Company name</label>
                        <input class="lp-field-input" id="company_name" name="company_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="tenant_code">Company code</label>
                        <input class="lp-field-input" id="tenant_code" name="tenant_code" placeholder="my-store" required>
                        <span class="lp-field-hint">Short unique code (letters, numbers, dash)</span>
                    </div>
                </div>
            </section>

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">2</span>
                    <div>
                        <h2>Owner account</h2>
                        <p>Your login credentials for the POS</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_first_name">First name</label>
                        <input class="lp-field-input" id="owner_first_name" name="owner_first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_last_name">Last name</label>
                        <input class="lp-field-input" id="owner_last_name" name="owner_last_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_email">Email</label>
                        <input class="lp-field-input" type="email" id="owner_email" name="owner_email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_phone">Phone</label>
                        <input class="lp-field-input" id="owner_phone" name="owner_phone">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_username">POS username</label>
                        <input class="lp-field-input" id="owner_username" name="owner_username" required>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_password">POS password</label>
                        <input class="lp-field-input" type="password" id="owner_password" name="owner_password" minlength="8" pattern="<?= esc(strong_password_js_pattern(), 'attr') ?>" required>
                        <span class="lp-field-hint"><?= lang('Common.password_strong_hint') ?></span>
                    </div>
                </div>
            </section>

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">3</span>
                    <div>
                        <h2>Payment reference</h2>
                        <p>Scan the QR code, pay $<?= number_format($monthly_price, 0) ?>, then paste your receipt ID</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="lp-field-label" for="payment_reference">Transaction / receipt ID</label>
                        <input class="lp-field-input" id="payment_reference" name="payment_reference" placeholder="e.g. ABA receipt number" required>
                    </div>
                </div>
            </section>

            <div class="lp-reg__actions">
                <button class="lp-btn lp-btn--primary lp-btn--lg" type="submit">Submit subscription</button>
                <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Cancel</a>
            </div>
            <?= form_close() ?>
        </div>

        <aside class="lp-reg__aside">
            <div class="lp-reg__qr">
                <div class="lp-reg__qr-head">
                    <span class="lp-reg__qr-badge">Scan to pay</span>
                    <p class="lp-reg__qr-price">$<?= number_format($monthly_price, 0) ?><span>/month</span></p>
                </div>
                <div class="lp-reg__qr-frame lp-reg__qr-frame--code-only">
                    <img
                        src="<?= base_url('images/payment/aba-khqr-code.png') ?>"
                        alt="Scan QR code to pay $<?= number_format($monthly_price, 0) ?> per month"
                    >
                </div>
                <p class="lp-reg__qr-hint">Open ABA or any KHQR app and scan</p>
            </div>

            <ul class="lp-reg__checklist">
                <li>Sales &amp; inventory management</li>
                <li>Staff roles &amp; permissions</li>
                <li>Reports &amp; dashboard</li>
                <li>Cloud access — any device</li>
            </ul>
        </aside>
    </div>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <a class="lp-brand lp-brand--sm" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
        <p class="lp-footer__tagline">Cloud POS · $<?= number_format($monthly_price, 0) ?>/month</p>
    </div>
</footer>

</body>
</html>
