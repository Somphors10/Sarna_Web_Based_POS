<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc(lang('Common.software_title')) ?> | Check Your Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('images/favicon.svg?v=5') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png?v=5') ?>">
    <link rel="icon" href="<?= base_url('favicon.ico?v=5') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png?v=5') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=18') ?>">
    <meta name="theme-color" content="#7c3aed">
</head>
<body class="login-page">

    <?= view('login/partial_pos_bg') ?>

    <main class="login-main">
        <div class="login-card login-card--success">
            <div class="login-success-icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="login-card__title">Check your email</h1>
            <p class="login-card__intro">
                If we found a matching account with an email on file, we sent a password reset link.
                The link expires in 1 hour.
            </p>

            <div class="login-steps">
                <div class="login-steps__item login-steps__item--done">
                    <span>1</span>
                    <div>
                        <strong>Request sent</strong>
                        <p>Look in your inbox (and spam folder).</p>
                    </div>
                </div>
                <div class="login-steps__item login-steps__item--active">
                    <span>2</span>
                    <div>
                        <strong>Open the email link</strong>
                        <p>Only you can use that link to set a new password.</p>
                    </div>
                </div>
                <div class="login-steps__item">
                    <span>3</span>
                    <div>
                        <strong>Sign in</strong>
                        <p>Use your username and the new password you choose.</p>
                    </div>
                </div>
            </div>

            <p class="login-card__intro">
                Did not get an email? Contact <a class="login-card__link" href="mailto:<?= esc($support_email, 'attr') ?>"><?= esc($support_email) ?></a>.
            </p>

            <a class="login-submit login-submit--link" href="<?= site_url('login') ?>">Back to sign in</a>
        </div>
    </main>
</body>
</html>
