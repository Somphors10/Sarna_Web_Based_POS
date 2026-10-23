<?php
/**
 * @var list<array<string, mixed>> $permissions
 * @var list<array<string, mixed>> $permission_matrix
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
$perm_codes = [];
foreach ($permissions as $permission) {
    $perm_names[(int)$permission['permission_id']] = (string)$permission['permission_name'];
    $perm_codes[(int)$permission['permission_id']] = (string)$permission['permission_code'];
}
$permission_matrix = $permission_matrix ?? [];
$crud_actions = ['create', 'read', 'update', 'delete'];
$crud_letters = ['create' => 'C', 'read' => 'R', 'update' => 'U', 'delete' => 'D'];
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

    <?php if ($message !== '' || $error !== ''): ?>
        <div class="rbac-toast<?= $error !== '' ? ' rbac-toast--error' : ' rbac-toast--ok' ?>" id="rbac-flash" role="status">
            <span class="rbac-toast__icon" aria-hidden="true"><?= $error !== '' ? '!' : '✓' ?></span>
            <span class="rbac-toast__text"><?= esc($error !== '' ? $error : $message) ?></span>
            <button type="button" class="rbac-toast__close" id="rbac-flash-close" aria-label="<?= esc(lang('Roles.cancel')) ?>">×</button>
        </div>
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
                        <?php
                        $is_crud = function_exists('rbac_is_crud_code') && rbac_is_crud_code((string)$permission['permission_code']);
                        ?>
                        <tr>
                            <td data-label="<?= esc(lang('Roles.permission_code')) ?>">
                                <code><?= esc($permission['permission_code']) ?></code>
                                <?php if ($is_crud): ?><span class="rbac-tag">CRUD</span><?php endif; ?>
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
        <?php
        $module_names = [];
        foreach ($permission_matrix as $row) {
            $module_names[$row['module']] = $row['name'];
        }
        ?>
        <article class="rbac-panel rbac-panel--roles">
            <?php if ($roles === []): ?>
                <p class="rbac-empty"><?= esc(lang('Roles.empty_roles')) ?></p>
            <?php else: ?>
                <div class="rbac-role-table" role="table" aria-label="<?= esc(lang('Roles.tab_roles')) ?>">
                    <div class="rbac-role-table__head" role="row">
                        <div role="columnheader"><?= esc(lang('Roles.role_name')) ?></div>
                        <div role="columnheader"><?= esc(lang('Roles.permissions')) ?></div>
                        <div class="rbac-role-table__act" role="columnheader"></div>
                        <div class="rbac-role-table__act" role="columnheader"></div>
                        <div class="rbac-role-table__act" role="columnheader"></div>
                    </div>
                    <?php foreach ($roles as $role): ?>
                        <?php
                        $ids = $role_perm_map[(int)$role['role_id']] ?? [];
                        $module_actions = [];
                        foreach ($ids as $id) {
                            $code = $perm_codes[$id] ?? '';
                            if ($code === '' || $code === 'home') {
                                continue;
                            }
                            if (function_exists('rbac_parse_crud_code')) {
                                $parsed = rbac_parse_crud_code($code);
                                if ($parsed !== null) {
                                    $module_actions[$parsed['module']][$parsed['action']] = true;
                                    continue;
                                }
                            }
                            if (!isset($module_actions[$code])) {
                                $module_actions[$code] = ['create' => true, 'read' => true, 'update' => true, 'delete' => true];
                            }
                        }
                        $ordered_names = [];
                        $action_count = 0;
                        $full_modules = 0;
                        foreach ($permission_matrix as $row) {
                            $mod = $row['module'];
                            if (!isset($module_actions[$mod])) {
                                continue;
                            }
                            $acts = $module_actions[$mod];
                            unset($module_actions[$mod]);
                            $ordered_names[] = $row['name'];
                            $n = 0;
                            foreach ($crud_actions as $a) {
                                if (!empty($acts[$a])) {
                                    $n++;
                                    $action_count++;
                                }
                            }
                            if ($n === 4) {
                                $full_modules++;
                            }
                        }
                        foreach ($module_actions as $mod => $acts) {
                            $ordered_names[] = $module_names[$mod] ?? $mod;
                            $n = 0;
                            foreach ($crud_actions as $a) {
                                if (!empty($acts[$a])) {
                                    $n++;
                                    $action_count++;
                                }
                            }
                            if ($n === 4) {
                                $full_modules++;
                            }
                        }
                        $module_count = count($ordered_names);
                        $is_system = (int)$role['is_system'] === 1;
                        $preview = array_slice($ordered_names, 0, 4);
                        $more = $module_count - count($preview);
                        $initial = mb_strtoupper(mb_substr((string)$role['role_name'], 0, 1));
                        ?>
                        <div class="rbac-role-row" role="row">
                            <div class="rbac-role-row__who" role="cell">
                                <span class="rbac-role-avatar" aria-hidden="true"><?= esc($initial) ?></span>
                                <div class="rbac-role-row__meta">
                                    <div class="rbac-role-row__name">
                                        <?= esc($role['role_name']) ?>
                                        <?php if ($is_system): ?><span class="rbac-tag">system</span><?php endif; ?>
                                    </div>
                                    <?php if (trim((string)($role['description'] ?? '')) !== ''): ?>
                                        <div class="rbac-role-row__desc"><?= esc((string)$role['description']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="rbac-role-row__perms" role="cell">
                                <div class="rbac-role-row__perms-inner">
                                <?php if ($module_count === 0): ?>
                                    <span class="rbac-muted"><?= esc(lang('Roles.none_yet')) ?></span>
                                <?php else: ?>
                                    <div class="rbac-role-row__stats">
                                        <span class="rbac-stat"><?= (int)$module_count ?> <?= esc(lang('Roles.modules_count')) ?></span>
                                        <span class="rbac-stat rbac-stat--soft"><?= (int)$action_count ?> <?= esc(lang('Roles.actions_count')) ?></span>
                                    </div>
                                    <div class="rbac-role-row__modules" title="<?= esc(implode(', ', $ordered_names)) ?>">
                                        <?= esc(implode(' · ', $preview)) ?>
                                        <?php if ($more > 0): ?>
                                            <span class="rbac-more">+<?= (int)$more ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="rbac-role-row__act rbac-role-row__act--view" role="cell">
                                <button
                                    type="button"
                                    class="rbac-row-icon rbac-view-role"
                                    title="<?= esc(lang('Roles.view')) ?>"
                                    aria-label="<?= esc(lang('Roles.view')) ?>"
                                    data-id="<?= (int)$role['role_id'] ?>"
                                    data-name="<?= esc($role['role_name']) ?>"
                                    data-description="<?= esc((string)($role['description'] ?? '')) ?>"
                                    data-system="<?= $is_system ? '1' : '0' ?>"
                                    data-ids="<?= esc(implode(',', array_map('intval', $ids))) ?>"
                                ><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
                            </div>
                            <div class="rbac-role-row__act rbac-role-row__act--edit" role="cell">
                                <button
                                    type="button"
                                    class="rbac-row-icon rbac-edit-role"
                                    title="<?= esc(lang('Roles.edit')) ?>"
                                    aria-label="<?= esc(lang('Roles.edit')) ?>"
                                    data-id="<?= (int)$role['role_id'] ?>"
                                    data-name="<?= esc($role['role_name']) ?>"
                                    data-description="<?= esc((string)($role['description'] ?? '')) ?>"
                                    data-ids="<?= esc(implode(',', array_map('intval', $ids))) ?>"
                                ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
                            </div>
                            <div class="rbac-role-row__act rbac-role-row__act--del" role="cell">
                                <?php if (!$is_system): ?>
                                    <?= form_open('roles/deleteRole', ['class' => 'rbac-delete-form']) ?>
                                        <input type="hidden" name="role_id" value="<?= (int)$role['role_id'] ?>">
                                        <button
                                            type="button"
                                            class="rbac-row-icon rbac-row-icon--danger rbac-delete-role"
                                            title="<?= esc(lang('Common.delete')) ?>"
                                            aria-label="<?= esc(lang('Common.delete')) ?>"
                                        ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                <p class="rbac-muted" style="margin:0 0 12px;"><?= esc(lang('Roles.code_format_hint')) ?></p>
                <div class="rbac-fields" id="rbac-perm-builder">
                    <div class="rbac-field">
                        <label for="permission_module"><?= esc(lang('Roles.module_column')) ?></label>
                        <select id="permission_module" class="form-control">
                            <option value=""><?= esc(lang('Roles.select_module')) ?></option>
                            <?php foreach ($permission_matrix as $row): ?>
                                <option value="<?= esc($row['module'], 'attr') ?>"><?= esc($row['name']) ?> (<?= esc($row['module']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="rbac-field">
                        <label for="permission_action"><?= esc(lang('Roles.action_column')) ?></label>
                        <select id="permission_action" class="form-control">
                            <option value="create"><?= esc(lang('Roles.action_create')) ?> (.create)</option>
                            <option value="read"><?= esc(lang('Roles.action_read')) ?> (.read)</option>
                            <option value="update"><?= esc(lang('Roles.action_update')) ?> (.update)</option>
                            <option value="delete"><?= esc(lang('Roles.action_delete')) ?> (.delete)</option>
                        </select>
                    </div>
                    <div class="rbac-field">
                        <label for="permission_code"><?= esc(lang('Roles.permission_code')) ?></label>
                        <input id="permission_code" class="form-control" type="text" name="permission_code" placeholder="sales.create" required>
                        <small id="rbac-code-hint" class="rbac-muted" hidden><?= esc(lang('Roles.code_readonly_hint')) ?></small>
                        <small class="rbac-muted" id="rbac-code-format"><?= esc(lang('Roles.code_example')) ?></small>
                    </div>
                    <div class="rbac-field">
                        <label for="permission_name"><?= esc(lang('Roles.permission_name')) ?></label>
                        <input id="permission_name" class="form-control" type="text" name="permission_name" placeholder="Sales - Create" required>
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

<div id="rbac-view-modal" class="rbac-modal-overlay" aria-hidden="true">
    <div class="rbac-modal rbac-modal--wide" role="dialog" aria-modal="true" aria-labelledby="rbac-view-title">
        <div class="rbac-modal__head">
            <div>
                <h4 id="rbac-view-title"><?= esc(lang('Roles.view_role')) ?></h4>
                <p class="rbac-view-sub" id="rbac-view-sub"></p>
            </div>
            <button type="button" class="rbac-modal__close" data-rbac-close aria-label="<?= esc(lang('Roles.close')) ?>">×</button>
        </div>
        <div class="rbac-modal__body">
            <p class="rbac-muted" style="margin:0 0 12px;"><?= esc(lang('Roles.view_role_hint')) ?></p>
            <div class="rbac-view-legend">
                <span><i class="rbac-yes">✓</i> <?= esc(lang('Roles.allowed')) ?></span>
                <span><i class="rbac-no">–</i> <?= esc(lang('Roles.not_allowed')) ?></span>
            </div>
            <div class="rbac-crud-wrap rbac-view-wrap">
                <table class="table rbac-crud-table rbac-view-table">
                    <thead>
                        <tr>
                            <th><?= esc(lang('Roles.module_column')) ?></th>
                            <?php foreach ($crud_actions as $action): ?>
                                <th class="rbac-crud-col"><?= esc(lang('Roles.action_' . $action)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="rbac-view-body"></tbody>
                </table>
            </div>
        </div>
        <div class="rbac-modal__actions">
            <button type="button" class="btn btn-default btn-sm rbac-btn" data-rbac-close><?= esc(lang('Roles.close')) ?></button>
            <button type="button" class="btn btn-primary btn-sm rbac-btn" id="rbac-view-edit"><?= esc(lang('Roles.edit')) ?></button>
        </div>
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
                    <p class="rbac-muted" style="margin:0 0 10px;"><?= esc(lang('Roles.crud_hint')) ?></p>
                    <div class="rbac-crud-wrap">
                        <table class="table rbac-crud-table">
                            <thead>
                                <tr>
                                    <th><?= esc(lang('Roles.module_column')) ?></th>
                                    <?php foreach ($crud_actions as $action): ?>
                                        <th class="rbac-crud-col"><?= esc(lang('Roles.action_' . $action)) ?></th>
                                    <?php endforeach; ?>
                                    <th class="rbac-crud-col"><?= esc(lang('Roles.select_all_row')) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permission_matrix as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($row['name']) ?></strong>
                                            <code class="rbac-muted"><?= esc($row['module']) ?></code>
                                        </td>
                                        <?php foreach ($crud_actions as $action): ?>
                                            <?php $action_perm = $row['actions'][$action] ?? null; ?>
                                            <td class="rbac-crud-col">
                                                <?php if ($action_perm): ?>
                                                    <label class="rbac-crud-check">
                                                        <input
                                                            type="checkbox"
                                                            name="permission_ids[]"
                                                            value="<?= (int)$action_perm['id'] ?>"
                                                            class="rbac-role-perm"
                                                            data-module="<?= esc($row['module'], 'attr') ?>"
                                                            data-action="<?= esc($action, 'attr') ?>"
                                                        >
                                                    </label>
                                                <?php else: ?>
                                                    <span class="rbac-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="rbac-crud-col">
                                            <button type="button" class="rbac-link-btn rbac-row-all" data-module="<?= esc($row['module'], 'attr') ?>">
                                                <?= esc(lang('Roles.select_all_row')) ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
.rbac-toast {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 12px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid transparent;
    font-size: 13px;
    font-weight: 600;
    animation: rbacToastIn .28s ease;
}
.rbac-toast--ok { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
.rbac-toast--error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
.rbac-toast__icon {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    background: rgba(255,255,255,.7);
}
.rbac-toast__text { flex: 1; min-width: 0; }
.rbac-toast__close {
    border: 0;
    background: transparent;
    color: inherit;
    opacity: .55;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    padding: 0 4px;
}
.rbac-toast__close:hover { opacity: 1; }
.rbac-toast.is-hiding { opacity: 0; transform: translateY(-6px); transition: .25s ease; }
@keyframes rbacToastIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: none; }
}
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
.rbac-panel--roles { padding: 0; background: #fff; }
.rbac-empty { margin: 28px 0; text-align: center; color: #64748b; }
.rbac-role-table { width: 100%; }
.rbac-role-table__head,
.rbac-role-row {
    display: grid;
    grid-template-columns: minmax(220px, 1.2fr) minmax(240px, 1.8fr) 48px 48px 48px;
    gap: 0;
    align-items: center;
    padding: 0;
}
.rbac-role-table__head {
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #94a3b8;
    background: #f8fafc;
}
.rbac-role-table__head > div,
.rbac-role-row > div {
    padding: 14px 12px;
    border-right: 1px solid #eef2f7;
    min-height: 64px;
    display: flex;
    align-items: center;
}
.rbac-role-table__head > div:last-child,
.rbac-role-row > div:last-child { border-right: 0; }
.rbac-role-table__head > div:first-child,
.rbac-role-row > div:first-child { padding-left: 18px; }
.rbac-role-table__act,
.rbac-role-row__act {
    justify-content: center;
    padding-left: 0 !important;
    padding-right: 0 !important;
}
.rbac-role-row {
    border-bottom: 1px solid #eef2f7;
    transition: background .15s ease;
}
.rbac-role-row:last-child { border-bottom: 0; }
.rbac-role-row:hover { background: #fafbff; }
.rbac-role-row__who {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    width: 100%;
}
.rbac-role-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(145deg, #6366f1, #4f46e5);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.rbac-role-row__meta { min-width: 0; }
.rbac-role-row__name {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.rbac-role-row__desc {
    margin-top: 2px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
}
.rbac-role-row__perms { min-width: 0; }
.rbac-role-row__perms-inner { width: 100%; min-width: 0; }
.rbac-role-row__stats {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 4px;
}
.rbac-stat {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    background: #eef2ff;
    color: #3730a3;
    font-size: 11px;
    font-weight: 700;
}
.rbac-stat--soft {
    background: #f1f5f9;
    color: #475569;
}
.rbac-role-row__modules {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.rbac-more {
    display: inline-block;
    margin-left: 4px;
    padding: 0 6px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
}
.rbac-delete-form { margin: 0; display: inline-flex; }
.rbac-row-icon {
    width: 36px;
    height: 36px;
    border: 0;
    background: transparent;
    color: #7c3aed;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 8px;
    padding: 0;
    transition: background .15s ease, color .15s ease;
}
.rbac-row-icon .glyphicon {
    font-size: 15px;
    top: 0;
}
.rbac-row-icon:hover {
    background: #f3e8ff;
    color: #6d28d9;
}
.rbac-row-icon--danger { color: #a855f7; }
.rbac-row-icon--danger:hover {
    background: #fef2f2;
    color: #dc2626;
}
.rbac-row-icon:focus {
    outline: 2px solid #ddd6fe;
    outline-offset: 1px;
}
.rbac-view-sub {
    margin: 4px 0 0;
    font-size: 13px;
    font-weight: 400;
    color: #64748b;
}
.rbac-view-legend {
    display: flex;
    gap: 16px;
    margin: 0 0 10px;
    font-size: 12px;
    color: #64748b;
}
.rbac-view-legend i {
    display: inline-flex;
    width: 18px;
    height: 18px;
    border-radius: 5px;
    align-items: center;
    justify-content: center;
    font-style: normal;
    font-weight: 700;
    font-size: 11px;
    margin-right: 4px;
    vertical-align: middle;
}
.rbac-yes { background: #ecfdf5; color: #059669; }
.rbac-no { background: #f1f5f9; color: #94a3b8; }
.rbac-view-table tbody tr.rbac-view-empty td {
    color: #94a3b8;
    font-size: 12px;
}
.rbac-view-mark {
    display: inline-flex;
    width: 26px;
    height: 26px;
    border-radius: 7px;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
}
.rbac-view-mark.is-yes { background: #ecfdf5; color: #059669; }
.rbac-view-mark.is-no { background: #f8fafc; color: #cbd5e1; border: 1px solid #e2e8f0; }
.rbac-view-mod strong { display: block; color: #0f172a; }
.rbac-view-mod code { font-size: 11px; }
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
.rbac-modal--wide { width: min(860px, 100%); }
.rbac-crud-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
.rbac-crud-table { margin: 0; font-size: 13px; }
.rbac-crud-table th, .rbac-crud-table td { vertical-align: middle; }
.rbac-crud-col { text-align: center; width: 72px; white-space: nowrap; }
.rbac-crud-check { display: inline-flex; margin: 0; font-weight: 400; }
.rbac-crud-table code { display: block; font-size: 11px; }
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
.rbac-tag { display: inline-block; margin-left: 0; padding: 2px 8px; border-radius: 999px; background: #f1f5f9; font-size: 11px; color: #475569; font-weight: 600; }
.rbac-muted { color: #64748b; font-size: 12px; font-weight: 400; }
@media (max-width: 900px) {
    .rbac-check-grid { grid-template-columns: 1fr; }
    .rbac-role-table__head { display: none; }
    .rbac-role-table__head,
    .rbac-role-row {
        grid-template-columns: 1fr 48px 48px 48px;
        grid-template-areas:
            "who who who who"
            "perms view edit del";
    }
    .rbac-role-row {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin: 10px;
        overflow: hidden;
    }
    .rbac-role-row:last-child { margin-bottom: 10px; }
    .rbac-role-row__who { grid-area: who; border-right: 0 !important; border-bottom: 1px solid #eef2f7; }
    .rbac-role-row__perms { grid-area: perms; }
    .rbac-role-row__act--view { grid-area: view; }
    .rbac-role-row__act--edit { grid-area: edit; }
    .rbac-role-row__act--del { grid-area: del; }
    .rbac-role-row__desc { max-width: none; white-space: normal; }
    .rbac-role-row__modules { white-space: normal; }
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
    var flash = document.getElementById('rbac-flash');
    if (flash) {
        var closeFlash = function () {
            flash.classList.add('is-hiding');
            setTimeout(function () {
                if (flash && flash.parentNode) {
                    flash.parentNode.removeChild(flash);
                }
            }, 260);
        };
        var closeBtn = document.getElementById('rbac-flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeFlash);
        }
        setTimeout(closeFlash, 4200);
    }

    document.querySelectorAll('.rbac-delete-role').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            if (!form) {
                return;
            }
            var message = <?= json_encode(lang('Roles.delete_confirm')) ?>;
            var title = <?= json_encode(lang('Roles.delete_confirm_title')) ?>;
            var confirmLabel = <?= json_encode(lang('Common.delete')) ?>;

            if (typeof window.osposConfirm === 'function') {
                window.osposConfirm({
                    action: 'delete',
                    title: title,
                    message: message,
                    confirmLabel: confirmLabel
                }, function (confirmed) {
                    if (confirmed) {
                        form.submit();
                    }
                });
                return;
            }

            if (window.confirm(message)) {
                form.submit();
            }
        });
    });

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
        var moduleField = document.getElementById('permission_module');
        var actionField = document.getElementById('permission_action');
        var hint = document.getElementById('rbac-code-hint');
        var formatHint = document.getElementById('rbac-code-format');
        var builder = document.getElementById('rbac-perm-builder');
        var newTitle = <?= json_encode(lang('Roles.new_permission')) ?>;
        var editTitle = <?= json_encode(lang('Roles.editing_permission')) ?>;
        var actionLabels = {
            create: <?= json_encode(lang('Roles.action_create')) ?>,
            read: <?= json_encode(lang('Roles.action_read')) ?>,
            update: <?= json_encode(lang('Roles.action_update')) ?>,
            delete: <?= json_encode(lang('Roles.action_delete')) ?>
        };

        function buildCodeFromSelects() {
            if (!moduleField || !actionField || codeField.readOnly) {
                return;
            }
            var module = (moduleField.value || '').trim();
            var action = (actionField.value || '').trim();
            if (!module || !action) {
                return;
            }
            codeField.value = module + '.' + action;
            var moduleLabel = moduleField.options[moduleField.selectedIndex]
                ? moduleField.options[moduleField.selectedIndex].text.replace(/\s*\(.*\)$/, '')
                : module;
            nameField.value = moduleLabel + ' - ' + (actionLabels[action] || action);
        }

        if (moduleField) moduleField.addEventListener('change', buildCodeFromSelects);
        if (actionField) actionField.addEventListener('change', buildCodeFromSelects);

        function fillForm(data) {
            idField.value = data.id || '0';
            codeField.value = data.code || '';
            nameField.value = data.name || '';
            descField.value = data.description || '';
            var isSystem = data.system === '1' || data.system === 1;
            codeField.readOnly = isSystem;
            hint.hidden = !isSystem;
            if (formatHint) formatHint.hidden = isSystem;
            if (builder) {
                builder.querySelectorAll('#permission_module, #permission_action').forEach(function (el) {
                    el.disabled = isSystem;
                });
            }
            if (moduleField && actionField && data.code) {
                var parts = String(data.code).split('.');
                if (parts.length === 2) {
                    moduleField.value = parts[0];
                    actionField.value = parts[1];
                }
            }
            title.textContent = data.id && data.id !== '0' ? (editTitle + ': ' + (data.name || '')) : newTitle;
        }

        var newBtn = document.getElementById('rbac-new-permission');
        if (newBtn) newBtn.addEventListener('click', function () {
            fillForm({ id: '0', code: '', name: '', description: '', system: '0' });
            codeField.readOnly = false;
            hint.hidden = true;
            if (formatHint) formatHint.hidden = false;
            if (moduleField) { moduleField.disabled = false; moduleField.value = 'sales'; }
            if (actionField) { actionField.disabled = false; actionField.value = 'create'; }
            buildCodeFromSelects();
            title.textContent = newTitle;
            permUi.openModal();
            setTimeout(function () { if (moduleField) moduleField.focus(); }, 50);
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

    var permissionMatrix = <?= json_encode(array_values(array_map(static function (array $row): array {
        $actions = [];
        foreach (['create', 'read', 'update', 'delete'] as $action) {
            if (!empty($row['actions'][$action]['id'])) {
                $actions[$action] = (int)$row['actions'][$action]['id'];
            }
        }

        return [
            'module'  => (string)$row['module'],
            'name'    => (string)$row['name'],
            'actions' => $actions,
        ];
    }, $permission_matrix)), JSON_UNESCAPED_UNICODE) ?>;

    var roleOverlay = document.getElementById('rbac-role-modal');
    var roleUi = bindOverlay(roleOverlay);
    var fillRole = null;

    if (roleOverlay && roleUi) {
        var roleTitle = document.getElementById('rbac-role-title');
        var roleIdField = document.getElementById('role_id');
        var roleNameField = document.getElementById('role_name');
        var roleDescField = document.getElementById('role_description');
        var roleBoxes = roleOverlay.querySelectorAll('.rbac-role-perm');
        var newRoleTitle = <?= json_encode(lang('Roles.new_role')) ?>;
        var editRoleTitle = <?= json_encode(lang('Roles.editing_role')) ?>;

        fillRole = function (data) {
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
        };

        roleOverlay.querySelectorAll('.rbac-row-all').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var module = btn.getAttribute('data-module');
                var boxes = roleOverlay.querySelectorAll('.rbac-role-perm[data-module="' + module + '"]');
                var allOn = true;
                boxes.forEach(function (box) {
                    if (!box.checked) allOn = false;
                });
                boxes.forEach(function (box) {
                    box.checked = !allOn;
                });
            });
        });

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

    var viewOverlay = document.getElementById('rbac-view-modal');
    var viewUi = bindOverlay(viewOverlay);
    if (viewOverlay && viewUi) {
        var viewTitle = document.getElementById('rbac-view-title');
        var viewSub = document.getElementById('rbac-view-sub');
        var viewBody = document.getElementById('rbac-view-body');
        var viewEditBtn = document.getElementById('rbac-view-edit');
        var viewRoleTitle = <?= json_encode(lang('Roles.view_role')) ?>;
        var currentViewData = null;

        function mark(on) {
            return on
                ? '<span class="rbac-view-mark is-yes" title="Yes">✓</span>'
                : '<span class="rbac-view-mark is-no" title="No">–</span>';
        }

        function renderView(data) {
            currentViewData = data;
            var selected = {};
            String(data.ids || '').split(',').forEach(function (id) {
                if (id) selected[String(id)] = true;
            });

            viewTitle.textContent = viewRoleTitle + ': ' + (data.name || '');
            viewSub.textContent = data.description || '';

            var html = '';
            var shown = 0;
            permissionMatrix.forEach(function (row) {
                var createId = row.actions.create ? String(row.actions.create) : '';
                var readId = row.actions.read ? String(row.actions.read) : '';
                var updateId = row.actions.update ? String(row.actions.update) : '';
                var deleteId = row.actions.delete ? String(row.actions.delete) : '';
                var c = createId && selected[createId];
                var r = readId && selected[readId];
                var u = updateId && selected[updateId];
                var d = deleteId && selected[deleteId];
                if (!(c || r || u || d)) {
                    return;
                }
                shown++;
                html += '<tr>'
                    + '<td class="rbac-view-mod"><strong>' + escapeHtml(row.name) + '</strong><code class="rbac-muted">' + escapeHtml(row.module) + '</code></td>'
                    + '<td class="rbac-crud-col">' + mark(!!c) + '</td>'
                    + '<td class="rbac-crud-col">' + mark(!!r) + '</td>'
                    + '<td class="rbac-crud-col">' + mark(!!u) + '</td>'
                    + '<td class="rbac-crud-col">' + mark(!!d) + '</td>'
                    + '</tr>';
            });

            if (!shown) {
                html = '<tr class="rbac-view-empty"><td colspan="5"><?= esc(lang('Roles.none_yet')) ?></td></tr>';
            }
            viewBody.innerHTML = html;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        document.querySelectorAll('.rbac-view-role').forEach(function (btn) {
            btn.addEventListener('click', function () {
                renderView(btn.dataset);
                viewUi.openModal();
            });
        });

        if (viewEditBtn) {
            viewEditBtn.addEventListener('click', function () {
                if (!currentViewData || !fillRole || !roleUi) {
                    return;
                }
                viewUi.closeModal();
                fillRole(currentViewData);
                roleUi.openModal();
            });
        }
    }
})();
</script>

<?= view('partial/footer') ?>
