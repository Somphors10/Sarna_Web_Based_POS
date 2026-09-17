<?php

/**
 * @var bool $has_errors
 * @var bool $invalid_token
 * @var string $token
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 * @var string $support_email
 */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc(lang('Common.software_title')) ?> | Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('images/favicon.svg?v=4') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png?v=4') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png?v=4') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=18') ?>">
    <link rel="stylesheet" href="<?= base_url('css/password-toggle.css?v=1') ?>">
    <meta name="theme-color" content="#7c3aed">
</head>
<body class="login-page">

    <?= view('login/partial_pos_bg') ?>

    <main class="login-main login-main--form">
        <div class="login-card login-card--wide">
            <?php if ($invalid_token): ?>
                <h1 class="login-card__title">Link expired</h1>
                <p class="login-card__intro">
                    This password reset link is invalid, already used, or expired after 1 hour.
                    Please request a new link.
                </p>
                <a class="login-submit login-submit--link" href="<?= site_url('login/forgot-password') ?>">Request new link</a>
            <?php else: ?>
                <h1 class="login-card__title">Choose a new password</h1>
                <p class="login-card__intro">
                    Enter a strong password for your shop account. This link works only once.
                </p>

                <?= form_open('login/reset-password/' . esc($token, 'url'), ['id' => 'reset-password-form']) ?>

                <?php if ($has_errors): ?>
                    <?php foreach (($validation?->getErrors() ?? []) as $error): ?>
                        <div class="login-alert login-alert--danger"><?= esc($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="login-field">
                    <label class="login-field__label" for="input-password">
                        New password<span class="login-field__required">*</span>
                    </label>
                    <input
                        class="login-field__input login-field__input--text"
                        id="input-password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <p class="login-field__hint"><?= lang('Common.password_strong_hint') ?></p>
                </div>

                <div class="login-field">
                    <label class="login-field__label" for="input-password-confirm">
                        Confirm new password<span class="login-field__required">*</span>
                    </label>
                    <input
                        class="login-field__input login-field__input--text"
                        id="input-password-confirm"
                        name="password_confirm"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button class="login-submit" type="submit">Update password</button>
                <?= form_close() ?>
            <?php endif; ?>

            <p class="login-card__footer">
                <a class="login-card__link" href="<?= site_url('login') ?>">Return to sign in</a>
            </p>
        </div>
    </main>
    <script src="<?= base_url('js/password_toggle.js?v=1') ?>"></script>
</body>
</html>
