<?php
/**
 * @var bool $has_errors
 * @var bool $is_latest
 * @var string $latest_version
 * @var bool $gcaptcha_enabled
 * @var array $config
 * @var \CodeIgniter\Validation\ValidationInterface|null $validation
 */
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc($config['company']) . ' | Login' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css?v=9') ?>">
    <meta name="theme-color" content="#4f46e5">
</head>

<body class="login-page">
    <header class="login-topbar">
        <a class="login-topbar__brand" href="<?= site_url() ?>"><?= esc($config['company']) ?></a>
        <a class="login-topbar__link" href="<?= site_url() ?>">View our services</a>
    </header>

    <div class="login-pos-bg" aria-hidden="true">
        <div class="login-pos-receipt">
            <p class="login-pos-receipt__title">Receipt</p>
            <div class="login-pos-receipt__line"></div>
            <div class="login-pos-receipt__line login-pos-receipt__line--short"></div>
            <div class="login-pos-receipt__line"></div>
            <div class="login-pos-receipt__dash"></div>
            <div class="login-pos-receipt__total">
                <span>Total</span>
                <span>$24.50</span>
            </div>
        </div>

        <div class="login-pos-barcode">
            <?php for ($i = 0; $i < 18; $i++): ?>
                <span></span>
            <?php endfor; ?>
        </div>

        <div class="login-pos-tile">
            <div class="login-pos-tile__icon">$</div>
            <p class="login-pos-tile__name">Sale Item</p>
            <p class="login-pos-tile__price">$12.00</p>
        </div>

        <div class="login-pos-total">
            <p class="login-pos-total__label">Register Total</p>
            <p class="login-pos-total__amount">$0.00</p>
        </div>
    </div>

    <main class="login-main">
        <div class="login-card">
            <h1 class="login-card__title">Sign In</h1>

            <?= form_open('login', ['id' => 'login-form']) ?>

            <?php if ($has_errors): ?>
                <?php foreach (($validation?->getErrors() ?? []) as $error): ?>
                    <div class="login-alert login-alert--danger">
                        <?= esc($error) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$is_latest): ?>
                <div class="login-alert login-alert--info">
                    A database migration to <?= esc($latest_version) ?> will start after login.
                </div>
            <?php endif; ?>

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
                    <?php if (ENVIRONMENT == "testing") echo 'value="admin"'; ?>
                >
            </div>

            <div class="login-field">
                <label class="login-field__label" for="input-password">
                    Password<span class="login-field__required">*</span>
                </label>
                <div class="login-field__input-wrap">
                    <input
                        class="login-field__input"
                        id="input-password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        <?php if (ENVIRONMENT == "testing") echo 'value="pointofsale"'; ?>
                    >
                    <button class="login-field__toggle" type="button" id="toggle-password" aria-label="Show password">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="icon-eye-off is-hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-6.94"></path>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <label class="login-remember" for="remember-me">
                <input type="checkbox" id="remember-me" name="remember_me" value="1">
                <span>Remember me</span>
            </label>

            <?php
            if ($gcaptcha_enabled) {
                echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
                echo '<div class="g-recaptcha" data-sitekey="' . esc($config['gcaptcha_site_key']) . '"></div>';
            }
            ?>

            <button class="login-submit" name="login-button" type="submit">Login</button>
            <?= form_close() ?>

            <p class="login-card__footer">
                <a class="login-card__link" href="<?= site_url() ?>">View our services</a>
            </p>
        </div>
    </main>

    <script>
        (function () {
            const usernameInput = document.getElementById('input-username');
            const passwordInput = document.getElementById('input-password');
            const rememberCheckbox = document.getElementById('remember-me');
            const toggleButton = document.getElementById('toggle-password');
            const loginForm = document.getElementById('login-form');
            const storageKey = 'ospos_login_username';

            if (localStorage.getItem(storageKey) && usernameInput) {
                usernameInput.value = localStorage.getItem(storageKey);
                if (rememberCheckbox) {
                    rememberCheckbox.checked = true;
                }
            }

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function () {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    toggleButton.querySelector('.icon-eye').classList.toggle('is-hidden', !isHidden);
                    toggleButton.querySelector('.icon-eye-off').classList.toggle('is-hidden', isHidden);
                });
            }

            if (loginForm && usernameInput && rememberCheckbox) {
                loginForm.addEventListener('submit', function () {
                    if (rememberCheckbox.checked) {
                        localStorage.setItem(storageKey, usernameInput.value);
                    } else {
                        localStorage.removeItem(storageKey);
                    }
                });
            }
        })();
    </script>
</body>

</html>
