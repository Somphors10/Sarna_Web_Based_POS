<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc(lang('Common.software_title')) ?> | Reset Request Sent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=15') ?>">
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
            <h1 class="login-card__title">Request submitted</h1>
            <p class="login-card__intro">
                If your company code and username match an active account, your password reset is now pending platform admin approval.
            </p>

            <div class="login-steps">
                <div class="login-steps__item login-steps__item--done">
                    <span>1</span>
                    <div>
                        <strong>Request sent</strong>
                        <p>Your new password is waiting for review.</p>
                    </div>
                </div>
                <div class="login-steps__item login-steps__item--active">
                    <span>2</span>
                    <div>
                        <strong>Admin review</strong>
                        <p>Platform admin approves the reset in Super Admin.</p>
                    </div>
                </div>
                <div class="login-steps__item">
                    <span>3</span>
                    <div>
                        <strong>Sign in</strong>
                        <p>Use your username and the new password you submitted.</p>
                    </div>
                </div>
            </div>

            <a class="login-submit login-submit--link" href="<?= site_url('login') ?>">Back to sign in</a>
        </div>
    </main>
</body>
</html>
