<?php
/**
 * @var list<array<string, mixed>> $permissions
 * @var list<array<string, mixed>> $roles
 * @var array<int, list<int>> $role_perm_map
 * @var array<string, mixed>|null $edit_permission
 * @var array<string, mixed>|null $edit_role
 * @var string $active_tab
 * @var bool $show_permission_form
 * @var bool $show_role_form
 * @var string $message
 * @var string $error
 */
$perm_names = [];
foreach ($permissions as $permission) {
    $perm_names[(int)$permission['permission_id']] = (string)$permission['permission_name'];
}
$is_permissions = ($active_tab ?? 'roles') === 'permissions';
$show_permission_form = !empty($show_permission_form);
$show_role_form = !empty($show_role_form);
?>
<?= view('partial/header') ?>

<section class="neo-module-page rbac-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= esc(lang('Roles.title')) ?></h3>
        </div>
        <div class="neo-module-actions btn-toolbar">
            <?php if ($is_permissions): ?>
                <button type="button" class="btn btn-primary btn-sm rbac-btn" id="rbac-new-permission"><?= esc(lang('Roles.new_permission')) ?></button>
            <?php else: ?>
                <button type="button" class="btn btn-primary btn-sm rbac-btn" id="rbac-new-role"><?= esc(lang('Roles.new_role')) ?></button>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>

    <nav class="neo-list-tabs rbac-tabs" aria-label="<?= esc(lang('Roles.title')) ?>">
        <div class="neo-list-tabs__track">
            <a class="neo-list-tab<?= !$is_permissions ? ' is-active' : '' ?>" href="<?= site_url('roles?tab=roles') ?>">
                <?= esc(lang('Roles.tab_roles')) ?>
            </a>
            <a class="neo-list-tab<?= $is_permissions ? ' is-active' : '' ?>" href="<?= site_url('roles?tab=permissions') ?>">
                <?= esc(lang('Roles.tab_permissions')) ?>
            </a>
        </div>
    </nav>

    <?php if ($is_permissions): ?>
        <article class="rbac-panel">
            <div class="rbac-table-wrap">
            <table class="table table-striped rbac-table">
                <thead>
                    <tr>
                        <th><?= esc(lang('Roles.permission_code')) ?></th>
                        <th><?= esc(lang('Roles.permission_name')) ?></th>
                        <th class="rbac-col-desc"><?= esc(lang('Roles.description')) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $permission): ?>
                        <tr>
                            <td data-label="<?= esc(lang('Roles.permission_code')) ?>">
                                <code><?= esc($permission['permission_code']) ?></code>
                                <?php if ((int)$permission['is_system'] === 1): ?><span class="rbac-tag">POS</span><?php endif; ?>
                            </td>
                            <td data-label="<?= esc(lang('Roles.permission_name')) ?>"><?= esc($permission['permission_name']) ?></td>
                            <td class="rbac-col-desc" data-label="<?= esc(lang('Roles.description')) ?>"><?= esc((string)($permission['description'] ?? '')) ?></td>
                            <td class="rbac-actions">
                                <button
                                    type="button"
                                    class="rbac-link-btn rbac-edit-permission"
                                    data-id="<?= (int)$permission['permission_id'] ?>"
                                    data-code="<?= esc($permission['permission_code']) ?>"
                                    data-name="<?= esc($permission['permission_name']) ?>"
                                    data-description="<?= esc((string)($permission['description'] ?? '')) ?>"
                                    data-system="<?= (int)$permission['is_system'] === 1 ? '1' : '0' ?>"
                                ><?= esc(lang('Roles.edit')) ?></button>
                                <?php if ((int)$permission['is_system'] !== 1): ?>
                                    <?= form_open('roles/deletePermission', ['class' => 'rbac-inline']) ?>
                                        <input type="hidden" name="permission_id" value="<?= (int)$permission['permission_id'] ?>">
                                        <button type="submit" onclick="return confirm('Delete this permission?')"><?= esc(lang('Common.delete')) ?></button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </article>
    <?php else: ?>
        <article class="rbac-panel">
            <div class="rbac-table-wrap">
            <table class="table table-striped rbac-table">
                <thead>
                    <tr>
                        <th><?= esc(lang('Roles.role_name')) ?></th>
                        <th><?= esc(lang('Roles.permissions')) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($roles === []): ?>
                        <tr><td colspan="3" class="rbac-muted"><?= esc(lang('Roles.empty_roles')) ?></td></tr>
                    <?php endif; ?>
                    <?php foreach ($roles as $role): ?>
                        <?php
                        $ids = $role_perm_map[(int)$role['role_id']] ?? [];
                        $labels = [];
                        foreach ($ids as $id) {
                            if (isset($perm_names[$id])) {
                                $labels[] = $perm_names[$id];
                            }
                        }
                        ?>
                        <tr>
                            <td data-label="<?= esc(lang('Roles.role_name')) ?>">
                                <strong><?= esc($role['role_name']) ?></strong>
                                <?php if ((int)$role['is_system'] === 1): ?><span class="rbac-tag">system</span><?php endif; ?>
                                <div class="rbac-muted"><?= esc((string)($role['description'] ?? '')) ?></div>
                            </td>
                            <td data-label="<?= esc(lang('Roles.permissions')) ?>">
                                <?php if ($labels === []): ?>
                                    <span class="rbac-muted"><?= esc(lang('Roles.none_yet')) ?></span>
                                <?php else: ?>
                                    <div class="rbac-pills">
                                        <?php foreach ($labels as $label): ?>
                                            <span class="rbac-pill"><?= esc($label) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="rbac-actions">
                                <button
                                    type="button"
                                    class="rbac-link-btn rbac-edit-role"
                                    data-id="<?= (int)$role['role_id'] ?>"
                                    data-name="<?= esc($role['role_name']) ?>"
                                    data-description="<?= esc((string)($role['description'] ?? '')) ?>"
                                    data-ids="<?= esc(implode(',', array_map('intval', $ids))) ?>"
                                ><?= esc(lang('Roles.edit')) ?></button>
                                <?php if ((int)$role['is_system'] !== 1): ?>
                                    <?= form_open('roles/deleteRole', ['class' => 'rbac-inline']) ?>
                                        <input type="hidden" name="role_id" value="<?= (int)$role['role_id'] ?>">
                                        <button type="submit" onclick="return confirm('Delete this role?')"><?= esc(lang('Common.delete')) ?></button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </article>
    <?php endif; ?>
</section>

<div id="rbac-perm-modal" class="rbac-modal-overlay" aria-hidden="true">
    <div class="rbac-modal" role="dialog" aria-modal="true" aria-labelledby="rbac-perm-title">
        <div class="rbac-modal__head">
            <h4 id="rbac-perm-title"><?= esc(lang('Roles.new_permission')) ?></h4>
            <button type="button" class="rbac-modal__close" data-rbac-close aria-label="<?= esc(lang('Roles.cancel')) ?>">×</button>
        </div>
        <?= form_open('roles/savePermission', ['class' => 'rbac-form', 'id' => 'rbac-perm-form']) ?>
            <div class="rbac-modal__body">
                <input type="hidden" name="permission_id" id="permission_id" value="0">
                <div class="rbac-fields">
                    <div class="rbac-field">
                        <label for="permission_code"><?= esc(lang('Roles.permission_code')) ?></label>
                        <input id="permission_code" class="form-control" type="text" name="permission_code" placeholder="sales" required>
                        <small id="rbac-code-hint" class="rbac-muted" hidden><?= esc(lang('Roles.code_readonly_hint')) ?></small>
                    </div>
                    <div class="rbac-field">
                        <label for="permission_name"><?= esc(lang('Roles.permission_name')) ?></label>
                        <input id="permission_name" class="form-control" type="text" name="permission_name" placeholder="Sales" required>
                    </div>
                    <div class="rbac-field rbac-field--wide">
                        <label for="permission_description"><?= esc(lang('Roles.description')) ?></label>
                        <input id="permission_description" class="form-control" type="text" name="description" placeholder="Optional">
                    </div>
                </div>
            </div>
            <div class="rbac-modal__actions">
                <button type="button" class="btn btn-default btn-sm rbac-btn" data-rbac-close><?= esc(lang('Roles.cancel')) ?></button>
                <button type="submit" class="btn btn-primary btn-sm rbac-btn"><?= esc(lang('Roles.add_permission')) ?></button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<div id="rbac-role-modal" class="rbac-modal-overlay" aria-hidden="true">
    <div class="rbac-modal rbac-modal--wide" role="dialog" aria-modal="true" aria-labelledby="rbac-role-title">
        <div class="rbac-modal__head">
            <h4 id="rbac-role-title"><?= esc(lang('Roles.new_role')) ?></h4>
            <button type="button" class="rbac-modal__close" data-rbac-close aria-label="<?= esc(lang('Roles.cancel')) ?>">×</button>
        </div>
        <?= form_open('roles/saveRole', ['class' => 'rbac-form', 'id' => 'rbac-role-form']) ?>
            <div class="rbac-modal__body">
                <input type="hidden" name="role_id" id="role_id" value="0">
                <div class="rbac-fields">
                    <div class="rbac-field">
                        <label for="role_name"><?= esc(lang('Roles.role_name')) ?></label>
                        <input id="role_name" class="form-control" type="text" name="role_name" placeholder="Cashier" required>
                    </div>
                    <div class="rbac-field">
                        <label for="role_description"><?= esc(lang('Roles.description')) ?></label>
                        <input id="role_description" class="form-control" type="text" name="description" placeholder="Optional">
                    </div>
                </div>
                <fieldset class="rbac-checks">
                    <legend><?= esc(lang('Roles.assign_permissions')) ?></legend>
                    <div class="rbac-check-grid">
                        <?php foreach ($permissions as $permission): ?>
                            <?php if ((string)$permission['permission_code'] === 'home') { continue; } ?>
                            <label class="rbac-check">
                                <input
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="<?= (int)$permission['permission_id'] ?>"
                                    class="rbac-role-perm"
                                >
                                <span>
                                    <strong><?= esc($permission['permission_name']) ?></strong>
                                    <code><?= esc($permission['permission_code']) ?></code>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            </div>
            <div class="rbac-modal__actions">
                <button type="button" class="btn btn-default btn-sm rbac-btn" data-rbac-close><?= esc(lang('Roles.cancel')) ?></button>
                <button type="submit" class="btn btn-primary btn-sm rbac-btn"><?= esc(lang('Roles.add_role')) ?></button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<style>
.rbac-page { width: 100%; max-width: none; box-sizing: border-box; }
.rbac-tabs { margin: 0; border-radius: 12px 12px 0 0; }
.rbac-tabs .neo-list-tabs__track { max-width: 100%; }
.rbac-tabs .neo-list-tab { text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
.rbac-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-top: 0;
    border-radius: 0 0 12px 12px;
    padding: 16px;
    overflow: hidden;
}
.rbac-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.rbac-field--wide { grid-column: 1 / -1; }
.rbac-field label { display: block; margin: 0 0 4px; font-size: 13px; font-weight: 600; color: #334155; }
.rbac-form .form-control {
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    max-width: 100% !important;
    height: 38px !important;
    min-height: 38px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    background: #fff !important;
    color: #0f172a !important;
    font-size: 14px !important;
    box-sizing: border-box !important;
}
.rbac-page .rbac-btn {
    height: 32px !important;
    min-height: 32px !important;
    max-height: 32px !important;
    padding: 0 12px !important;
    line-height: 30px !important;
    font-size: 13px !important;
    border-radius: 8px !important;
    width: auto !important;
}
.rbac-checks { border: 0; margin: 14px 0 0; padding: 0; }
.rbac-checks legend { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; }
.rbac-check-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.rbac-check {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    margin: 0;
    padding: 8px 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 400;
}
.rbac-check input { margin-top: 3px; flex-shrink: 0; }
.rbac-check strong { display: block; font-size: 13px; color: #0f172a; }
.rbac-check code { font-size: 11px; color: #64748b; }
.rbac-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.rbac-table { margin-bottom: 0; font-size: 13px; width: 100%; }
.rbac-actions { white-space: nowrap; width: 1%; }
.rbac-link-btn {
    border: 0;
    background: none;
    padding: 0;
    color: #4f46e5;
    cursor: pointer;
    font: inherit;
}
.rbac-link-btn:hover { text-decoration: underline; }
.rbac-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99990;
    padding: 1rem;
}
.rbac-modal-overlay.is-open { display: flex !important; }
.rbac-modal {
    width: min(520px, 100%);
    max-height: min(92vh, 860px);
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.28);
    overflow: auto;
}
.rbac-modal--wide { width: min(720px, 100%); }
.rbac-modal__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px 8px;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
}
.rbac-modal__head h4 { margin: 0; font-size: 18px; color: #0f172a; }
.rbac-modal__close {
    border: 0;
    background: #f1f5f9;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 22px;
    line-height: 1;
    color: #475569;
    cursor: pointer;
    flex-shrink: 0;
}
.rbac-modal__body { padding: 8px 18px 6px; }
.rbac-modal__actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 18px 16px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
    position: sticky;
    bottom: 0;
}
.rbac-inline { display: inline; margin-left: 10px; }
.rbac-inline button { border: 0; background: none; color: #dc2626; padding: 0; }
.rbac-tag { display: inline-block; margin-left: 6px; padding: 1px 6px; border-radius: 999px; background: #f1f5f9; font-size: 11px; color: #475569; }
.rbac-muted { color: #64748b; font-size: 12px; font-weight: 400; }
.rbac-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.rbac-pill { background: #f1f5f9; border-radius: 999px; padding: 2px 8px; font-size: 12px; color: #334155; }
@media (max-width: 900px) {
    .rbac-check-grid { grid-template-columns: 1fr; }
}
@media (max-width: 760px) {
    .rbac-tabs { padding: 8px; }
    .rbac-tabs .neo-list-tabs__track { width: 100%; }
    .rbac-tabs .neo-list-tab { flex: 1 1 0; min-width: 0; padding: 7px 10px; }
    .rbac-panel { padding: 10px; }
    .rbac-fields, .rbac-check-grid { grid-template-columns: 1fr; }
    .rbac-col-desc { display: none; }
    .rbac-form .form-control { font-size: 16px !important; }
    .rbac-table thead { display: none; }
    .rbac-table, .rbac-table tbody, .rbac-table tr, .rbac-table td {
        display: block;
        width: 100%;
    }
    .rbac-table tr {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        background: #fff;
    }
    .rbac-table td {
        padding: 4px 0;
        border: 0;
        white-space: normal;
    }
    .rbac-table td[data-label]::before {
        content: attr(data-label);
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .rbac-actions {
        width: 100%;
        padding-top: 8px;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .rbac-inline { margin-left: 0; }
    .rbac-modal-overlay { padding: 8px; align-items: flex-end; }
    .rbac-modal, .rbac-modal--wide {
        width: 100%;
        max-height: 92vh;
        border-radius: 16px 16px 8px 8px;
    }
}
@media (max-width: 480px) {
    .rbac-page .rbac-btn {
        padding: 0 10px !important;
        font-size: 12px !important;
    }
}
</style>

<script>
(function () {
    function bindOverlay(overlay) {
        if (!overlay) {
            return;
        }
        function openModal() {
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
        }
        function closeModal() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
        }
        overlay.querySelectorAll('[data-rbac-close]').forEach(function (btn) {
            btn.addEventListener('click', closeModal);
        });
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
                closeModal();
            }
        });
        return { openModal: openModal, closeModal: closeModal };
    }

    var permOverlay = document.getElementById('rbac-perm-modal');
    var permUi = bindOverlay(permOverlay);
    if (permOverlay && permUi) {
        var title = document.getElementById('rbac-perm-title');
        var idField = document.getElementById('permission_id');
        var codeField = document.getElementById('permission_code');
        var nameField = document.getElementById('permission_name');
        var descField = document.getElementById('permission_description');
        var hint = document.getElementById('rbac-code-hint');
        var newTitle = <?= json_encode(lang('Roles.new_permission')) ?>;
        var editTitle = <?= json_encode(lang('Roles.editing_permission')) ?>;

        function fillForm(data) {
            idField.value = data.id || '0';
            codeField.value = data.code || '';
            nameField.value = data.name || '';
            descField.value = data.description || '';
            var isSystem = data.system === '1' || data.system === 1;
            codeField.readOnly = isSystem;
            hint.hidden = !isSystem;
            title.textContent = data.id && data.id !== '0' ? (editTitle + ': ' + (data.name || '')) : newTitle;
        }

        var newBtn = document.getElementById('rbac-new-permission');
        if (newBtn) newBtn.addEventListener('click', function () {
            fillForm({ id: '0', code: '', name: '', description: '', system: '0' });
            codeField.readOnly = false;
            hint.hidden = true;
            title.textContent = newTitle;
            permUi.openModal();
            setTimeout(function () { codeField.focus(); }, 50);
        });

        document.querySelectorAll('.rbac-edit-permission').forEach(function (btn) {
            btn.addEventListener('click', function () {
                fillForm(btn.dataset);
                permUi.openModal();
            });
        });

        <?php if ($show_permission_form): ?>
        fillForm({
            id: <?= json_encode((string)(int)($edit_permission['permission_id'] ?? 0)) ?>,
            code: <?= json_encode((string)($edit_permission['permission_code'] ?? '')) ?>,
            name: <?= json_encode((string)($edit_permission['permission_name'] ?? '')) ?>,
            description: <?= json_encode((string)($edit_permission['description'] ?? '')) ?>,
            system: <?= json_encode(!empty($edit_permission['is_system']) ? '1' : '0') ?>
        });
        permUi.openModal();
        <?php endif; ?>
    }

    var roleOverlay = document.getElementById('rbac-role-modal');
    var roleUi = bindOverlay(roleOverlay);
    if (roleOverlay && roleUi) {
        var roleTitle = document.getElementById('rbac-role-title');
        var roleIdField = document.getElementById('role_id');
        var roleNameField = document.getElementById('role_name');
        var roleDescField = document.getElementById('role_description');
        var roleBoxes = roleOverlay.querySelectorAll('.rbac-role-perm');
        var newRoleTitle = <?= json_encode(lang('Roles.new_role')) ?>;
        var editRoleTitle = <?= json_encode(lang('Roles.editing_role')) ?>;

        function fillRole(data) {
            roleIdField.value = data.id || '0';
            roleNameField.value = data.name || '';
            roleDescField.value = data.description || '';
            var selected = {};
            String(data.ids || '').split(',').forEach(function (id) {
                if (id) selected[id] = true;
            });
            roleBoxes.forEach(function (box) {
                box.checked = !!selected[box.value];
            });
            roleTitle.textContent = data.id && data.id !== '0' ? (editRoleTitle + ': ' + (data.name || '')) : newRoleTitle;
        }

        var newRoleBtn = document.getElementById('rbac-new-role');
        if (newRoleBtn) newRoleBtn.addEventListener('click', function () {
            fillRole({ id: '0', name: '', description: '', ids: '' });
            roleUi.openModal();
            setTimeout(function () { roleNameField.focus(); }, 50);
        });

        document.querySelectorAll('.rbac-edit-role').forEach(function (btn) {
            btn.addEventListener('click', function () {
                fillRole(btn.dataset);
                roleUi.openModal();
            });
        });

        <?php if ($show_role_form && !empty($edit_role)): ?>
        fillRole({
            id: <?= json_encode((string)(int)($edit_role['role_id'] ?? 0)) ?>,
            name: <?= json_encode((string)($edit_role['role_name'] ?? '')) ?>,
            description: <?= json_encode((string)($edit_role['description'] ?? '')) ?>,
            ids: <?= json_encode(implode(',', array_map('intval', $edit_role['permission_ids'] ?? []))) ?>
        });
        roleUi.openModal();
        <?php elseif ($show_role_form): ?>
        fillRole({ id: '0', name: '', description: '', ids: '' });
        roleUi.openModal();
        <?php endif; ?>
    }
})();
</script>

<?= view('partial/footer') ?>
