<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>Registration Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
</head>
<body class="saas-modern">
<main class="container py-5 saas-shell d-flex align-items-center justify-content-center" style="min-height: 72vh;">
    <div class="saas-card" style="max-width: 760px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="saas-login-accent">✓</div>
                <div>
                    <h3 class="mb-1">Registration Submitted</h3>
                    <p class="saas-subtitle mb-0">Thank you. Your request is in review queue.</p>
                </div>
            </div>

            <div class="alert alert-info mb-3">
                Your payment and business registration are now pending platform admin approval.
                Once approved, you can login to POS using your registered username and password.
            </div>

            <div class="row g-2 mb-4">
                <div class="col-md-4">
                    <div class="saas-kpi mb-0">
                        <p class="saas-kpi-label">Step 1</p>
                        <p class="saas-kpi-value">Submitted</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="saas-kpi mb-0">
                        <p class="saas-kpi-label">Step 2</p>
                        <p class="saas-kpi-value">Review Pending</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="saas-kpi mb-0">
                        <p class="saas-kpi-label">Step 3</p>
                        <p class="saas-kpi-value">POS Activation</p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="<?= site_url('login') ?>">Go to POS Login</a>
                <a class="btn btn-outline-secondary" href="<?= site_url('saas') ?>">Back to Website</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
