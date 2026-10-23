<?php
/**
 * Read-only employee detail (View details modal).
 *
 * @var object $person_info
 * @var array<string, mixed>|null $role
 * @var list<array{code:string,name:string,description:string}> $perm_details
 */
$perm_details = $perm_details ?? [];
$role_name = is_array($role) ? (string)($role['role_name'] ?? '') : '';
$full_name = trim((string)($person_info->first_name ?? '') . ' ' . (string)($person_info->last_name ?? ''));
?>
<div class="employee-detail">
    <div class="employee-detail__section">
        <h4 class="employee-detail__title"><?= esc(lang('Employees.basic_information')) ?></h4>
        <dl class="employee-detail__grid">
            <div>
                <dt><?= esc(lang('Common.first_name')) ?></dt>
                <dd><?= esc((string)($person_info->first_name ?? '—')) ?></dd>
            </div>
            <div>
                <dt><?= esc(lang('Common.last_name')) ?></dt>
                <dd><?= esc((string)($person_info->last_name ?? '—')) ?></dd>
            </div>
            <div>
                <dt><?= esc(lang('Common.email')) ?></dt>
                <dd><?= esc((string)($person_info->email ?? '—')) ?></dd>
            </div>
            <div>
                <dt><?= esc(lang('Common.phone_number')) ?></dt>
                <dd><?= esc((string)($person_info->phone_number ?? '—')) ?></dd>
            </div>
            <div>
                <dt><?= esc(lang('Employees.username')) ?></dt>
                <dd><?= esc((string)($person_info->username ?? '—')) ?></dd>
            </div>
        </dl>
    </div>

    <div class="employee-detail__section">
        <h4 class="employee-detail__title"><?= esc(lang('Employees.permission_info')) ?></h4>
        <p class="employee-detail__role">
            <strong><?= esc(lang('Roles.choose_role')) ?>:</strong>
            <?= $role_name !== '' ? esc($role_name) : '<span class="text-muted">' . esc(lang('Roles.none_yet')) . '</span>' ?>
        </p>
        <?php if ($full_name !== ''): ?>
            <p class="employee-detail__muted"><?= esc($full_name) ?></p>
        <?php endif; ?>

        <?php if ($perm_details === []): ?>
            <p class="employee-detail__muted"><?= esc(lang('Roles.none_yet')) ?></p>
        <?php else: ?>
            <table class="table table-condensed employee-detail__perms">
                <thead>
                    <tr>
                        <th><?= esc(lang('Roles.permission_code')) ?></th>
                        <th><?= esc(lang('Roles.permission_name')) ?></th>
                        <th><?= esc(lang('Roles.description')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perm_details as $perm): ?>
                        <tr>
                            <td><code><?= esc($perm['code']) ?></code></td>
                            <td><?= esc($perm['name']) ?></td>
                            <td><?= esc($perm['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.employee-detail { padding: 4px 2px 8px; color: #0f172a; }
.employee-detail__section { margin-bottom: 18px; }
.employee-detail__title {
    margin: 0 0 10px;
    font-size: 15px;
    font-weight: 700;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 6px;
}
.employee-detail__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 16px;
    margin: 0;
}
.employee-detail__grid dt {
    margin: 0;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #64748b;
}
.employee-detail__grid dd {
    margin: 2px 0 0;
    font-size: 14px;
    color: #0f172a;
}
.employee-detail__role { margin: 0 0 6px; font-size: 14px; }
.employee-detail__muted { color: #64748b; font-size: 13px; margin: 0 0 8px; }
.employee-detail__perms { margin: 0; font-size: 12px; }
.employee-detail__perms code { font-size: 11px; }
@media (max-width: 640px) {
    .employee-detail__grid { grid-template-columns: 1fr; }
}
</style>
