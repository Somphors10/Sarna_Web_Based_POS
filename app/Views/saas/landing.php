<?php
/**
 * @var array $config
 * @var array|null $plan
 */
$monthly_price = 10.00;
$brand_name = esc(lang('Common.software_title'));
$company = $brand_name;
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= $company ?> | Cloud POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Modern cloud POS for sales, inventory, and team management. $10/month.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css?v=4">
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
        </nav>

        <div class="lp-nav__actions">
            <a class="lp-btn lp-btn--ghost" href="<?= site_url('login') ?>">Log in</a>
            <a class="lp-btn lp-btn--primary" href="<?= site_url('saas/register') ?>">Get started</a>
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
                No install. Open your browser and start selling in minutes.
            </p>
            <div class="lp-hero__cta">
                <a class="lp-btn lp-btn--primary lp-btn--lg" href="<?= site_url('saas/register') ?>">
                    Start subscription — $<?= number_format($monthly_price, 0) ?>/mo
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
                <strong>Receipt sent</strong>
                <span>Sale #1082 · $24.50</span>
            </div>
            <div class="lp-float-card lp-float-card--2">
                <strong>Stock updated</strong>
                <span>+12 units received</span>
            </div>
        </div>
    </section>

    <section class="lp-logos saas-shell">
        <div class="lp-logos__grid">
            <div class="lp-logo-item">
                <span class="lp-logo-item__icon">$</span>
                <div><strong>Sales</strong><span>Quick billing & receipts</span></div>
            </div>
            <div class="lp-logo-item">
                <span class="lp-logo-item__icon">▦</span>
                <div><strong>Inventory</strong><span>Live stock tracking</span></div>
            </div>
            <div class="lp-logo-item">
                <span class="lp-logo-item__icon">◎</span>
                <div><strong>Customers</strong><span>History & insights</span></div>
            </div>
            <div class="lp-logo-item">
                <span class="lp-logo-item__icon">↗</span>
                <div><strong>Reports</strong><span>Daily performance</span></div>
            </div>
        </div>
    </section>

    <section id="features" class="lp-section saas-shell">
        <div class="lp-section__head">
            <p class="lp-label">Features</p>
            <h2>Everything your business needs, nothing you don't.</h2>
            <p class="lp-section__sub">Powerful tools with a simple interface your team can learn in one shift.</p>
        </div>
        <div class="lp-bento">
            <article class="lp-bento__item lp-bento__item--wide">
                <div class="lp-bento__icon">⚡</div>
                <h3>Smart Sales Screen</h3>
                <p>Fast checkout, discounts, taxes, split payments, and instant printable receipts.</p>
            </article>
            <article class="lp-bento__item">
                <div class="lp-bento__icon">▦</div>
                <h3>Inventory Control</h3>
                <p>Track stock, low-level alerts, adjustments, and receiving.</p>
            </article>
            <article class="lp-bento__item">
                <div class="lp-bento__icon">👥</div>
                <h3>Staff Permissions</h3>
                <p>Owner, manager, and cashier roles with secure access.</p>
            </article>
            <article class="lp-bento__item">
                <div class="lp-bento__icon">◎</div>
                <h3>Customer CRM</h3>
                <p>Store details, purchase history, and buying behavior.</p>
            </article>
            <article class="lp-bento__item lp-bento__item--wide">
                <div class="lp-bento__icon">📊</div>
                <h3>Business Reports</h3>
                <p>Product performance, payment breakdown, and revenue trends — updated in real time.</p>
            </article>
        </div>
    </section>

    <section id="how-it-works" class="lp-section lp-section--muted saas-shell">
        <div class="lp-section__head">
            <p class="lp-label">How it works</p>
            <h2>Up and running in three steps</h2>
        </div>
        <div class="lp-steps">
            <div class="lp-step">
                <span class="lp-step__num">01</span>
                <h3>Register your business</h3>
                <p>Create your account and fill in your store details.</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">02</span>
                <h3>Pay $<?= number_format($monthly_price, 0) ?> via QR</h3>
                <p>Scan the ABA KHQR code and submit your payment reference.</p>
            </div>
            <div class="lp-step">
                <span class="lp-step__num">03</span>
                <h3>Start selling</h3>
                <p>Log in to your dashboard and run sales with full visibility.</p>
            </div>
        </div>
    </section>

    <section id="pricing" class="lp-section saas-shell">
        <div class="lp-section__head lp-section__head--center">
            <p class="lp-label">Pricing</p>
            <h2>One plan. Full access.</h2>
            <p class="lp-section__sub">No free tier, no hidden fees — just $<?= number_format($monthly_price, 0) ?> per month.</p>
        </div>
        <div class="lp-pricing">
            <div class="lp-pricing__card">
                <span class="lp-pricing__badge">Most popular</span>
                <h3>POS Subscription</h3>
                <div class="lp-pricing__price">
                    <span class="lp-pricing__amount">$<?= number_format($monthly_price, 0) ?></span>
                    <span class="lp-pricing__period">/ month</span>
                </div>
                <ul class="lp-pricing__list">
                    <li>Sales, inventory & customer management</li>
                    <li>Staff roles & permissions</li>
                    <li>Business reports & dashboard</li>
                    <li>Cloud access from any device</li>
                    <li>Pay via ABA KHQR — scan & go</li>
                </ul>
                <a class="lp-btn lp-btn--primary lp-btn--lg lp-btn--block" href="<?= site_url('saas/register') ?>">
                    Subscribe now
                </a>
                <p class="lp-pricing__note">Already subscribed? <a href="<?= site_url('login') ?>">Log in here</a></p>
            </div>
        </div>
    </section>

    <section class="lp-cta saas-shell">
        <div class="lp-cta__inner">
            <div>
                <h2>Ready to modernize your store?</h2>
                <p>Join businesses using <?= $company ?> to sell smarter every day.</p>
            </div>
            <a class="lp-btn lp-btn--white lp-btn--lg" href="<?= site_url('saas/register') ?>">Get started — $<?= number_format($monthly_price, 0) ?>/mo</a>
        </div>
    </section>
</main>

<footer class="lp-footer">
    <div class="lp-footer__inner saas-shell">
        <a class="lp-brand lp-brand--sm" href="<?= site_url() ?>">
            <span class="lp-brand__mark">W</span>
            <span class="lp-brand__name"><?= $company ?></span>
        </a>
        <p class="lp-footer__copy">&copy; <?= date('Y') ?> <?= $company ?>. All rights reserved.</p>
        <div class="lp-footer__links">
            <a href="<?= site_url('login') ?>">Log in</a>
            <a href="<?= site_url('saas/register') ?>">Subscribe</a>
        </div>
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
