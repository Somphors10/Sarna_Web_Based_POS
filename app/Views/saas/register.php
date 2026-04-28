<?php
/**
 * @var array $config
 * @var array $plans
 * @var bool $has_errors
 * @var $validation
 */
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc($config['company']) ?> | Register Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
</head>
<body class="saas-modern">
<main class="container py-4 saas-shell">
    <section class="saas-card">
        <div class="saas-register-layout">
            <div class="saas-register-main">
                <h1 class="saas-register-title">Start Your POS Subscription</h1>
                <p class="saas-subtitle mb-4">Create your business account and submit payment reference for fast activation.</p>

            <?php if ($has_errors): ?>
                <?php foreach ($validation->getErrors() as $error): ?>
                    <div class="alert alert-danger"><?= esc($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?= form_open('saas/register') ?>
            <p class="saas-section-title">Business Information</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Company Name</label>
                    <input class="form-control" name="company_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company Code</label>
                    <input class="form-control" name="tenant_code" required>
                </div>
            </div>

            <p class="saas-section-title mt-4">Owner Account</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Owner First Name</label>
                    <input class="form-control" name="owner_first_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Last Name</label>
                    <input class="form-control" name="owner_last_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Email</label>
                    <input type="email" class="form-control" name="owner_email" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Phone</label>
                    <input class="form-control" name="owner_phone">
                </div>
                <div class="col-md-6">
                    <label class="form-label">POS Username</label>
                    <input class="form-control" name="owner_username" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">POS Password</label>
                    <input type="password" class="form-control" name="owner_password" required>
                </div>
            </div>

            <p class="saas-section-title mt-4">Subscription and Payment</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Plan</label>
                    <select class="form-select" name="plan_id" required>
                        <?php foreach ($plans as $plan): ?>
                            <option value="<?= esc($plan['plan_id']) ?>">
                                <?= esc($plan['plan_name']) ?> - $<?= esc(number_format((float)$plan['price_monthly'], 2)) ?>/month
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Reference</label>
                    <input class="form-control" name="payment_reference" placeholder="Transaction/Receipt ID" required>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit">Submit and Buy</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('saas') ?>">Back</a>
            </div>
            <?= form_close() ?>
            </div>

            <aside class="saas-register-side">
                <h5 class="mb-3">What happens next?</h5>
                <div class="saas-kpi">
                    <p class="saas-kpi-label">Step 1</p>
                    <p class="saas-kpi-value">Submit registration</p>
                </div>
                <div class="saas-kpi">
                    <p class="saas-kpi-label">Step 2</p>
                    <p class="saas-kpi-value">Admin verifies payment</p>
                </div>
                <div class="saas-kpi">
                    <p class="saas-kpi-label">Step 3</p>
                    <p class="saas-kpi-value">POS account activated</p>
                </div>
                <p class="small text-muted mb-0 mt-3">
                    Use a clear payment reference so activation can be processed quickly.
                </p>
            </aside>
        </div>
    </section>
</main>
</body>
</html>
