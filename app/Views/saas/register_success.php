<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$owner_email = (string)($owner_email ?? '');
$needs_verify = !empty($needs_verify);
$email_sent = !empty($email_sent);
$mail_error = trim((string)($mail_error ?? ''));
$resent = !empty($resent);
$delivery_mode = \App\Libraries\PlatformMail::deliveryInfo()['mode'] ?? '';
$mailhog = $delivery_mode === 'mailhog' && \App\Libraries\PlatformMail::isLocalDevHost();
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Check your email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=33">
</head>
<body class="saas-modern saas-landing-body saas-success-page">

<header class="lp-nav lp-nav--scrolled lp-nav--simple" id="lp-nav">
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
                        <path d="M4 6h16v12H4V6z" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>

            <?php if (!$email_sent): ?>
                <p class="lp-label"><?= $resent ? 'Email not delivered' : 'Registration received' ?></p>
                <h1 class="lp-success__title">Verify your email</h1>
                <p class="lp-success__lead">
                    Gmail did not receive a message yet.
                    Super Admin must save a <strong>Gmail App Password</strong> once on Email settings.
                    After that, click Resend below — the owner gets the Verify link automatically.
                </p>
                <?php if ($mail_error !== ''): ?>
                    <p class="lp-success__lead"><?= esc($mail_error) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="lp-label"><?= $resent ? 'Email sent again' : 'Registration received' ?></p>
                <h1 class="lp-success__title">Verify your email</h1>
                <p class="lp-success__lead">
                    <?php if ($owner_email !== ''): ?>
                        We sent a verification link to <strong><?= esc($owner_email) ?></strong>.
                        Open Gmail (and Spam) and click Verify. Super Admin sees your shop only after you click.
                    <?php else: ?>
                        Check your inbox and click the verification link.
                    <?php endif; ?>
                </p>
                <?php if ($mailhog): ?>
                    <p class="lp-success__lead">On this computer, open <a href="http://localhost:8025" target="_blank" rel="noopener">localhost:8025</a> to see the message.</p>
                <?php else: ?>
                    <p class="lp-success__lead">If you do not see it, check Spam and Promotions, then use Resend below.</p>
                <?php endif; ?>
            <?php endif; ?>

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
                        <p>Your store details were received. No payment was taken.</p>
                    </div>
                </div>

                <div class="lp-success__step lp-success__step--active" role="listitem">
                    <span class="lp-success__step-dot">
                        <span class="lp-success__step-pulse"></span>
                    </span>
                    <div class="lp-success__step-body">
                        <span class="lp-success__step-label">Step 2</span>
                        <strong>Verify email</strong>
                        <p>Open the Gmail message and click Verify. Super Admin sees your request after that.</p>
                    </div>
                </div>

                <div class="lp-success__step lp-success__step--upcoming" role="listitem">
                    <span class="lp-success__step-dot">3</span>
                    <div class="lp-success__step-body">
                        <span class="lp-success__step-label">Step 3</span>
                        <strong>Activate, pay, log in</strong>
                        <p>Super Admin activates, then emails KHQR. After they confirm your ABA payment, you can log in.</p>
                    </div>
                </div>
            </div>

            <?php if ($needs_verify && $owner_email !== ''): ?>
                <?= form_open('saas/resend-verify', ['class' => 'lp-success__actions']) ?>
                <input type="hidden" name="owner_email" value="<?= esc($owner_email, 'attr') ?>">
                <button class="lp-btn lp-btn--outline lp-btn--lg" type="submit">Resend verification email</button>
                <?= form_close() ?>
            <?php endif; ?>

            <div class="lp-success__actions">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url() ?>">Back to website</a>
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
        <p class="lp-footer__tagline">Cloud POS · Verify your email</p>
    </div>
</footer>

</body>
</html>
