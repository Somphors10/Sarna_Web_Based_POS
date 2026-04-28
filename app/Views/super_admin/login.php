<?php
/**
 * @var array $config
 * @var bool $has_errors
 * @var $validation
 */
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc($config['company']) ?> | Super Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
</head>
<body class="saas-modern">
<main class="container saas-shell saas-auth-shell">
    <section class="saas-card login-card saas-login-card">
        <div class="card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="saas-login-accent">S</div>
                <div>
                    <h4 class="mb-1">Platform Admin Login</h4>
                    <p class="saas-subtitle mb-0">Sign in to manage tenants and subscriptions</p>
                </div>
            </div>
        </div>
        <div class="card-body saas-login-content">
            <?php if ($has_errors): ?>
                <?php foreach ($validation->getErrors() as $error): ?>
                    <div class="alert alert-danger"><?= esc($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?= form_open('super-admin/login') ?>
            <div class="mb-4">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-3">
                <button class="btn btn-primary" type="submit">Login to Dashboard</button>
            </div>
            <?= form_close() ?>
            <p class="saas-subtitle text-center mt-4 mb-0">Secure access for platform-level administration</p>
        </div>
    </section>
</main>
</body>
</html>
