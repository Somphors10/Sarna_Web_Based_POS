<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$shop = esc((string)($company_name ?? ''));
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Payment received</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=45">
</head>
<body class="saas-modern saas-landing-body saas-success-page">

<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
        </div>
    </div>
</header>

<main class="lp-success">
    <div class="lp-success__shell saas-shell">
        <div class="lp-success__card">
            <div class="lp-success__icon-wrap">
                <span class="lp-success__icon-ring"></span>
                <span class="lp-success__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <p class="lp-label">Payment submitted</p>
            <h1 class="lp-success__title">Your shop is ready</h1>
            <p class="lp-success__lead">
                <?php if ($shop !== ''): ?>
                    <?= $shop ?> is now active. Sign in with the username and password you registered.
                <?php else: ?>
                    Your POS account is now active. Sign in with the username and password you registered.
                <?php endif; ?>
            </p>
            <div class="lp-success__actions">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('login') ?>">Go to POS login</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
