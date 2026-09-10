<?php
/**
 * @var array $config
 * @var array|null $plan
 */
$plans = $plans ?? [];
$monthly_price = saas_monthly_price((float) ($plan['price_monthly'] ?? 0));
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
$contact_phone_display = '012 981 696';
$contact_email = 'support@wbpos.com';
$contact_address = 'Street 299, Sangkat Boeung Kak II, Khan Toul Kork, Phnom Penh';
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Cloud POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Modern cloud POS for sales, inventory, and team management. $<?= number_format($monthly_price, 0) ?>/month.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=37">
</head>
<body class="saas-modern saas-landing-body">

<header class="lp-nav" id="lp-nav">
    <div class="lp-nav__inner saas-shell">
        <a class="lp-brand" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>

        <nav class="lp-nav__links" id="lp-nav-links" aria-label="Main">
            <a href="#features">Features</a>
            <a href="#how-it-works">How it works</a>
            <a href="#pricing">Pricing</a>
            <a href="#contact">Contact</a>
            <a href="<?= site_url('saas/checkout') ?>">Complete payment</a>
            <a class="lp-nav__mobile-only" href="<?= site_url('login') ?>">Log in</a>
        </nav>

        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--primary" href="<?= site_url('saas/register') ?>">Apply for WBPOS</a>
            <button class="lp-nav__toggle" id="lp-nav-toggle" type="button" aria-label="Open menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main>
    <section class="lp-hero saas-shell">
        <div class="lp-hero__content">
            <div class="lp-pill">
                <span class="lp-pill__dot"></span>
                Cloud POS · $<?= number_format($monthly_price, 0) ?>/month
            </div>
            <h1 class="lp-hero__title">
                Run your store smarter with a <span class="lp-gradient-text">modern POS</span> built for today.
            </h1>
            <p class="lp-hero__text">
                Sales, inventory, customers, and reports — all in one clean dashboard.
                Cloud-based with no install. Use it from any browser once your shop is active.
            </p>
            <div class="lp-hero__cta">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('saas/register') ?>">
                    Register your store
                </a>
                <a class="lp-btn lp-btn--outline lp-btn--lg" href="#pricing">View pricing</a>
            </div>
            <ul class="lp-hero__trust">
                <li>Fast checkout</li>
                <li>Real-time stock</li>
                <li>Staff roles</li>
                <li>Works on any device</li>
            </ul>
        </div>

        <div class="lp-hero__visual" aria-hidden="true">
            <div class="lp-mockup">
                <div class="lp-mockup__bar">
                    <span></span><span></span><span></span>
                    <em>Dashboard</em>
                </div>
                <div class="lp-mockup__body">
                    <div class="lp-mockup__stat">
                        <small>Today's Sales</small>
                        <strong>$1,248</strong>
                        <span class="lp-mockup__up">+12%</span>
                    </div>
                    <div class="lp-mockup__stat">
                        <small>Transactions</small>
                        <strong>86</strong>
                    </div>
                    <div class="lp-mockup__stat">
                        <small>Low Stock</small>
                        <strong>4 items</strong>
                    </div>
                    <div class="lp-mockup__chart">
                        <div style="height:42%"></div>
                        <div style="height:68%"></div>
                        <div style="height:55%"></div>
                        <div style="height:82%"></div>
                        <div style="height:61%"></div>
                        <div style="height:90%"></div>
                        <div style="height:74%"></div>
                    </div>
                </div>
            </div>
            <div class="lp-float-card lp-float-card--1">
                <strong>Sale complete</strong>
                <span>Sale #1082 · $24.50</span>
            </div>
            <div class="lp-float-card lp-float-card--2">
                <strong>Stock updated</strong>
                <span>+12 units from purchase</span>
            </div>
        </div>
    </section>

    <section class="lp-highlights saas-shell" aria-label="Product highlights">
        <div class="lp-highlights__grid">
            <article class="lp-highlight-card">
                <span class="lp-highlight-card__icon lp-highlight-card__icon--sales" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
                <div>
                    <h3>Sales</h3>
                    <p>Quick billing & receipts</p>
                </div>
            </article>
            <article class="lp-highlight-card">
                <span class="lp-highlight-card__icon lp-highlight-card__icon--inventory" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </span>
                <div>
                    <h3>Inventory</h3>
                    <p>Live stock tracking</p>
                </div>
            </article>
            <article class="lp-highlight-card">
                <span class="lp-highlight-card__icon lp-highlight-card__icon--customers" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                </span>
                <div>
                    <h3>Customers</h3>
                    <p>Records &amp; purchase history</p>
                </div>
            </article>
            <article class="lp-highlight-card">
                <span class="lp-highlight-card__icon lp-highlight-card__icon--reports" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-6 4 3 5-8"/></svg>
                </span>
                <div>
                    <h3>Reports</h3>
                    <p>Daily performance</p>
                </div>
            </article>
        </div>
    </section>

    <section id="features" class="lp-section lp-section--features saas-shell">
        <div class="lp-section__head lp-section__head--center">
            <p class="lp-label">Features</p>
            <h2>Everything your business needs, nothing you don't.</h2>
            <p class="lp-section__sub">Powerful tools with a simple interface your team can learn in one shift.</p>
        </div>
        <div class="lp-features">
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--violet" aria-hidden="true">⚡</span>
                <h3>Smart Sales Screen</h3>
                <p>Process sales and returns, apply discounts and taxes, take multiple payments, and print receipts.</p>
            </article>
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--blue" aria-hidden="true">▦</span>
                <h3>Inventory Control</h3>
                <p>Track items and stock levels, reorder alerts, item kits, and purchase stock from suppliers.</p>
            </article>
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--emerald" aria-hidden="true">👥</span>
                <h3>Staff Permissions</h3>
                <p>Add employees with owner, manager, or cashier access and control module permissions.</p>
            </article>
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--amber" aria-hidden="true">◎</span>
                <h3>Customer Records</h3>
                <p>Store customer details, default discounts, and purchase history in one place.</p>
            </article>
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--rose" aria-hidden="true">📊</span>
                <h3>Business Reports</h3>
                <p>Sales summaries, inventory reports, payment breakdowns, and daily store performance.</p>
            </article>
            <article class="lp-feature-card">
                <span class="lp-feature-card__icon lp-feature-card__icon--indigo" aria-hidden="true">☁</span>
                <h3>Cloud Anywhere</h3>
                <p>Run your store from any browser — no install, each shop gets its own private database.</p>
            </article>
        </div>
    </section>

    <section id="how-it-works" class="lp-section lp-section--muted saas-shell">
        <div class="lp-section__head">
            <p class="lp-label">How it works</p>
            <h2>Register in five steps</h2>
            <p class="lp-section__sub">No payment on the form. You log in only after Super Admin confirms your KHQR payment.</p>
        </div>
        <div class="lp-steps lp-steps--five">
            <div class="lp-step">
                <span class="lp-step__num">01</span>
                <h3>Register</h3>
                <p>Enter your shop and owner login. Do not pay yet.</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">02</span>
                <h3>Verify email</h3>
                <p>Open the link we send. Super Admin sees you only after this.</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">03</span>
                <h3>We activate</h3>
                <p>Super Admin accepts the shop. Login is still blocked.</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">04</span>
                <h3>Pay KHQR</h3>
                <p>We email the KHQR. Scan it in ABA Bank ($20/month).</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">05</span>
                <h3>Log in</h3>
                <p>After Super Admin confirms payment, sign in with your username.</p>
            </div>
        </div>
    </section>

    <section id="pricing" class="lp-section lp-section--pricing saas-shell">
        <div class="lp-section__head lp-section__head--center">
            <p class="lp-label">Pricing</p>
            <h2>One plan. Everything included.</h2>
            <p class="lp-section__sub">$20 per month — all POS modules. Pay with KHQR after Super Admin activates your shop.</p>
        </div>
        <div class="lp-pricing lp-pricing--single">
            <?php foreach ($plans as $tier): ?>
            <article class="lp-pricing__card is-featured">
                <span class="lp-pricing__badge">All features</span>
                <h3><?= esc($tier['plan_name']) ?></h3>
                <div class="lp-pricing__price">
                    <strong>$<?= number_format(saas_monthly_price((float)$tier['price_monthly']), 0) ?></strong>
                    <span>/ month</span>
                </div>
                <ul class="lp-pricing__list">
                    <li>Sales, inventory &amp; customers</li>
                    <li>Reports, purchase &amp; gift cards</li>
                    <li>Staff, expenses, cashups &amp; suppliers</li>
                    <li>Private shop database</li>
                    <li>Pay after Super Admin activates</li>
                </ul>
                <a class="lp-pricing__cta" href="<?= esc(site_url('saas/register'), 'attr') ?>">Apply for WBPOS</a>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="contact" class="lp-section lp-section--contact saas-shell">
        <div class="lp-section__head lp-section__head--center">
            <p class="lp-label">Contact us</p>
            <h2>Questions about WBPOS?</h2>
            <p class="lp-section__sub">Reach out before you register — we are happy to help with setup, pricing, or payment.</p>
        </div>
        <div class="lp-contact">
            <div class="lp-contact__card">
                <span class="lp-contact__icon lp-contact__icon--phone" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </span>
                <span class="lp-contact__label">Phone</span>
                <strong class="lp-contact__value"><?= esc($contact_phone_display) ?></strong>
            </div>
            <div class="lp-contact__card">
                <span class="lp-contact__icon lp-contact__icon--email" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <span class="lp-contact__label">Email</span>
                <strong class="lp-contact__value"><?= esc($contact_email) ?></strong>
            </div>
            <div class="lp-contact__card lp-contact__card--location">
                <span class="lp-contact__icon lp-contact__icon--location" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <span class="lp-contact__label">Location</span>
                <strong class="lp-contact__value"><?= esc($contact_address) ?></strong>
            </div>
        </div>
    </section>
</main>

<footer class="lp-footer lp-footer--simple">
    <div class="lp-footer__inner saas-shell">
        <a class="lp-brand lp-brand--sm" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
        <p class="lp-footer__tagline">Cloud POS · $<?= number_format($monthly_price, 0) ?>/month · <a href="#contact">Contact</a></p>
    </div>
</footer>

<script>
(function () {
    const nav = document.getElementById('lp-nav');
    const toggle = document.getElementById('lp-nav-toggle');
    const links = document.getElementById('lp-nav-links');

    window.addEventListener('scroll', function () {
        nav.classList.toggle('lp-nav--scrolled', window.scrollY > 12);
    }, { passive: true });

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            const open = links.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.classList.toggle('is-open', open);
        });

        links.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                links.classList.remove('is-open');
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }
})();
</script>
</body>
</html>
