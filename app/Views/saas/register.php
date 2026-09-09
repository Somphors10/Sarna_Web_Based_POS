<?php
/**
 * @var array $config
 * @var array|null $plan
 * @var bool $has_errors
 * @var $validation
 */
$plans = $plans ?? [];
$monthly_price = saas_monthly_price((float) ($plan['price_monthly'] ?? 0));
$plan_id = (int)($plan['plan_id'] ?? 0);
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$field_errors = ($has_errors ?? false) ? $validation->getErrors() : [];
$field_invalid_class = static function (string $name) use ($field_errors): string {
    return isset($field_errors[$name]) ? ' is-invalid' : '';
};
$show_register_aside = false; // set true to show “What happens next” again
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Start Subscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=38">
    <link rel="stylesheet" href="css/password-toggle.css?v=4">
</head>
<body class="saas-modern saas-landing-body">

<header class="lp-nav lp-nav--scrolled lp-nav--simple" id="lp-nav">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--outline" href="<?= site_url() ?>">
                <span class="lp-nav__label-full">Back to home</span>
                <span class="lp-nav__label-short">Home</span>
            </a>
        </div>
    </div>
</header>

<main class="lp-reg lp-reg--compact">
    <div class="lp-reg__header saas-shell">
        <p class="lp-label">POS Subscription</p>
        <h1 class="lp-reg__title">Register your POS</h1>
        <p class="lp-reg__subtitle">$<?= number_format($monthly_price, 0) ?>/month · all features. You pay after Super Admin activates — not on this form.</p>
        <ol class="lp-reg__flow" aria-label="Registration steps">
            <li class="is-now"><strong>1</strong><span>Register</span></li>
            <li><strong>2</strong><span>Verify</span></li>
            <li><strong>3</strong><span>Activate</span></li>
            <li><strong>4</strong><span>Pay KHQR</span></li>
            <li><strong>5</strong><span>Log in</span></li>
        </ol>
    </div>

    <div class="lp-reg__layout saas-shell<?= empty($show_register_aside) ? ' lp-reg__layout--form-only' : '' ?>">
        <div class="lp-reg__form-wrap">
            <?php if ($has_errors): ?>
                <div class="lp-reg__alert">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= form_open('saas/register', ['class' => 'lp-reg__form', 'novalidate' => 'novalidate']) ?>
            <input type="hidden" name="plan_id" value="<?= esc($plan_id) ?>" data-price="<?= (int)$monthly_price ?>">

            <section class="lp-reg__section lp-reg__section--plan">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">1</span>
                    <div>
                        <h2>Your plan</h2>
                    </div>
                </div>
                <div class="lp-plan-picker">
                    <div class="lp-plan-option lp-plan-option--card is-selected">
                        <span>
                            <strong><?= esc((string)($plan['plan_name'] ?? 'WBPOS')) ?></strong>
                            <em>All POS features</em>
                            <b>$<?= number_format($monthly_price, 0) ?><small>/month</small></b>
                        </span>
                    </div>
                </div>
            </section>

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">2</span>
                    <div>
                        <h2>Business information</h2>
                        <p>Store details used on receipts and your POS account</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="lp-field-label" for="company_name">Company name <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('company_name') ?>" id="company_name" name="company_name" value="<?= set_value('company_name') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="tenant_code">Company code <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('tenant_code') ?>" id="tenant_code" name="tenant_code" placeholder="mystore" value="<?= set_value('tenant_code') ?>" maxlength="50" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="business_type">Business type <span class="lp-field-required">*</span></label>
                        <div class="lp-search-select" data-lp-search-select>
                            <select class="lp-field-input lp-search-select__native<?= $field_invalid_class('business_type') ?>" id="business_type" name="business_type" required aria-required="true">
                                <option value="">Select type</option>
                                <?php foreach (($business_types ?? []) as $type_id => $type_label): ?>
                                    <option value="<?= esc($type_id) ?>" <?= set_select('business_type', $type_id) ?>><?= esc($type_label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="tax_id">VAT TIN <span class="lp-field-optional">optional</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('tax_id') ?>" id="tax_id" name="tax_id" placeholder="e.g. K001-901234567" value="<?= set_value('tax_id') ?>" maxlength="64">
                        <span class="lp-field-hint">Cambodia VAT number. Leave blank if you do not have one yet. You can add it later in POS settings. Printed on receipts if filled.</span>
                    </div>
                    <div class="col-12">
                        <label class="lp-field-label" for="address">House No. / Street <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('address') ?>" id="address" name="address" placeholder="e.g. #12, St. 271, Village, Sangkat" value="<?= set_value('address') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="city">Province / City <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('city') ?>" id="city" name="city" placeholder="e.g. Phnom Penh" value="<?= set_value('city') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="country">Country <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('country') ?>" id="country" name="country" placeholder="e.g. Cambodia" value="<?= set_value('country', 'Cambodia') ?>" required aria-required="true" maxlength="80">
                    </div>
                </div>
            </section>

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">3</span>
                    <div>
                        <h2>Owner account</h2>
                        <p>Your login credentials for the POS</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_last_name">Last name <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_last_name') ?>" id="owner_last_name" name="owner_last_name" value="<?= set_value('owner_last_name') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_first_name">First name <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_first_name') ?>" id="owner_first_name" name="owner_first_name" value="<?= set_value('owner_first_name') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_email">Email <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_email') ?>" type="email" id="owner_email" name="owner_email" value="<?= set_value('owner_email') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_phone">Phone (+855) <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_phone') ?>" type="tel" id="owner_phone" name="owner_phone" placeholder="12 345 678" value="<?= set_value('owner_phone') ?>" required aria-required="true" minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_username">POS username <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_username') ?>" id="owner_username" name="owner_username" value="<?= set_value('owner_username') ?>" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label class="lp-field-label" for="owner_password">POS password <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('owner_password') ?>" type="password" id="owner_password" name="owner_password" minlength="8" required aria-required="true">
                        <span class="lp-field-hint"><?= lang('Common.password_strong_hint') ?></span>
                    </div>
                </div>
            </section>

            <section class="lp-reg__section">
                <div class="lp-reg__section-head">
                    <span class="lp-reg__step">4</span>
                    <div>
                        <h2>Security check</h2>
                        <p>Enter the code you see in the picture</p>
                    </div>
                </div>
                <div class="lp-captcha<?= $field_invalid_class('captcha_code') ?>">
                    <img class="lp-captcha__img" id="saas-captcha-img" src="<?= site_url('saas/captcha') ?>?v=<?= time() ?>" alt="Verification code picture" width="188" height="58">
                    <button class="lp-captcha__refresh" type="button" id="saas-captcha-refresh">New picture</button>
                    <div class="lp-captcha__field">
                        <label class="lp-field-label" for="captcha_code">Code from the picture <span class="lp-field-required">*</span></label>
                        <input class="lp-field-input<?= $field_invalid_class('captcha_code') ?>" id="captcha_code" name="captcha_code" autocomplete="off" autocapitalize="characters" spellcheck="false" maxlength="8" required aria-required="true">
                    </div>
                </div>
            </section>

            <div class="lp-reg__actions">
                <button class="lp-btn lp-btn--primary lp-btn--lg" type="submit">Submit registration</button>
                <a class="lp-btn lp-btn--outline lp-btn--lg" href="<?= site_url() ?>">Cancel</a>
            </div>
            <?= form_close() ?>
        </div>

        <?php if (!empty($show_register_aside)): ?>
        <aside class="lp-reg__aside">
            <div class="lp-reg__next">
                <p class="lp-reg__next-badge">No payment yet</p>
                <h3>What happens next</h3>
                <ol class="lp-reg__next-list">
                    <li>We email you a verification link. Click it first.</li>
                    <li>Super Admin activates your shop.</li>
                    <li>You receive the KHQR by email. Scan it in ABA, then you can log in.</li>
                </ol>
            </div>

            <ul class="lp-reg__checklist">
                <li>Sales &amp; inventory management</li>
                <li>Staff roles &amp; permissions</li>
                <li>Reports &amp; dashboard</li>
                <li>Cloud access — any device</li>
            </ul>
        </aside>
        <?php endif; ?>
    </div>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <a class="lp-brand lp-brand--sm" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
        <p class="lp-footer__tagline">Cloud POS · $<span data-plan-price><?= number_format($monthly_price, 0) ?></span>/month</p>
    </div>
</footer>

<script src="<?= base_url('js/password_toggle.js?v=1') ?>"></script>
<script>
    window.WBPOS_STRONG_PASSWORD_MESSAGE = <?= json_encode(lang('Common.password_strong'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
<?php if (!empty($field_errors)): ?>
<script>
    window.saasRegisterFieldErrors = <?= json_encode($field_errors, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
<?php endif; ?>
<script src="<?= base_url('js/password_strength.js?v=4') ?>"></script>
<script src="<?= base_url('js/saas_register.js?v=10') ?>"></script>

</body>
</html>
