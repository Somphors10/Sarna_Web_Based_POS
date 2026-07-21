<?php

/**
 * @var bool $has_errors
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc(lang('Common.software_title')) ?> | Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=15') ?>">
    <link rel="stylesheet" href="<?= base_url('css/password-toggle.css?v=1') ?>">
    <meta name="theme-color" content="#7c3aed">
</head>
<body class="login-page">

    <?= view('login/partial_pos_bg') ?>

    <main class="login-main login-main--form">
        <div class="login-card login-card--wide">
            <h1 class="login-card__title">Forgot password</h1>
            <p class="login-card__intro">
                Submit a password reset request. Your platform admin will review it and activate the new password.
            </p>

            <?= form_open('login/forgot-password', ['id' => 'forgot-password-form']) ?>

            <?php if ($has_errors): ?>
                <?php foreach (($validation?->getErrors() ?? []) as $error): ?>
                    <div class="login-alert login-alert--danger"><?= esc($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="login-field">
                <label class="login-field__label" for="input-tenant-code">
                    Company code<span class="login-field__required">*</span>
                </label>
                <input
                    class="login-field__input login-field__input--text"
                    id="input-tenant-code"
                    name="tenant_code"
                    type="text"
                    autocomplete="organization"
                    placeholder="my-store"
                    required
                >
                <p class="login-field__hint">The code you chose when registering your business.</p>
            </div>

            <div class="login-field">
                <label class="login-field__label" for="input-username">
                    Username<span class="login-field__required">*</span>
                </label>
                <input
                    class="login-field__input login-field__input--text"
                    id="input-username"
                    name="username"
                    type="text"
                    autocomplete="username"
                    required
                >
            </div>

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

            <button class="login-submit" type="submit">Submit reset request</button>
            <?= form_close() ?>

            <p class="login-card__footer">
                <a class="login-card__link" href="<?= site_url('login') ?>">Return to sign in</a>
            </p>
        </div>
    </main>
    <script src="<?= base_url('js/password_toggle.js?v=1') ?>"></script>
</body>
</html>
