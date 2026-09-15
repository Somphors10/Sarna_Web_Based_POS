<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$status_message = (string)($status_message ?? '');
$tenant_code = (string)($tenant_code ?? '');
$owner_email = (string)($owner_email ?? '');
$has_errors = !empty($has_errors);
$monthly_price = saas_monthly_price();
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=42">
</head>
<body class="saas-modern saas-landing-body lp-checkout-page">
<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--outline" href="<?= site_url('saas/register') ?>">Register</a>
        </div>
    </div>
</header>

<main class="lp-checkout">
    <div class="lp-checkout__shell saas-shell">
        <aside class="lp-checkout__intro">
            <p class="lp-checkout__eyebrow">Subscription payment</p>
            <h1 class="lp-checkout__title">Find your shop&nbsp;&amp; open KHQR</h1>
            <p class="lp-checkout__lead">
                Use this for <strong>first activation</strong> or to <strong>renew</strong> when the yellow warning appears (or after expiry).
            </p>

            <ol class="lp-checkout__steps">
                <li class="is-current">
                    <span class="lp-checkout__step-num">1</span>
                    <div>
                        <strong>Enter shop details</strong>
                        <p>Company code and the email you used when registering.</p>
                    </div>
                </li>
                <li>
                    <span class="lp-checkout__step-num">2</span>
                    <div>
                        <strong>Scan KHQR in ABA</strong>
                        <p>Pay $<?= number_format($monthly_price, 0) ?>/month with your bank app.</p>
                    </div>
                </li>
                <li>
                    <span class="lp-checkout__step-num">3</span>
                    <div>
                        <strong>Enter receipt number</strong>
                        <p>Expiry moves forward by 1 month (leftover days are kept).</p>
                    </div>
                </li>
            </ol>
        </aside>

        <div class="lp-checkout__panel">
            <div class="lp-checkout__panel-head">
                <h2>Complete payment</h2>
                <p>We will open your KHQR pay page after we match your shop.</p>
            </div>

            <?php if ($has_errors): ?>
                <div class="lp-checkout__alert lp-checkout__alert--danger" role="alert">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($status_message !== ''): ?>
                <div class="lp-checkout__alert lp-checkout__alert--info" role="status">
                    <p><?= esc($status_message) ?></p>
                </div>
            <?php endif; ?>

            <?= form_open('saas/checkout', ['class' => 'lp-checkout__form']) ?>
                <div class="lp-checkout__field">
                    <label class="lp-field-label" for="tenant_code">Company code <span class="lp-field-required">*</span></label>
                    <input
                        class="lp-field-input"
                        id="tenant_code"
                        name="tenant_code"
                        value="<?= esc($tenant_code) ?>"
                        placeholder="e.g. somphors-store"
                        autocomplete="organization"
                        required
                        autofocus
                    >
                </div>
                <div class="lp-checkout__field">
                    <label class="lp-field-label" for="owner_email">Email used at register <span class="lp-field-required">*</span></label>
                    <input
                        class="lp-field-input"
                        type="email"
                        id="owner_email"
                        name="owner_email"
                        value="<?= esc($owner_email) ?>"
                        placeholder="owner@email.com"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="lp-checkout__actions">
                    <button class="lp-btn lp-btn--primary lp-btn--lg lp-checkout__submit" type="submit">Show payment QR</button>
                    <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back to home</a>
                </div>
            <?= form_close() ?>

            <p class="lp-checkout__footnote">
                Already paid? Wait a moment, then
                <a href="<?= site_url('login') ?>">log in to POS</a>.
            </p>
        </div>
    </div>
</main>
</body>
</html>
