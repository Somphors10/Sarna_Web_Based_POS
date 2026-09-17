<?php

/**
 * @var bool $has_errors
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 * @var string $support_email
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
    <link rel="icon" type="image/svg+xml" href="<?= base_url('images/favicon.svg?v=4') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png?v=4') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png?v=4') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=18') ?>">
    <meta name="theme-color" content="#7c3aed">
</head>
<body class="login-page">

    <?= view('login/partial_pos_bg') ?>

    <main class="login-main login-main--form">
        <div class="login-card login-card--wide">
            <h1 class="login-card__title">Forgot password</h1>
            <p class="login-card__intro">
                Enter the email you used when you registered your shop. We will send you a secure link to choose a new password.
            </p>

            <?= form_open('login/forgot-password', ['id' => 'forgot-password-form']) ?>

            <?php if ($has_errors): ?>
                <?php foreach (($validation?->getErrors() ?? []) as $error): ?>
                    <div class="login-alert login-alert--danger"><?= esc($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="login-field">
                <label class="login-field__label" for="input-email">
                    Email<span class="login-field__required">*</span>
                </label>
                <input
                    class="login-field__input login-field__input--text"
                    id="input-email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    required
                >
                <p class="login-field__hint">Use the same email where you received WBPOS verify or payment emails.</p>
            </div>

            <button class="login-submit" type="submit">Email reset link</button>
            <?= form_close() ?>

            <p class="login-card__footer">
                Need help? <a class="login-card__link" href="mailto:<?= esc($support_email, 'attr') ?>"><?= esc($support_email) ?></a><br>
                <a class="login-card__link" href="<?= site_url('login') ?>">Return to sign in</a>
            </p>
        </div>
    </main>
</body>
</html>

