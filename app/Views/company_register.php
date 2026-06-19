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
    <title><?= esc($config['company']) ?> | SaaS Company Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
    <style>
        .register-card { max-width: 780px; margin: 30px auto; }
    </style>
</head>
<body class="saas-modern">
<main class="container saas-shell">
    <section class="saas-card register-card">
        <div class="card-header">
            <h4 class="mb-0">Create Business Account</h4>
            <p class="saas-subtitle mb-0">Platform admin creates one tenant with one owner admin for POS access.</p>
        </div>
        <div class="card-body">
            <p class="saas-section-title">Business and Owner Details</p>

            <?php if ($has_errors): ?>
                <?php foreach ($validation->getErrors() as $error): ?>
                    <div class="alert alert-danger"><?= esc($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?= form_open('register-company') ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="tenant_code">Company Code</label>
                    <input class="form-control" id="tenant_code" name="tenant_code" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input class="form-control" id="company_name" name="company_name" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="first_name">Owner First Name</label>
                    <input class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="last_name">Owner Last Name</label>
                    <input class="form-control" id="last_name" name="last_name" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="username">Owner Username</label>
                    <input class="form-control" id="username" name="username" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">Owner Password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" pattern="<?= esc(strong_password_js_pattern(), 'attr') ?>" required>
                    <div class="form-text"><?= lang('Common.password_strong_hint') ?></div>
                </div>

                <div class="col-12">
                    <label class="form-label" for="email">Owner Email (optional)</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary" type="submit">Create Company</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('super-admin') ?>">Back to Super Admin</a>
            </div>
            <?= form_close() ?>
        </div>
    </section>
</main>
</body>
</html>
