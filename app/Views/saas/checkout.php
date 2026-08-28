<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$status_message = (string)($status_message ?? '');
$tenant_code = (string)($tenant_code ?? '');
$owner_email = (string)($owner_email ?? '');
$has_errors = !empty($has_errors);
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
            <a class="lp-btn lp-btn--outline" href="<?= site_url('saas/register') ?>">Register</a>
        </div>
    </div>
</header>

<main class="lp-reg">
    <div class="lp-reg__header saas-shell">
        <p class="lp-label">Subscription</p>
        <h1 class="lp-reg__title">Complete payment</h1>
        <p class="lp-reg__subtitle">After Super Admin activates your shop, enter your company code and email. Then you will see the KHQR to pay.</p>
    </div>

    <div class="lp-reg__layout saas-shell" style="grid-template-columns: minmax(0, 560px); justify-content: center;">
        <div class="lp-reg__form-wrap">
            <?php if ($has_errors): ?>
                <div class="lp-reg__alert">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($status_message !== ''): ?>
                <div class="lp-reg__alert" style="background:#eff6ff;border-color:#bfdbfe;">
                    <p style="color:#1d4ed8;"><?= esc($status_message) ?></p>
                </div>
            <?php endif; ?>

            <?= form_open('saas/checkout', ['class' => 'lp-reg__form']) ?>
            <section class="lp-reg__section">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="lp-field-label" for="tenant_code">Company code <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input" id="tenant_code" name="tenant_code" value="<?= esc($tenant_code) ?>" placeholder="the code you registered" required>
                    </div>
                    <div class="col-12">
                        <label class="lp-field-label" for="owner_email">Email used at register <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input" type="email" id="owner_email" name="owner_email" value="<?= esc($owner_email) ?>" required>
                    </div>
                </div>
            </section>
            <div class="lp-reg__actions">
                <button class="lp-btn lp-btn--primary lp-btn--lg" type="submit">Show payment QR</button>
                <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Back</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</main>
</body>
</html>
