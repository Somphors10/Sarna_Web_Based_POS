<?php
/**
 * @var array $config
 * @var array $plans
 */
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title><?= esc($config['company']) ?> | POS SaaS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
</head>
<body class="saas-modern">
<header class="saas-site-nav-wrap">
    <div class="container saas-shell">
        <nav class="saas-site-nav">
            <a class="saas-brand" href="<?= site_url('saas') ?>"><?= esc($config['company']) ?></a>
            <div class="saas-site-links">
                <a href="#features">Features</a>
                <a href="#why-us">Why Us</a>
                <a href="#pricing">Pricing</a>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('login') ?>">Business Login</a>
                <a class="btn btn-primary btn-sm" href="<?= site_url('saas/register') ?>">Start Subscription</a>
            </div>
        </nav>
    </div>
</header>

<main class="container py-4 saas-shell saas-landing-page">
    <section class="saas-landing-hero mb-4">
        <div class="saas-landing-hero-content">
            <p class="saas-eyebrow">All-in-one SaaS POS Platform</p>
            <h1>Launch your store faster with a modern POS that handles sales, stock, and teams in one place.</h1>
            <p class="saas-hero-text">
                Built for retail and growing businesses. Manage checkout, inventory, customers, reporting, and multi-user operations from any device.
            </p>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <a class="btn btn-primary btn-lg" href="<?= site_url('saas/register') ?>">Start Free Setup</a>
                <a class="btn btn-outline-secondary btn-lg" href="#pricing">See Pricing</a>
            </div>
            <div class="saas-hero-badges">
                <span>Fast Checkout</span>
                <span>Inventory Sync</span>
                <span>Multi Staff Roles</span>
                <span>Cloud Based</span>
            </div>
        </div>
        <div class="saas-landing-hero-stats">
            <div class="saas-kpi">
                <p class="saas-kpi-label">Average Setup Time</p>
                <p class="saas-kpi-value">15 Minutes</p>
            </div>
            <div class="saas-kpi">
                <p class="saas-kpi-label">Uptime</p>
                <p class="saas-kpi-value">99.9%</p>
            </div>
            <div class="saas-kpi">
                <p class="saas-kpi-label">Support Availability</p>
                <p class="saas-kpi-value">Every Day</p>
            </div>
            <div class="saas-kpi">
                <p class="saas-kpi-label">Access</p>
                <p class="saas-kpi-value">Web + Tablet</p>
            </div>
        </div>
    </section>

    <section class="saas-proof-strip mb-4">
        <div class="saas-proof-item">
            <strong>Sales</strong>
            <span>Quick billing and receipt workflow</span>
        </div>
        <div class="saas-proof-item">
            <strong>Inventory</strong>
            <span>Real-time quantity visibility</span>
        </div>
        <div class="saas-proof-item">
            <strong>Customers</strong>
            <span>Purchase history and loyalty insights</span>
        </div>
        <div class="saas-proof-item">
            <strong>Reports</strong>
            <span>Clear daily business performance</span>
        </div>
    </section>

    <section id="features" class="saas-card saas-section p-4 mb-4">
        <div class="saas-section-head">
            <div>
                <p class="saas-section-title mb-1">Key Features</p>
                <h3 class="saas-section-heading mb-0">Everything you need to run your business smoothly</h3>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Smart Sales Screen</h6>
                    <p>Fast checkout, discounts, taxes, split payments, and instant printable receipts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Advanced Inventory</h6>
                    <p>Track stock, low-level alerts, adjustments, receiving, and item organization.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Staff Permissions</h6>
                    <p>Create owner, manager, and cashier roles with secure access control.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Customer CRM</h6>
                    <p>Store customer details, buying behavior, and communication history.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Business Reports</h6>
                    <p>See product performance, payment breakdown, and revenue trends in seconds.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-feature-box">
                    <h6>Multi-Tenant Ready</h6>
                    <p>Each business data is isolated while platform admins manage everything centrally.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="why-us" class="saas-card saas-section p-4 mb-4">
        <div class="row g-3 align-items-stretch">
            <div class="col-lg-6">
                <p class="saas-section-title mb-1">Why Choose <?= esc($config['company']) ?></p>
                <h3 class="saas-section-heading mb-2">Made for teams that want speed and clarity</h3>
                <ul class="saas-benefits-list">
                    <li>Simple interface for staff with minimal training</li>
                    <li>No installation required, launch from browser</li>
                    <li>Reliable workflow for single or multi-branch business</li>
                    <li>Frequent platform updates and centralized control</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="saas-step-list">
                    <div class="saas-step-item">
                        <span>1</span>
                        <div>
                            <h6>Register your business</h6>
                            <p>Create your account and submit store information.</p>
                        </div>
                    </div>
                    <div class="saas-step-item">
                        <span>2</span>
                        <div>
                            <h6>Choose a subscription</h6>
                            <p>Select the plan that fits your user and inventory needs.</p>
                        </div>
                    </div>
                    <div class="saas-step-item">
                        <span>3</span>
                        <div>
                            <h6>Start selling</h6>
                            <p>Access your dashboard and run sales with full visibility.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="mb-4">
        <div class="saas-section-head mb-3">
            <div>
                <p class="saas-section-title mb-1">Pricing</p>
                <h3 class="saas-section-heading mb-0">Choose the right plan for your business size</h3>
            </div>
        </div>
        <div class="row g-3">
        <?php foreach ($plans as $index => $plan): ?>
            <div class="col-md-4">
                <div class="card h-100 saas-plan saas-plan-card <?= $index === 1 ? 'saas-plan-featured' : '' ?>">
                    <div class="card-body">
                        <?php if ($index === 1): ?>
                            <span class="saas-plan-badge">Most Popular</span>
                        <?php endif; ?>
                        <h5><?= esc($plan['plan_name']) ?></h5>
                        <div class="saas-price">$<?= esc(number_format((float)$plan['price_monthly'], 2)) ?></div>
                        <div class="text-muted">per month</div>
                        <hr>
                        <ul class="small mb-0 saas-plan-list">
                            <li>Max Users: <?= esc($plan['max_users']) ?></li>
                            <li>Max Locations: <?= esc($plan['max_locations']) ?></li>
                            <li>Max Items: <?= esc($plan['max_items']) ?></li>
                        </ul>
                        <a class="btn btn-primary w-100 mt-3" href="<?= site_url('saas/register') ?>">Start with <?= esc($plan['plan_name']) ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>

    <section class="saas-cta mb-4">
        <div>
            <h4>Ready to modernize your business operations?</h4>
            <p class="mb-0">Create your account now and start using your cloud POS in minutes.</p>
        </div>
        <a class="btn btn-light btn-lg" href="<?= site_url('saas/register') ?>">Create Account</a>
    </section>
</main>
</body>
</html>
