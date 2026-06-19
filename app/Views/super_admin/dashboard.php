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
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>Super Admin Console</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/super-admin.css?v=4') ?>">
</head>
<body class="sa-dashboard">
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
            'subtitle' => 'Review website registrations and approve new POS accounts.',
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
    if (service('request')->getGet('company_created') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'New company created successfully.'];
    }

    $error_code = (string)service('request')->getGet('error');
    $error_messages = [
        'request_not_found' => 'Request not found or already processed.',
        'tenant_or_user_exists' => 'Tenant code or owner username already exists.',
        'approve_failed' => 'Could not approve the request. Please try again.',
        'admin_creation_disabled' => 'Creating platform admins from this screen is disabled.',
    ];
    if ($error_code !== '' && isset($error_messages[$error_code])) {
        $flash_messages[] = ['type' => 'error', 'text' => $error_messages[$error_code]];
    }
?>
<div class="sa-layout">
    <aside class="sa-sidebar">
        <div class="sa-sidebar__head">
            <a class="sa-brand" href="<?= site_url('super-admin/overview') ?>">WBPOS</a>
            <span class="sa-brand__tag">Super Admin</span>
        </div>
        <nav class="sa-nav">
            <a class="<?= $active_page === 'overview' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/overview') ?>">
                <img class="sa-nav__icon" src="<?= base_url('images/super-admin/overview.svg') ?>" alt="">
                <span>Overview</span>
            </a>
            <a class="<?= $active_page === 'businesses' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/businesses') ?>">
                <img class="sa-nav__icon" src="<?= base_url('images/super-admin/businesses.svg') ?>" alt="">
                <span>Businesses</span>
            </a>
            <a class="<?= $active_page === 'admins' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/admins') ?>">
                <img class="sa-nav__icon" src="<?= base_url('images/super-admin/admins.svg') ?>" alt="">
                <span>Platform Admins</span>
            </a>
            <a class="<?= $active_page === 'requests' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/requests') ?>">
                <img class="sa-nav__icon" src="<?= base_url('images/super-admin/pending.svg') ?>" alt="">
                <span>Pending Requests</span>
                <?php if ($pending_count > 0): ?>
                    <span class="sa-nav__badge"><?= $pending_count ?></span>
                <?php endif; ?>
            </a>
            <a class="sa-nav__logout js-super-admin-logout" href="<?= site_url('super-admin/logout') ?>">
                <img class="sa-nav__icon" src="<?= base_url('images/super-admin/logout.svg') ?>" alt="">
                <span>Logout</span>
            </a>
        </nav>
    </aside>
    <div id="sa_sidebar_backdrop" class="sa-sidebar-backdrop" aria-hidden="true"></div>

    <div class="sa-main">
        <header class="sa-mobile-topbar">
            <button id="sa_mobile_sidebar_toggle" class="sa-mobile-menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
                <span class="sa-hamburger-icon" aria-hidden="true"></span>
            </button>
            <span class="sa-mobile-topbar__title"><?= esc($current_meta['title']) ?></span>
        </header>
        <header class="sa-hero">
            <p class="sa-hero__eyebrow">Platform Console</p>
            <h1 class="sa-hero__title"><?= esc($current_meta['title']) ?></h1>
            <p class="sa-hero__subtitle"><?= esc($current_meta['subtitle']) ?></p>
            <div class="sa-hero__chips">
                <span class="sa-chip">Businesses: <?= $total_tenants ?></span>
                <span class="sa-chip">Pending: <?= $pending_count ?></span>
                <span class="sa-chip">Admins: <?= $admins_count ?></span>
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
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Tenant Status Breakdown</h2>
                <p class="sa-panel__subtitle">How your <?= $total_tenants ?> registered businesses are split by status.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-metrics sa-metrics--status">
                    <div class="sa-metric">
                        <div class="sa-metric__label">Active</div>
                        <div class="sa-metric__value"><?= $active_tenants ?></div>
                        <div class="sa-metric__hint">Currently operating</div>
                    </div>
                    <div class="sa-metric">
                        <div class="sa-metric__label">Suspended</div>
                        <div class="sa-metric__value"><?= $suspended_tenants ?></div>
                        <div class="sa-metric__hint">Needs intervention</div>
                    </div>
                    <div class="sa-metric">
                        <div class="sa-metric__label">Cancelled</div>
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
                <div class="sa-checklist">
                    <a class="sa-checklist__item sa-checklist__item--link" href="<?= site_url('super-admin/requests') ?>">
                        <strong>Pending Registrations<?= $pending_count > 0 ? ' (' . $pending_count . ')' : '' ?></strong>
                        <span>Review and approve incoming business signups.</span>
                    </a>
                    <a class="sa-checklist__item sa-checklist__item--link" href="<?= site_url('super-admin/businesses') ?>">
                        <strong>Manage Businesses<?= $suspended_tenants > 0 ? ' (' . $suspended_tenants . ' suspended)' : '' ?></strong>
                        <span>Update tenant status and view owner accounts.</span>
                    </a>
                    <a class="sa-checklist__item sa-checklist__item--link" href="<?= site_url('super-admin/admins') ?>">
                        <strong>Platform Admins (<?= $admins_count ?>)</strong>
                        <span>View who can access this console.</span>
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
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($platform_admins)): ?>
                        <tr><td colspan="5" class="sa-empty">No platform admins.</td></tr>
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
                                    <?= form_open('super-admin/approve-request/' . (int)$request['request_id']) ?>
                                    <button class="sa-btn sa-btn--success" type="submit">Approve</button>
                                    <?= form_close() ?>
                                    <?= form_open('super-admin/reject-request/' . (int)$request['request_id']) ?>
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
        const mobileToggle = document.getElementById('sa_mobile_sidebar_toggle');
        const mobileBackdrop = document.getElementById('sa_sidebar_backdrop');

        const closeMobileSidebar = function() {
            document.documentElement.classList.remove('sa-mobile-sidebar-open');
            if (mobileToggle) {
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        };

        const openMobileSidebar = function() {
            document.documentElement.classList.add('sa-mobile-sidebar-open');
            if (mobileToggle) {
                mobileToggle.setAttribute('aria-expanded', 'true');
            }
        };

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                if (window.innerWidth > 992) {
                    return;
                }

                if (document.documentElement.classList.contains('sa-mobile-sidebar-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            });
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeMobileSidebar);
        }

        document.querySelectorAll('.sa-nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && window.innerWidth <= 992 && document.documentElement.classList.contains('sa-mobile-sidebar-open')) {
                closeMobileSidebar();
            }
        });

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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
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
