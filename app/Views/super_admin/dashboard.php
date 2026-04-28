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
                <a class="btn btn-light saas-btn-quiet" href="<?= site_url('register-company') ?>">Create Business</a>
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
                            <?= form_open('super-admin/toggle-status/' . (int)$tenant['tenant_id']) ?>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="active" <?= $tenant['status'] === 'active' ? 'selected' : '' ?>>active</option>
                                    <option value="suspended" <?= $tenant['status'] === 'suspended' ? 'selected' : '' ?>>suspended</option>
                                    <option value="cancelled" <?= $tenant['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                                </select>
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

    <?php if ($is_owner): ?>
        <div class="saas-card saas-section mt-3">
            <div class="card-header">
                <p class="saas-block-title">Create Platform Admin</p>
                <p class="saas-block-subtitle">Only owner can create and manage other platform admins.</p>
            </div>
            <div class="card-body saas-admin-form-body">
                <?= form_open('super-admin/create-admin') ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Full Name</label>
                        <input class="form-control" name="full_name" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary saas-btn-primary" type="submit">Create Admin</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    <?php endif; ?>
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
</body>
</html>
