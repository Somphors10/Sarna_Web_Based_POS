<?php
/**
 * @var array $tenants
 * @var array $platform_admins
 * @var bool $is_owner
 * @var array $subscription_requests
 * @var array $password_reset_requests
 * @var string $active_page
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>Super Admin Console</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/layout-sidebar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/responsive.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/super-admin.css?v=13') ?>">
</head>
<body class="sa-dashboard">
<script>
    (function() {
        const key = 'ospos_sidebar_collapsed';
        if (window.innerWidth > 992 && localStorage.getItem(key) === '1') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    })();
</script>
<?php
    $total_tenants = count($tenants);
    $pending_count = count($subscription_requests) + count($password_reset_requests ?? []);
    $admins_count = count($platform_admins);
    $active_page = $active_page ?? 'overview';

    $active_tenants = 0;
    $suspended_tenants = 0;
    $cancelled_tenants = 0;
    foreach ($tenants as $tenant) {
        $status = strtolower((string)($tenant['status'] ?? ''));
        if ($status === 'active') {
            $active_tenants++;
        } elseif ($status === 'suspended') {
            $suspended_tenants++;
        } elseif ($status === 'cancelled') {
            $cancelled_tenants++;
        }
    }

    $page_meta = [
        'overview' => [
            'title' => 'Tenant Management',
            'subtitle' => 'Control business accounts, platform admins, and signup approvals.',
        ],
        'businesses' => [
            'title' => 'Businesses',
            'subtitle' => 'View and update tenant status for every registered company.',
        ],
        'admins' => [
            'title' => 'Platform Admins',
            'subtitle' => 'Accounts allowed to operate this platform dashboard.',
        ],
        'requests' => [
            'title' => 'Pending Requests',
            'subtitle' => 'Review website registrations and password reset requests.',
        ],
    ];
    $current_meta = $page_meta[$active_page] ?? $page_meta['overview'];

    $flash_messages = [];
    if (service('request')->getGet('request_approved') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Registration approved. The business account is now active.'];
    }
    if (service('request')->getGet('request_rejected') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Registration request rejected.'];
    }
    if (service('request')->getGet('password_reset_approved') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Password reset approved. The user can sign in with the new password.'];
    }
    if (service('request')->getGet('password_reset_rejected') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Password reset request rejected.'];
    }
    if (service('request')->getGet('company_created') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'New company created successfully.'];
    }

    $error_code = (string)service('request')->getGet('error');
    $error_messages = [
        'request_not_found' => 'Request not found or already processed.',
        'tenant_or_user_exists' => 'Tenant code or owner username already exists.',
        'approve_failed' => 'Could not approve the request. Please try again.',
        'admin_creation_disabled' => 'Creating platform admins from this screen is disabled.',
        'password_reset_not_found' => 'Password reset request not found or already processed.',
        'password_reset_failed' => 'Could not approve the password reset. Please try again.',
        'password_reset_unavailable' => 'Password reset table is not installed yet.',
    ];
    if ($error_code !== '' && isset($error_messages[$error_code])) {
        $flash_messages[] = ['type' => 'error', 'text' => $error_messages[$error_code]];
    }
?>
<div class="neo-layout sa-layout">
    <aside class="neo-global-sidebar">
        <div class="neo-global-brand-row">
            <a class="neo-global-brand" href="<?= site_url('super-admin/overview') ?>">
                <span class="neo-global-brand-full">WBPOS</span>
                <span class="neo-global-brand-mini">W</span>
            </a>
            <button id="sa_sidebar_toggle" class="neo-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">
                <svg class="neo-sidebar-toggle__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        </div>
        <div class="neo-global-sidebar-body">
            <nav class="neo-global-menu">
                <a class="neo-global-menu-item <?= $active_page === 'overview' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/overview') ?>" title="Overview">
                    <img class="neo-nav__icon" src="<?= base_url('images/super-admin/overview.svg') ?>" alt="">
                    <span>Overview</span>
                </a>
                <a class="neo-global-menu-item <?= $active_page === 'businesses' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/businesses') ?>" title="Businesses">
                    <img class="neo-nav__icon" src="<?= base_url('images/super-admin/businesses.svg') ?>" alt="">
                    <span>Businesses</span>
                </a>
                <a class="neo-global-menu-item <?= $active_page === 'admins' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/admins') ?>" title="Platform Admins">
                    <img class="neo-nav__icon" src="<?= base_url('images/super-admin/admins.svg') ?>" alt="">
                    <span>Platform Admins</span>
                </a>
                <a class="neo-global-menu-item <?= $active_page === 'requests' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/requests') ?>" title="Pending Requests">
                    <img class="neo-nav__icon" src="<?= base_url('images/super-admin/pending.svg') ?>" alt="">
                    <span>Pending Requests</span>
                </a>
            </nav>
            <div class="neo-sidebar-footer">
                <a class="neo-sidebar-logout js-super-admin-logout" href="<?= site_url('super-admin/logout') ?>" title="Logout">
                    <img class="neo-nav__icon" src="<?= base_url('images/super-admin/logout.svg') ?>" alt="">
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>
    <div id="sa_sidebar_backdrop" class="neo-sidebar-backdrop" aria-hidden="true"></div>

    <main class="neo-global-content sa-main">
        <header class="sa-mobile-topbar">
            <button id="sa_mobile_sidebar_toggle" class="neo-mobile-menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
                <span class="neo-hamburger-icon" aria-hidden="true"></span>
            </button>
            <span class="sa-mobile-topbar__title"><?= esc($current_meta['title']) ?></span>
        </header>
        <header class="sa-page-header">
            <div class="sa-page-header__content">
                <p class="sa-page-header__eyebrow">Platform Console</p>
                <h1 class="sa-page-header__title"><?= esc($current_meta['title']) ?></h1>
                <p class="sa-page-header__subtitle"><?= esc($current_meta['subtitle']) ?></p>
            </div>
            <?php if (in_array($active_page, ['businesses', 'admins', 'requests'], true)): ?>
            <div class="sa-toolbar">
                <div class="sa-search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                    </svg>
                    <input type="text" id="super_admin_search" class="sa-input" placeholder="Search businesses, admins, requests...">
                </div>
                <?php if ($active_page === 'businesses'): ?>
                <select id="super_admin_status_filter" class="sa-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <?php else: ?>
                <select id="super_admin_status_filter" class="sa-select" hidden aria-hidden="true">
                    <option value=""></option>
                </select>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </header>

        <?php foreach ($flash_messages as $flash): ?>
            <div class="sa-alert sa-alert--<?= esc($flash['type']) ?>">
                <?= esc($flash['text']) ?>
            </div>
        <?php endforeach; ?>

        <?php if ($active_page === 'overview'): ?>
        <section class="sa-stat-grid">
            <article class="sa-stat-card sa-stat-card--purple">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">Total Businesses</p>
                    <p class="sa-stat-card__value"><?= $total_tenants ?></p>
                </div>
            </article>
            <article class="sa-stat-card sa-stat-card--amber">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3H10l-2-3H2"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">Pending Requests</p>
                    <p class="sa-stat-card__value"><?= $pending_count ?></p>
                </div>
            </article>
            <article class="sa-stat-card sa-stat-card--blue">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">Platform Admins</p>
                    <p class="sa-stat-card__value"><?= $admins_count ?></p>
                </div>
            </article>
        </section>

        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Tenant Status Breakdown</h2>
                <p class="sa-panel__subtitle">How your <?= $total_tenants ?> registered businesses are split by status.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-metrics sa-metrics--status">
                    <div class="sa-metric sa-metric--active">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Active</span>
                        </div>
                        <div class="sa-metric__value"><?= $active_tenants ?></div>
                        <div class="sa-metric__hint">Currently operating</div>
                    </div>
                    <div class="sa-metric sa-metric--suspended">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Suspended</span>
                        </div>
                        <div class="sa-metric__value"><?= $suspended_tenants ?></div>
                        <div class="sa-metric__hint">Needs intervention</div>
                    </div>
                    <div class="sa-metric sa-metric--cancelled">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Cancelled</span>
                        </div>
                        <div class="sa-metric__value"><?= $cancelled_tenants ?></div>
                        <div class="sa-metric__hint">Closed accounts</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Quick Actions</h2>
                <p class="sa-panel__subtitle">Jump to the pages you use most often.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-actions">
                    <a class="sa-action-card" href="<?= site_url('super-admin/requests') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--amber" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3H10l-2-3H2"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Pending Registrations<?= $pending_count > 0 ? ' (' . $pending_count . ')' : '' ?></strong>
                            <span>Review and approve incoming business signups.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="sa-action-card" href="<?= site_url('super-admin/businesses') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--purple" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Manage Businesses<?= $suspended_tenants > 0 ? ' (' . $suspended_tenants . ' suspended)' : '' ?></strong>
                            <span>Update tenant status and view owner accounts.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="sa-action-card" href="<?= site_url('super-admin/admins') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Platform Admins (<?= $admins_count ?>)</strong>
                            <span>View who can access this console.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'businesses'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">All Businesses</h2>
                <p class="sa-panel__subtitle">Manage tenant status and owner account visibility.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Company</th>
                        <th>Owner</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($tenants)): ?>
                        <tr><td colspan="7" class="sa-empty">No businesses yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($tenants as $tenant): ?>
                        <?php $status = strtolower((string)($tenant['status'] ?? '')); ?>
                        <tr class="js-searchable-row"
                            data-group="tenant"
                            data-status="<?= esc($status) ?>"
                            data-search="<?= esc(strtolower(trim(($tenant['tenant_code'] ?? '') . ' ' . ($tenant['company_name'] ?? '') . ' ' . ($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '') . ' ' . ($tenant['username'] ?? '')))) ?>">
                            <td><?= esc($tenant['tenant_id']) ?></td>
                            <td><?= esc($tenant['tenant_code']) ?></td>
                            <td><?= esc($tenant['company_name']) ?></td>
                            <td><?= esc(trim(($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? ''))) ?></td>
                            <td><?= esc($tenant['username'] ?? '') ?></td>
                            <td><span class="sa-status sa-status--<?= esc($status) ?>"><?= esc($tenant['status']) ?></span></td>
                            <td>
                                <?= form_open('super-admin/toggle-status/' . (int)$tenant['tenant_id'], ['class' => 'js-tenant-status-form']) ?>
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-title="Business #<?= (int)$tenant['tenant_id'] ?>"
                                            data-id="<?= esc((string)$tenant['tenant_id'], 'attr') ?>"
                                            data-company="<?= esc($tenant['company_name'], 'attr') ?>"
                                            data-code="<?= esc($tenant['tenant_code'], 'attr') ?>"
                                            data-owner="<?= esc(trim(($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '')), 'attr') ?>"
                                            data-username="<?= esc($tenant['username'] ?? '', 'attr') ?>"
                                            data-status="<?= esc($tenant['status'], 'attr') ?>">
                                        View
                                    </button>
                                    <select class="sa-select--sm" name="status">
                                        <option value="active" <?= $tenant['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="suspended" <?= $tenant['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                        <option value="cancelled" <?= $tenant['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="tenant_code" value="<?= esc($tenant['tenant_code']) ?>">
                                    <button class="sa-btn sa-btn--primary" type="submit">Save</button>
                                </div>
                                <?= form_close() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'admins'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Platform Admins</h2>
                <p class="sa-panel__subtitle">Accounts allowed to operate this platform dashboard.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($platform_admins)): ?>
                        <tr><td colspan="6" class="sa-empty">No platform admins.</td></tr>
                    <?php else: ?>
                    <?php foreach ($platform_admins as $admin): ?>
                        <?php $admin_status = strtolower((string)($admin['status'] ?? '')); ?>
                        <tr class="js-searchable-row"
                            data-group="admin"
                            data-search="<?= esc(strtolower(trim(($admin['username'] ?? '') . ' ' . ($admin['full_name'] ?? '') . ' ' . ($admin['email'] ?? '')))) ?>">
                            <td><?= esc($admin['admin_id']) ?></td>
                            <td><?= esc($admin['username']) ?></td>
                            <td><?= esc($admin['full_name']) ?></td>
                            <td><?= esc($admin['email'] ?? '') ?></td>
                            <td><span class="sa-status sa-status--<?= esc($admin_status) ?>"><?= esc($admin['status']) ?></span></td>
                            <td>
                                <button type="button"
                                        class="sa-btn sa-btn--ghost js-sa-view-detail"
                                        data-title="Platform admin #<?= (int)$admin['admin_id'] ?>"
                                        data-id="<?= esc((string)$admin['admin_id'], 'attr') ?>"
                                        data-username="<?= esc($admin['username'], 'attr') ?>"
                                        data-name="<?= esc($admin['full_name'], 'attr') ?>"
                                        data-email="<?= esc($admin['email'] ?? '', 'attr') ?>"
                                        data-status="<?= esc($admin['status'], 'attr') ?>">
                                    View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'requests'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Pending Website Registrations</h2>
                <p class="sa-panel__subtitle">Approve paid requests to auto-create active business POS accounts.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Code</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Payment Ref</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($subscription_requests)): ?>
                        <tr><td colspan="8" class="sa-empty">No pending requests.</td></tr>
                    <?php else: ?>
                    <?php foreach ($subscription_requests as $request): ?>
                        <tr class="js-searchable-row"
                            data-group="request"
                            data-search="<?= esc(strtolower(trim(($request['company_name'] ?? '') . ' ' . ($request['tenant_code'] ?? '') . ' ' . ($request['owner_first_name'] ?? '') . ' ' . ($request['owner_last_name'] ?? '') . ' ' . ($request['owner_email'] ?? '') . ' ' . ($request['plan_name'] ?? '') . ' ' . ($request['payment_reference'] ?? '')))) ?>">
                            <td><?= esc($request['request_id']) ?></td>
                            <td><?= esc($request['company_name']) ?></td>
                            <td><?= esc($request['tenant_code']) ?></td>
                            <td><?= esc($request['owner_first_name'] . ' ' . $request['owner_last_name']) ?></td>
                            <td><?= esc($request['owner_email']) ?></td>
                            <td><?= esc($request['plan_name'] ?? '') ?></td>
                            <td><?= esc($request['payment_reference']) ?></td>
                            <td>
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-title="Registration request #<?= (int)$request['request_id'] ?>"
                                            data-company="<?= esc($request['company_name'], 'attr') ?>"
                                            data-code="<?= esc($request['tenant_code'], 'attr') ?>"
                                            data-owner="<?= esc(trim($request['owner_first_name'] . ' ' . $request['owner_last_name']), 'attr') ?>"
                                            data-email="<?= esc($request['owner_email'], 'attr') ?>"
                                            data-phone="<?= esc($request['owner_phone'] ?? '', 'attr') ?>"
                                            data-username="<?= esc($request['owner_username'] ?? '', 'attr') ?>"
                                            data-plan="<?= esc($request['plan_name'] ?? '', 'attr') ?>"
                                            data-payment="<?= esc($request['payment_reference'], 'attr') ?>"
                                            data-created="<?= esc($request['created_at'] ?? '', 'attr') ?>">
                                        View
                                    </button>
                                    <?= form_open('super-admin/approve-request/' . (int)$request['request_id'], [
                                        'class' => 'js-confirm-action-form',
                                        'data-action' => 'approve',
                                        'data-context' => 'registration',
                                    ]) ?>
                                    <button class="sa-btn sa-btn--success" type="submit">Approve</button>
                                    <?= form_close() ?>
                                    <?= form_open('super-admin/reject-request/' . (int)$request['request_id'], [
                                        'class' => 'js-confirm-action-form',
                                        'data-action' => 'reject',
                                        'data-context' => 'registration',
                                    ]) ?>
                                    <button class="sa-btn sa-btn--danger" type="submit">Reject</button>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sa-panel" style="margin-top: 24px;">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Pending Password Resets</h2>
                <p class="sa-panel__subtitle">Approve reset requests submitted from the POS login page.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Code</th>
                        <th>Username</th>
                        <th>Tenant</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($password_reset_requests)): ?>
                        <tr><td colspan="6" class="sa-empty">No pending password resets.</td></tr>
                    <?php else: ?>
                    <?php foreach ($password_reset_requests as $reset): ?>
                        <tr class="js-searchable-row"
                            data-group="password-reset"
                            data-search="<?= esc(strtolower(trim(($reset['tenant_code'] ?? '') . ' ' . ($reset['username'] ?? '') . ' ' . ($reset['tenant_id'] ?? '')))) ?>">
                            <td><?= esc($reset['request_id']) ?></td>
                            <td><?= esc($reset['tenant_code']) ?></td>
                            <td><?= esc($reset['username']) ?></td>
                            <td>#<?= esc($reset['tenant_id'] ?? '') ?></td>
                            <td><?= esc($reset['created_at'] ?? '') ?></td>
                            <td>
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-title="Password reset request #<?= (int)$reset['request_id'] ?>"
                                            data-code="<?= esc($reset['tenant_code'], 'attr') ?>"
                                            data-username="<?= esc($reset['username'], 'attr') ?>"
                                            data-tenant="#<?= esc((string)($reset['tenant_id'] ?? ''), 'attr') ?>"
                                            data-created="<?= esc($reset['created_at'] ?? '', 'attr') ?>">
                                        View
                                    </button>
                                    <?= form_open('super-admin/approve-password-reset/' . (int)$reset['request_id'], [
                                        'class' => 'js-confirm-action-form',
                                        'data-action' => 'approve',
                                        'data-context' => 'password-reset',
                                    ]) ?>
                                    <button class="sa-btn sa-btn--success" type="submit">Approve</button>
                                    <?= form_close() ?>
                                    <?= form_open('super-admin/reject-password-reset/' . (int)$reset['request_id'], [
                                        'class' => 'js-confirm-action-form',
                                        'data-action' => 'reject',
                                        'data-context' => 'password-reset',
                                    ]) ?>
                                    <button class="sa-btn sa-btn--danger" type="submit">Reject</button>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<div id="sa-row-detail" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="sa-row-detail-title">
        <div class="sa-modal__head" id="sa-row-detail-title">Details</div>
        <div class="sa-modal__body sa-detail-list" id="sa-row-detail-body"></div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--primary" id="sa-row-detail-close">Close</button>
        </div>
    </div>
</div>

<div id="request-action-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="request-action-confirm-title">
        <div class="sa-modal__head" id="request-action-confirm-title">Confirm action</div>
        <div class="sa-modal__body" id="request-action-confirm-message">Are you sure?</div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--ghost" id="request-action-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--primary" id="request-action-confirm-continue">Confirm</button>
        </div>
    </div>
</div>

<div id="tenant-status-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="tenant-confirm-title">
        <div class="sa-modal__head" id="tenant-confirm-title">Confirm status update</div>
        <div class="sa-modal__body" id="tenant-confirm-message">Are you sure you want to save this change?</div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--ghost" id="tenant-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--primary" id="tenant-confirm-save">Save</button>
        </div>
    </div>
</div>

<div id="super-admin-logout-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="logout-confirm-title">
        <div class="sa-modal__head" id="logout-confirm-title">Confirm logout</div>
        <div class="sa-modal__body">Are you sure you want to logout from Super Admin?</div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--ghost" id="logout-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--danger-solid" id="logout-confirm-continue">Logout</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarKey = 'ospos_sidebar_collapsed';
        const sidebarToggle = document.getElementById('sa_sidebar_toggle');
        const mobileToggle = document.getElementById('sa_mobile_sidebar_toggle');
        const mobileBackdrop = document.getElementById('sa_sidebar_backdrop');

        const syncForViewport = function() {
            if (window.innerWidth <= 992) {
                document.documentElement.classList.remove('sidebar-collapsed');
            } else if (localStorage.getItem(sidebarKey) === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        };

        const setExpandedState = function() {
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', (!document.documentElement.classList.contains('sidebar-collapsed')).toString());
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    return;
                }

                document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem(sidebarKey, document.documentElement.classList.contains('sidebar-collapsed') ? '1' : '0');
                setExpandedState();
            });
        }

        const closeMobileSidebar = function() {
            document.documentElement.classList.remove('mobile-sidebar-open');
            if (mobileToggle) {
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        };

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                if (window.innerWidth > 992) {
                    return;
                }

                const isOpen = document.documentElement.classList.toggle('mobile-sidebar-open');
                mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeMobileSidebar);
        }

        document.querySelectorAll('.neo-global-menu-item, .neo-sidebar-logout').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', function() {
            syncForViewport();
            setExpandedState();
            if (window.innerWidth > 992) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && window.innerWidth <= 992 && document.documentElement.classList.contains('mobile-sidebar-open')) {
                closeMobileSidebar();
            }
        });

        syncForViewport();
        setExpandedState();

        const overlay = document.getElementById('tenant-status-confirm');
        const messageEl = document.getElementById('tenant-confirm-message');
        const cancelBtn = document.getElementById('tenant-confirm-cancel');
        const saveBtn = document.getElementById('tenant-confirm-save');
        const logoutOverlay = document.getElementById('super-admin-logout-confirm');
        const logoutCancelBtn = document.getElementById('logout-confirm-cancel');
        const logoutContinueBtn = document.getElementById('logout-confirm-continue');
        const actionOverlay = document.getElementById('request-action-confirm');
        const actionTitleEl = document.getElementById('request-action-confirm-title');
        const actionMessageEl = document.getElementById('request-action-confirm-message');
        const actionCancelBtn = document.getElementById('request-action-confirm-cancel');
        const actionContinueBtn = document.getElementById('request-action-confirm-continue');
        const searchInput = document.getElementById('super_admin_search');
        const statusFilter = document.getElementById('super_admin_status_filter');
        let pendingForm = null;
        let pendingLogoutHref = null;
        let pendingActionForm = null;

        const closeModal = function() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            pendingForm = null;
        };

        const openModal = function(form) {
            const statusInput = form.querySelector('select[name="status"]');
            const tenantCodeInput = form.querySelector('input[name="tenant_code"]');
            const nextStatus = statusInput ? statusInput.value : '';
            const tenantCode = tenantCodeInput ? tenantCodeInput.value : '';
            messageEl.textContent = tenantCode
                ? 'Save status "' + nextStatus + '" for tenant "' + tenantCode + '"?'
                : 'Save this status change?';
            pendingForm = form;
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
        };

        const closeLogoutModal = function() {
            logoutOverlay.classList.remove('is-open');
            logoutOverlay.setAttribute('aria-hidden', 'true');
            pendingLogoutHref = null;
        };

        const openLogoutModal = function(href) {
            pendingLogoutHref = href;
            logoutOverlay.classList.add('is-open');
            logoutOverlay.setAttribute('aria-hidden', 'false');
        };

        const closeActionModal = function() {
            if (!actionOverlay) {
                return;
            }

            actionOverlay.classList.remove('is-open');
            actionOverlay.setAttribute('aria-hidden', 'true');
            pendingActionForm = null;
        };

        const openActionModal = function(form) {
            if (!actionOverlay || !actionMessageEl || !actionTitleEl || !actionContinueBtn) {
                return;
            }

            const action = form.dataset.action || 'approve';
            const context = form.dataset.context || 'registration';
            const isApprove = action === 'approve';

            actionTitleEl.textContent = isApprove ? 'Confirm approval' : 'Confirm rejection';

            if (context === 'password-reset') {
                actionMessageEl.textContent = isApprove
                    ? 'Are you sure you want to approve this password reset?'
                    : 'Are you sure you want to reject this password reset?';
            } else {
                actionMessageEl.textContent = isApprove
                    ? 'Are you sure you want to approve this company registration?'
                    : 'Are you sure you want to reject this company registration?';
            }

            actionContinueBtn.textContent = isApprove ? 'Approve' : 'Reject';
            actionContinueBtn.className = isApprove
                ? 'sa-btn sa-btn--success'
                : 'sa-btn sa-btn--danger-solid';

            pendingActionForm = form;
            actionOverlay.classList.add('is-open');
            actionOverlay.setAttribute('aria-hidden', 'false');
        };

        document.querySelectorAll('.js-confirm-action-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openActionModal(form);
            });
        });

        document.querySelectorAll('.js-tenant-status-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openModal(form);
            });
        });

        const applyRowFilters = function() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const status = statusFilter && !statusFilter.hidden ? statusFilter.value.toLowerCase() : '';

            document.querySelectorAll('.js-searchable-row').forEach(function(row) {
                const haystack = (row.dataset.search || '').toLowerCase();
                const rowGroup = row.dataset.group || '';
                const rowStatus = (row.dataset.status || '').toLowerCase();

                const queryMatch = query === '' || haystack.indexOf(query) !== -1;
                const statusMatch = rowGroup !== 'tenant' || status === '' || rowStatus === status;
                row.style.display = queryMatch && statusMatch ? '' : 'none';
            });
        };

        if (searchInput) {
            searchInput.addEventListener('input', applyRowFilters);
        }
        if (statusFilter) {
            statusFilter.addEventListener('change', applyRowFilters);
        }
        applyRowFilters();

        document.querySelectorAll('.js-super-admin-logout').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                openLogoutModal(link.getAttribute('href'));
            });
        });

        if (logoutContinueBtn) {
            logoutContinueBtn.addEventListener('click', function() {
                if (!pendingLogoutHref) {
                    closeLogoutModal();
                    return;
                }
                window.location.href = pendingLogoutHref;
            });
        }

        if (logoutCancelBtn) {
            logoutCancelBtn.addEventListener('click', closeLogoutModal);
        }

        if (logoutOverlay) {
            logoutOverlay.addEventListener('click', function(event) {
                if (event.target === logoutOverlay) {
                    closeLogoutModal();
                }
            });
        }

        if (actionContinueBtn) {
            actionContinueBtn.addEventListener('click', function() {
                if (!pendingActionForm) {
                    closeActionModal();
                    return;
                }

                const formToSubmit = pendingActionForm;
                closeActionModal();
                formToSubmit.submit();
            });
        }

        if (actionCancelBtn) {
            actionCancelBtn.addEventListener('click', closeActionModal);
        }

        if (actionOverlay) {
            actionOverlay.addEventListener('click', function(event) {
                if (event.target === actionOverlay) {
                    closeActionModal();
                }
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (!pendingForm) {
                    closeModal();
                    return;
                }

                const formToSubmit = pendingForm;
                closeModal();
                formToSubmit.submit();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        if (overlay) {
            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    closeModal();
                }
            });
        }

        const detailOverlay = document.getElementById('sa-row-detail');
        const detailTitleEl = document.getElementById('sa-row-detail-title');
        const detailBodyEl = document.getElementById('sa-row-detail-body');
        const detailCloseBtn = document.getElementById('sa-row-detail-close');

        const closeDetailModal = function() {
            if (!detailOverlay) {
                return;
            }

            detailOverlay.classList.remove('is-open');
            detailOverlay.setAttribute('aria-hidden', 'true');
        };

        const openDetailModal = function(button) {
            if (!detailOverlay || !detailTitleEl || !detailBodyEl) {
                return;
            }

            const lines = [];
            const map = {
                id: 'ID',
                company: 'Company',
                code: 'Company code',
                owner: 'Owner',
                name: 'Full name',
                email: 'Email',
                phone: 'Phone',
                username: 'Username',
                plan: 'Plan',
                payment: 'Payment reference',
                tenant: 'Tenant',
                status: 'Status',
                created: 'Requested'
            };

            Object.keys(map).forEach(function(key) {
                const value = button.dataset[key];
                if (value) {
                    lines.push('<div class="sa-detail-list__row"><span>' + map[key] + '</span><strong>' + value + '</strong></div>');
                }
            });

            detailTitleEl.textContent = button.dataset.title || 'Details';
            detailBodyEl.innerHTML = lines.join('');
            detailOverlay.classList.add('is-open');
            detailOverlay.setAttribute('aria-hidden', 'false');
        };

        document.querySelectorAll('.js-sa-view-detail').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                openDetailModal(button);
            });
        });

        if (detailCloseBtn) {
            detailCloseBtn.addEventListener('click', closeDetailModal);
        }

        if (detailOverlay) {
            detailOverlay.addEventListener('click', function(event) {
                if (event.target === detailOverlay) {
                    closeDetailModal();
                }
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
                closeModal();
            }
            if (logoutOverlay && event.key === 'Escape' && logoutOverlay.classList.contains('is-open')) {
                closeLogoutModal();
            }
            if (actionOverlay && event.key === 'Escape' && actionOverlay.classList.contains('is-open')) {
                closeActionModal();
            }
            if (detailOverlay && event.key === 'Escape' && detailOverlay.classList.contains('is-open')) {
                closeDetailModal();
            }
        });
    });
</script>
</body>
</html>
