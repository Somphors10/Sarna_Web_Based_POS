<?php
/**
 * @var array $tenants
 * @var array $platform_admins
 * @var bool $is_owner
 * @var array $subscription_requests
 * @var string $active_page
 */
?>
<!doctype html>
<html lang="<?= current_language_code() ?>">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>Super Admin - Tenants</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/bootswatch5/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="css/theme/saas-modern.css">
    <style>
        .tenant-confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1200;
            padding: 1rem;
        }

        .tenant-confirm-overlay.is-open {
            display: flex;
        }

        .tenant-confirm-modal {
            width: min(440px, 100%);
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.3);
            overflow: hidden;
        }

        .tenant-confirm-header {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .tenant-confirm-body {
            padding: 0 1.25rem 1rem;
            color: #334155;
            line-height: 1.4;
        }

        .tenant-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.9rem 1.25rem 1.1rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .logout-confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1300;
            padding: 1rem;
        }

        .logout-confirm-overlay.is-open {
            display: flex;
        }

        .logout-confirm-modal {
            width: min(420px, 100%);
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.3);
            overflow: hidden;
        }

        .logout-confirm-header {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .logout-confirm-body {
            padding: 0 1.25rem 1rem;
            color: #334155;
            line-height: 1.4;
        }

        .logout-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.9rem 1.25rem 1.1rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .saas-highlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .saas-highlight-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fbff;
            padding: 0.85rem 0.95rem;
        }

        .saas-highlight-card strong {
            display: block;
            color: #0f172a;
            font-size: 0.92rem;
            margin-bottom: 0.25rem;
        }

        .saas-highlight-card span {
            color: #475569;
            font-size: 0.82rem;
        }

        .saas-bi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.75rem;
        }

        .saas-bi-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.9rem 0.95rem;
            background: #fff;
        }

        .saas-bi-title {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            margin-bottom: 0.4rem;
        }

        .saas-bi-value {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .saas-bi-sub {
            color: #475569;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .sa-super-layout {
            display: grid;
            grid-template-columns: 205px minmax(0, 1fr);
            gap: 10px;
            align-items: stretch;
            padding: 0;
        }

        .sa-super-sidebar {
            position: sticky;
            top: 0;
            border-right: 1px solid #e5ebf5;
            background: #ffffff;
            box-shadow: none;
            min-height: 100vh;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        .sa-super-sidebar-head {
            padding: 20px 16px 14px;
        }

        .sa-super-brand {
            display: block;
            color: #2563eb;
            font-weight: 700;
            font-size: 48px;
            letter-spacing: -0.02em;
            text-decoration: none;
            line-height: 1;
        }

        .sa-super-brand:hover,
        .sa-super-brand:focus {
            color: #2563eb;
            text-decoration: none;
        }

        .sa-super-nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 6px 10px 14px;
            flex: 1;
        }

        .sa-super-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #2f3d4f;
            padding: 8px 8px;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .sa-super-nav a span:last-child {
            line-height: 1.2;
        }

        .sa-super-nav a:hover {
            background: #f7f9fc;
            border-color: #e7edf7;
            color: #1f2937;
        }

        .sa-super-nav a.is-active {
            background: #f2f6fd;
            border-color: #deebff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .sa-super-nav .sa-super-nav-logout {
            margin-top: auto;
        }

        .sa-super-menu-dot {
            width: 24px;
            height: 24px;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sa-super-menu-dot img {
            width: 20px;
            height: 20px;
            object-fit: contain;
            opacity: 0.95;
        }

        .sa-super-content {
            min-width: 0;
        }

        .sa-super-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 180px;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .sa-super-toolbar .form-control,
        .sa-super-toolbar .form-select {
            min-height: 44px;
        }

        @media (max-width: 992px) {
            .sa-super-layout {
                grid-template-columns: 1fr;
                padding: 0.75rem;
            }

            .sa-super-sidebar {
                position: static;
                min-height: auto;
                height: auto;
                border-right: 0;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                margin-top: 0.75rem;
            }

            .sa-super-toolbar {
                grid-template-columns: 1fr;
            }
        }

    </style>
</head>
<body class="saas-modern">
<?php
    $total_tenants = count($tenants);
    $pending_count = count($subscription_requests);
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

    $approval_load = $total_tenants > 0 ? round(($pending_count / $total_tenants) * 100, 1) : 0;
?>
<div class="sa-super-layout">
    <aside class="sa-super-sidebar">
        <div class="sa-super-sidebar-head">
            <a class="sa-super-brand" href="<?= site_url('super-admin/overview') ?>">OSPOS</a>
        </div>
        <nav class="sa-super-nav">
            <a class="<?= $active_page === 'overview' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/overview') ?>"><span class="sa-super-menu-dot"><img src="<?= base_url('images/menubar/home.svg') ?>" alt="Overview"></span><span>Overview</span></a>
            <a class="<?= $active_page === 'businesses' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/businesses') ?>"><span class="sa-super-menu-dot"><img src="<?= base_url('images/menubar/office.svg') ?>" alt="Businesses"></span><span>Businesses</span></a>
            <a class="<?= $active_page === 'admins' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/admins') ?>"><span class="sa-super-menu-dot"><img src="<?= base_url('images/menubar/employees.svg') ?>" alt="Platform Admins"></span><span>Platform Admins</span></a>
            <a class="<?= $active_page === 'requests' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/requests') ?>"><span class="sa-super-menu-dot"><img src="<?= base_url('images/menubar/messages.svg') ?>" alt="Pending Requests"></span><span>Pending Requests</span></a>
            <a class="sa-super-nav-logout js-super-admin-logout" href="<?= site_url('super-admin/logout') ?>"><span class="sa-super-menu-dot"><img src="<?= base_url('images/menubar/config.svg') ?>" alt="Logout"></span><span>Logout</span></a>
        </nav>
    </aside>

    <div class="sa-super-content">
        <main class="container py-4 saas-shell">
            <div class="saas-topbar mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <p class="saas-eyebrow mb-1">Platform Console</p>
                        <h4 class="saas-title">Tenant Management</h4>
                        <p class="saas-subtitle">Control business accounts, platform admins, and signup approvals.</p>
                    </div>
                </div>
                <div class="saas-topbar-meta">
                    <span class="saas-meta-chip">Businesses: <?= $total_tenants ?></span>
                    <span class="saas-meta-chip">Pending: <?= $pending_count ?></span>
                    <span class="saas-meta-chip">Admins: <?= $admins_count ?></span>
                </div>
                <div class="sa-super-toolbar">
                    <input type="text" id="super_admin_search" class="form-control" placeholder="Search businesses, admins, requests...">
                    <select id="super_admin_status_filter" class="form-select">
                        <option value="">All Business Status</option>
                        <option value="active">active</option>
                        <option value="suspended">suspended</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                </div>
            </div>

            <?php if ($active_page === 'overview'): ?>
            <div id="section-overview" class="saas-summary-grid">
                <div class="saas-summary-card">
                    <p class="saas-summary-label">Total Businesses</p>
                    <p class="saas-summary-value"><?= $total_tenants ?></p>
                </div>
                <div class="saas-summary-card">
                    <p class="saas-summary-label">Pending Signups</p>
                    <p class="saas-summary-value"><?= $pending_count ?></p>
                </div>
                <div class="saas-summary-card">
                    <p class="saas-summary-label">Platform Admins</p>
                    <p class="saas-summary-value"><?= $admins_count ?></p>
                </div>
            </div>

    <div class="saas-card saas-section mt-3">
        <div class="card-header">
            <p class="saas-block-title">Must Show</p>
            <p class="saas-block-subtitle">Primary controls every Super Admin should check each day.</p>
        </div>
        <div class="saas-highlight-grid px-3 pb-3">
            <div class="saas-highlight-card">
                <strong>Pending Registrations</strong>
                <span>Review and approve incoming business signups.</span>
            </div>
            <div class="saas-highlight-card">
                <strong>Tenant Status Health</strong>
                <span>Keep active businesses running and resolve suspended accounts.</span>
            </div>
            <div class="saas-highlight-card">
                <strong>Platform Admin Access</strong>
                <span>Ensure only authorized admins can manage the platform.</span>
            </div>
            <div class="saas-highlight-card">
                <strong>Business Intelligence</strong>
                <span>Track growth and operational load in BI cards below.</span>
            </div>
        </div>
    </div>

    <div class="saas-card saas-section mt-3">
        <div class="card-header">
            <p class="saas-block-title">BI Overview</p>
            <p class="saas-block-subtitle">Live platform intelligence to support operational decisions.</p>
        </div>
        <div class="saas-bi-grid px-3 pb-3">
            <div class="saas-bi-card">
                <div class="saas-bi-title">Active Businesses</div>
                <div class="saas-bi-value"><?= $active_tenants ?></div>
                <div class="saas-bi-sub">Healthy active tenant count</div>
            </div>
            <div class="saas-bi-card">
                <div class="saas-bi-title">Suspended Businesses</div>
                <div class="saas-bi-value"><?= $suspended_tenants ?></div>
                <div class="saas-bi-sub">Accounts needing intervention</div>
            </div>
            <div class="saas-bi-card">
                <div class="saas-bi-title">Cancelled Businesses</div>
                <div class="saas-bi-value"><?= $cancelled_tenants ?></div>
                <div class="saas-bi-sub">Inactive and churned accounts</div>
            </div>
            <div class="saas-bi-card">
                <div class="saas-bi-title">Approval Load</div>
                <div class="saas-bi-value"><?= $approval_load ?>%</div>
                <div class="saas-bi-sub">Pending approvals vs total businesses</div>
            </div>
        </div>
    </div>
            <?php endif; ?>

    <?php if ($active_page === 'businesses'): ?>
    <div id="section-businesses" class="saas-card saas-section mt-3">
        <div class="card-header">
            <p class="saas-block-title">Businesses</p>
            <p class="saas-block-subtitle">Manage tenant status and owner account visibility.</p>
        </div>
        <div class="table-responsive">
            <table class="table saas-table mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Company</th>
                    <th>Owner</th>
                    <th>Owner Username</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($tenants)): ?>
                    <tr><td colspan="7" class="saas-empty">No businesses yet.</td></tr>
                <?php else: ?>
                <?php foreach ($tenants as $tenant): ?>
                    <tr class="js-searchable-row"
                        data-group="tenant"
                        data-status="<?= esc(strtolower((string)($tenant['status'] ?? ''))) ?>"
                        data-search="<?= esc(strtolower(trim(($tenant['tenant_code'] ?? '') . ' ' . ($tenant['company_name'] ?? '') . ' ' . ($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '') . ' ' . ($tenant['username'] ?? '')))) ?>">
                        <td><?= esc($tenant['tenant_id']) ?></td>
                        <td><?= esc($tenant['tenant_code']) ?></td>
                        <td><?= esc($tenant['company_name']) ?></td>
                        <td><?= esc(trim(($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? ''))) ?></td>
                        <td><?= esc($tenant['username'] ?? '') ?></td>
                        <td>
                            <?php $status = strtolower((string)($tenant['status'] ?? '')); ?>
                            <span class="badge saas-status saas-status-<?= esc($status) ?>"><?= esc($tenant['status']) ?></span>
                        </td>
                        <td>
                            <?= form_open('super-admin/toggle-status/' . (int)$tenant['tenant_id'], ['class' => 'js-tenant-status-form']) ?>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="active" <?= $tenant['status'] === 'active' ? 'selected' : '' ?>>active</option>
                                    <option value="suspended" <?= $tenant['status'] === 'suspended' ? 'selected' : '' ?>>suspended</option>
                                    <option value="cancelled" <?= $tenant['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                                </select>
                                <input type="hidden" name="tenant_code" value="<?= esc($tenant['tenant_code']) ?>">
                                <button class="btn btn-sm btn-primary saas-btn-save" type="submit">Save</button>
                            </div>
                            <?= form_close() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($active_page === 'admins'): ?>
    <div id="section-admins" class="saas-card saas-section mt-3">
        <div class="card-header">
            <p class="saas-block-title">Platform Admins</p>
            <p class="saas-block-subtitle">Accounts allowed to operate this platform dashboard.</p>
        </div>
        <div class="table-responsive">
            <table class="table saas-table mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($platform_admins)): ?>
                    <tr><td colspan="5" class="saas-empty">No platform admins.</td></tr>
                <?php else: ?>
                <?php foreach ($platform_admins as $admin): ?>
                    <tr class="js-searchable-row"
                        data-group="admin"
                        data-search="<?= esc(strtolower(trim(($admin['username'] ?? '') . ' ' . ($admin['full_name'] ?? '') . ' ' . ($admin['email'] ?? '')))) ?>">
                        <td><?= esc($admin['admin_id']) ?></td>
                        <td><?= esc($admin['username']) ?></td>
                        <td><?= esc($admin['full_name']) ?></td>
                        <td><?= esc($admin['email'] ?? '') ?></td>
                        <?php $admin_status = strtolower((string)($admin['status'] ?? '')); ?>
                        <td><span class="badge saas-status saas-status-<?= esc($admin_status) ?>"><?= esc($admin['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($active_page === 'requests'): ?>
    <div id="section-requests" class="saas-card saas-section mt-3">
        <div class="card-header">
            <p class="saas-block-title">Pending Website Registrations</p>
            <p class="saas-block-subtitle">Approve paid requests to auto-create active business POS accounts.</p>
        </div>
        <div class="table-responsive">
            <table class="table saas-table mb-0">
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
                    <tr>
                        <td colspan="8" class="saas-empty">No pending requests.</td>
                    </tr>
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
                                <div class="d-flex gap-1">
                                    <?= form_open('super-admin/approve-request/' . (int)$request['request_id']) ?>
                                    <button class="btn btn-sm btn-success saas-action-btn" type="submit">Approve</button>
                                    <?= form_close() ?>
                                    <?= form_open('super-admin/reject-request/' . (int)$request['request_id']) ?>
                                    <button class="btn btn-sm btn-outline-danger saas-action-btn" type="submit">Reject</button>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
        </main>
    </div>
</div>
<div id="tenant-status-confirm" class="tenant-confirm-overlay" aria-hidden="true">
    <div class="tenant-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="tenant-confirm-title">
        <div class="tenant-confirm-header" id="tenant-confirm-title">Confirm status update</div>
        <div class="tenant-confirm-body" id="tenant-confirm-message">Are you sure you want to save this change?</div>
        <div class="tenant-confirm-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="tenant-confirm-cancel">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" id="tenant-confirm-save">Save</button>
        </div>
    </div>
</div>
<div id="super-admin-logout-confirm" class="logout-confirm-overlay" aria-hidden="true">
    <div class="logout-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="logout-confirm-title">
        <div class="logout-confirm-header" id="logout-confirm-title">Confirm logout</div>
        <div class="logout-confirm-body">Are you sure you want to logout from Super Admin?</div>
        <div class="logout-confirm-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="logout-confirm-cancel">Cancel</button>
            <button type="button" class="btn btn-danger btn-sm" id="logout-confirm-continue">Logout</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('tenant-status-confirm');
        const messageEl = document.getElementById('tenant-confirm-message');
        const cancelBtn = document.getElementById('tenant-confirm-cancel');
        const saveBtn = document.getElementById('tenant-confirm-save');
        const logoutOverlay = document.getElementById('super-admin-logout-confirm');
        const logoutCancelBtn = document.getElementById('logout-confirm-cancel');
        const logoutContinueBtn = document.getElementById('logout-confirm-continue');
        const searchInput = document.getElementById('super_admin_search');
        const statusFilter = document.getElementById('super_admin_status_filter');
        let pendingForm = null;
        let pendingLogoutHref = null;

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

        document.querySelectorAll('.js-tenant-status-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openModal(form);
            });
        });

        const applyRowFilters = function() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const status = statusFilter ? statusFilter.value.toLowerCase() : '';

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

        saveBtn.addEventListener('click', function() {
            if (!pendingForm) {
                closeModal();
                return;
            }

            const formToSubmit = pendingForm;
            closeModal();
            formToSubmit.submit();
        });

        cancelBtn.addEventListener('click', closeModal);

        overlay.addEventListener('click', function(event) {
            if (event.target === overlay) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
                closeModal();
            }
            if (logoutOverlay && event.key === 'Escape' && logoutOverlay.classList.contains('is-open')) {
                closeLogoutModal();
            }
        });
    });
</script>
</body>
</html>
