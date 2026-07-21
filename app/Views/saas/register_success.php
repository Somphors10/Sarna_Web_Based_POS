<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Registration Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=12">
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

<main class="lp-success">
    <div class="lp-success__bg" aria-hidden="true">
        <span class="lp-success__orb lp-success__orb--1"></span>
        <span class="lp-success__orb lp-success__orb--2"></span>
    </div>

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

            <p class="lp-label">Registration complete</p>
            <h1 class="lp-success__title">You&apos;re on the list</h1>
            <p class="lp-success__lead">
                Thank you for subscribing. Your business registration is in our review queue and will be activated shortly.
            </p>

            <div class="lp-success__timeline" role="list" aria-label="Registration progress">
                <div class="lp-success__step lp-success__step--done" role="listitem">
                    <span class="lp-success__step-dot">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="lp-success__step-body">
                        <span class="lp-success__step-label">Step 1</span>
                        <strong>Submitted</strong>
                        <p>Your details and payment reference were received.</p>
                    </div>
                </div>

                <div class="lp-success__step lp-success__step--active" role="listitem">
                    <span class="lp-success__step-dot">
                        <span class="lp-success__step-pulse"></span>
                    </span>
                    <div class="lp-success__step-body">
                        <span class="lp-success__step-label">Step 2</span>
                        <strong>Review pending</strong>
                        <p>Our team is verifying your registration.</p>
                    </div>
                </div>

                <div class="lp-success__step lp-success__step--upcoming" role="listitem">
                    <span class="lp-success__step-dot">3</span>
                    <div class="lp-success__step-body">
                        <span class="lp-success__step-label">Step 3</span>
                        <strong>POS activation</strong>
                        <p>Log in with your username and password.</p>
                    </div>
                </div>
            </div>

            <div class="lp-success__notice">
                <span class="lp-success__notice-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75"/>
                        <path d="M12 10v5M12 7h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <p>
                    Once approved, you can sign in to your POS dashboard using the username and password you registered with.
                    You&apos;ll receive access as soon as the platform admin completes the review.
                </p>
            </div>

            <div class="lp-success__actions">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('login') ?>">Go to POS login</a>
                <a class="lp-btn lp-btn--ghost" href="<?= site_url('saas') ?>">Back to website</a>
            </div>
        </div>
    </div>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <a class="lp-brand lp-brand--sm" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
        <p class="lp-footer__tagline">Cloud POS · Pending approval</p>
    </div>
</footer>

</body>
</html>
