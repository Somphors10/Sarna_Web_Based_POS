<?php
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$ok = !empty($ok);
$already = !empty($already);
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | <?= $ok ? 'Email verified' : 'Verification failed' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=33">
</head>
<body class="saas-modern saas-landing-body saas-success-page">
<header class="lp-nav lp-nav--scrolled lp-nav--simple">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
        </div>
    </div>
</header>
<main class="lp-success">
    <div class="lp-success__shell saas-shell">
        <div class="lp-success__card">
            <p class="lp-label"><?= $ok ? 'Email confirmed' : 'Link invalid' ?></p>
            <h1 class="lp-success__title"><?= $ok ? ($already ? 'Already verified' : 'Email verified — wait for Super Admin') : 'Could not verify' ?></h1>
            <p class="lp-success__lead">
                <?php if ($ok): ?>
                    Super Admin can now review your application. After they approve, you will receive the KHQR by email. You can log in only after payment.
                <?php else: ?>
                    This verification link is invalid or already used. Register again, or resend the email from the success page.
                <?php endif; ?>
            </p>
            <div class="lp-success__actions">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url() ?>">Back to website</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
