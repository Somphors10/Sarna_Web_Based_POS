<?php
/**
 * @var array $tenants
 * @var array $platform_admins
 * @var bool $is_owner
 * @var array $subscription_requests
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
    </style>
</head>
<body class="saas-modern">
<main class="container py-4 saas-shell">
    <div class="saas-topbar mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="saas-eyebrow mb-1">Platform Console</p>
                <h4 class="saas-title">Tenant Management</h4>
                <p class="saas-subtitle">Control business accounts, platform admins, and signup approvals.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-light saas-btn-quiet" href="<?= site_url('super-admin/logout') ?>">Logout</a>
            </div>
        </div>
        <div class="saas-topbar-meta">
            <span class="saas-meta-chip">Businesses: <?= count($tenants) ?></span>
            <span class="saas-meta-chip">Pending: <?= count($subscription_requests) ?></span>
            <span class="saas-meta-chip">Admins: <?= count($platform_admins) ?></span>
        </div>
    </div>

    <div class="saas-summary-grid">
        <div class="saas-summary-card">
            <p class="saas-summary-label">Total Businesses</p>
            <p class="saas-summary-value"><?= count($tenants) ?></p>
        </div>
        <div class="saas-summary-card">
            <p class="saas-summary-label">Pending Signups</p>
            <p class="saas-summary-value"><?= count($subscription_requests) ?></p>
        </div>
        <div class="saas-summary-card">
            <p class="saas-summary-label">Platform Admins</p>
            <p class="saas-summary-value"><?= count($platform_admins) ?></p>
        </div>
    </div>

    <div class="saas-card saas-section mt-3">
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
                    <tr>
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

    <div class="saas-card saas-section mt-3">
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
                    <tr>
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

</main>
<main class="container pb-4 saas-shell">
    <div class="saas-card saas-section">
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
                        <tr>
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
</main>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('tenant-status-confirm');
        const messageEl = document.getElementById('tenant-confirm-message');
        const cancelBtn = document.getElementById('tenant-confirm-cancel');
        const saveBtn = document.getElementById('tenant-confirm-save');
        let pendingForm = null;

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

        document.querySelectorAll('.js-tenant-status-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openModal(form);
            });
        });

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
        });
    });
</script>
</body>
</html>
