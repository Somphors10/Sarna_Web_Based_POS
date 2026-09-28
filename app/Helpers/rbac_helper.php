<?php

/**
 * Shop RBAC: Admin creates permissions and roles in the database (not only in PHP).
 * Access is User → Role → Permission. Existing POS module grants stay in sync.
 */

function rbac_ensure(): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $db = db_connect();
    if (!$db->tableExists('permissions')) {
        return;
    }

    $forge = \Config\Database::forge();

    if (!$db->tableExists('rbac_permissions')) {
        $forge->addField([
            'permission_id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'permission_code' => ['type' => 'VARCHAR', 'constraint' => 64],
            'permission_name' => ['type' => 'VARCHAR', 'constraint' => 128],
            'description'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_system'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $forge->addKey('permission_id', true);
        $forge->addUniqueKey(['tenant_id', 'permission_code']);
        $forge->createTable('rbac_permissions', true);
    }

    if (!$db->tableExists('rbac_roles')) {
        $forge->addField([
            'role_id'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tenant_id'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'role_key'    => ['type' => 'VARCHAR', 'constraint' => 32],
            'role_name'   => ['type' => 'VARCHAR', 'constraint' => 64],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_system'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $forge->addKey('role_id', true);
        $forge->addUniqueKey(['tenant_id', 'role_key']);
        $forge->createTable('rbac_roles', true);
    }

    if (!$db->tableExists('rbac_role_permissions')) {
        $forge->addField([
            'role_id'       => ['type' => 'INT', 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'unsigned' => true],
            'tenant_id'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
        ]);
        $forge->addKey(['role_id', 'permission_id'], true);
        $forge->createTable('rbac_role_permissions', true);
    }

    if (!$db->tableExists('rbac_user_roles')) {
        $forge->addField([
            'person_id' => ['type' => 'INT', 'unsigned' => true],
            'role_id'   => ['type' => 'INT', 'unsigned' => true],
            'tenant_id' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
        ]);
        $forge->addKey('person_id', true);
        $forge->createTable('rbac_user_roles', true);
    }

    rbac_ensure_tenant_schema($db);
    rbac_register_module($db);
    rbac_seed_defaults($db);
    $ready = true;
}

function rbac_current_tenant_id(): int
{
    return (int)(session()->get('tenant_id') ?? 0);
}

function rbac_database_is_shared(): bool
{
    try {
        $name = (string)db_connect()->getDatabase();
        $platform = (string)(config('Database')->platform['database'] ?? '');

        return $name === '' || $platform === '' || strcasecmp($name, $platform) === 0;
    } catch (\Throwable $e) {
        return true;
    }
}

function rbac_ensure_tenant_schema($db): void
{
    foreach (['rbac_permissions', 'rbac_roles', 'rbac_role_permissions', 'rbac_user_roles'] as $table) {
        if (!$db->tableExists($table) || $db->fieldExists('tenant_id', $table)) {
            continue;
        }
        $full = $db->prefixTable($table);
        try {
            $db->query("ALTER TABLE `{$full}` ADD COLUMN `tenant_id` INT UNSIGNED NOT NULL DEFAULT 0");
        } catch (\Throwable $e) {
            log_message('error', 'RBAC tenant column failed on ' . $table . ': ' . $e->getMessage());
            continue;
        }
        try {
            $db->query("ALTER TABLE `{$full}` ADD INDEX `idx_{$table}_tenant` (`tenant_id`)");
        } catch (\Throwable $e) {
        }
    }

    rbac_replace_unique_index($db, 'rbac_permissions', 'permission_code', 'uk_rbac_permissions_tenant_code', ['tenant_id', 'permission_code']);
    rbac_replace_unique_index($db, 'rbac_roles', 'role_key', 'uk_rbac_roles_tenant_key', ['tenant_id', 'role_key']);
    try {
        rbac_backfill_role_tenants($db);
    } catch (\Throwable $e) {
        log_message('error', 'RBAC tenant backfill failed: ' . $e->getMessage());
    }
}

function rbac_replace_unique_index($db, string $table, string $legacy_column, string $new_name, array $columns): void
{
    if (!$db->tableExists($table) || !$db->fieldExists('tenant_id', $table)) {
        return;
    }

    $full = $db->prefixTable($table);
    $have_new = false;
    try {
        foreach ($db->query("SHOW INDEX FROM `{$full}`")->getResultArray() as $index) {
            $key = (string)($index['Key_name'] ?? '');
            if ($key === $new_name) {
                $have_new = true;
            }
            $non_unique = (int)($index['Non_unique'] ?? 1);
            $column = (string)($index['Column_name'] ?? '');
            if ($key !== 'PRIMARY' && $key !== $new_name && $non_unique === 0 && $column === $legacy_column) {
                try {
                    $db->query("ALTER TABLE `{$full}` DROP INDEX `{$key}`");
                } catch (\Throwable $e) {
                }
            }
        }
    } catch (\Throwable $e) {
        return;
    }

    if ($have_new) {
        return;
    }

    $quoted = '`' . implode('`, `', $columns) . '`';
    try {
        $db->query("ALTER TABLE `{$full}` ADD UNIQUE INDEX `{$new_name}` ({$quoted})");
    } catch (\Throwable $e) {
    }
}

function rbac_known_tenant_ids($db): array
{
    $ids = [];
    try {
        $platform = db_connect('platform');
        if ($platform->tableExists('tenants')) {
            $rows = $platform->table('tenants')
                ->select('tenant_id')
                ->where('tenant_code !=', 'platform')
                ->get()
                ->getResultArray();
            foreach ($rows as $row) {
                $id = (int)($row['tenant_id'] ?? 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }
    } catch (\Throwable $e) {
    }

    if ($ids === [] && $db->tableExists('employees') && $db->fieldExists('tenant_id', 'employees')) {
        foreach ($db->table('employees')->select('tenant_id')->distinct()->get()->getResultArray() as $row) {
            $id = (int)($row['tenant_id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }
    }

    $current = rbac_current_tenant_id();
    if ($current > 0 && !in_array($current, $ids, true)) {
        $ids[] = $current;
    }

    return array_values(array_unique($ids));
}

function rbac_oldest_tenant_id($db): int
{
    $ids = rbac_known_tenant_ids($db);
    if ($ids === []) {
        return rbac_current_tenant_id();
    }

    return min($ids);
}

function rbac_person_tenant_id($db, int $person_id): int
{
    if ($person_id <= 0 || !$db->tableExists('employees') || !$db->fieldExists('tenant_id', 'employees')) {
        return 0;
    }

    $row = $db->table('employees')->select('tenant_id')->where('person_id', $person_id)->get(1)->getRowArray();

    return (int)($row['tenant_id'] ?? 0);
}

function rbac_infer_role_tenant($db, int $role_id): int
{
    if ($role_id <= 0 || !$db->tableExists('rbac_user_roles')) {
        return 0;
    }

    $found = [];
    foreach ($db->table('rbac_user_roles')->select('person_id')->where('role_id', $role_id)->get()->getResultArray() as $row) {
        $tid = rbac_person_tenant_id($db, (int)$row['person_id']);
        if ($tid > 0) {
            $found[$tid] = true;
        }
    }

    $ids = array_map('intval', array_keys($found));
    if (count($ids) === 1) {
        return $ids[0];
    }

    return 0;
}

function rbac_backfill_role_tenants($db): void
{
    if (!$db->tableExists('rbac_roles') || !$db->fieldExists('tenant_id', 'rbac_roles')) {
        return;
    }

    $current = rbac_current_tenant_id();
    $shared = rbac_database_is_shared();

    foreach ($db->table('rbac_roles')->where('tenant_id', 0)->where('is_system', 0)->get()->getResultArray() as $role) {
        $role_id = (int)$role['role_id'];
        $tid = rbac_infer_role_tenant($db, $role_id);
        if ($tid <= 0) {
            $tid = $shared ? rbac_oldest_tenant_id($db) : $current;
        }
        if ($tid <= 0) {
            continue;
        }
        $db->table('rbac_roles')->where('role_id', $role_id)->update(['tenant_id' => $tid]);
        if ($db->tableExists('rbac_role_permissions') && $db->fieldExists('tenant_id', 'rbac_role_permissions')) {
            $db->table('rbac_role_permissions')->where('role_id', $role_id)->update(['tenant_id' => $tid]);
        }
    }

    $tenants = $shared ? rbac_known_tenant_ids($db) : ($current > 0 ? [$current] : []);
    foreach ($db->table('rbac_roles')->where('tenant_id', 0)->where('is_system', 1)->get()->getResultArray() as $role) {
        $old_id = (int)$role['role_id'];
        $perm_ids = [];
        if ($db->tableExists('rbac_role_permissions')) {
            foreach ($db->table('rbac_role_permissions')->select('permission_id')->where('role_id', $old_id)->get()->getResultArray() as $row) {
                $perm_ids[] = (int)$row['permission_id'];
            }
        }

        foreach ($tenants as $tid) {
            if ($tid <= 0) {
                continue;
            }
            $exists = $db->table('rbac_roles')
                ->where('tenant_id', $tid)
                ->where('role_key', $role['role_key'])
                ->get(1)
                ->getRowArray();
            if ($exists) {
                $new_id = (int)$exists['role_id'];
            } else {
                $copy = $role;
                unset($copy['role_id']);
                $copy['tenant_id'] = $tid;
                $db->table('rbac_roles')->insert($copy);
                $new_id = (int)$db->insertID();
                foreach ($perm_ids as $permission_id) {
                    $link = [
                        'role_id'       => $new_id,
                        'permission_id' => $permission_id,
                    ];
                    if ($db->fieldExists('tenant_id', 'rbac_role_permissions')) {
                        $link['tenant_id'] = $tid;
                    }
                    $db->table('rbac_role_permissions')->insert($link);
                }
            }

            if ($db->tableExists('rbac_user_roles')) {
                foreach ($db->table('rbac_user_roles')->where('role_id', $old_id)->get()->getResultArray() as $assign) {
                    $person_id = (int)$assign['person_id'];
                    if (rbac_person_tenant_id($db, $person_id) !== $tid) {
                        continue;
                    }
                    $update = ['role_id' => $new_id];
                    if ($db->fieldExists('tenant_id', 'rbac_user_roles')) {
                        $update['tenant_id'] = $tid;
                    }
                    $db->table('rbac_user_roles')->where('person_id', $person_id)->update($update);
                }
            }
        }

        if ($tenants !== []) {
            $db->table('rbac_role_permissions')->where('role_id', $old_id)->delete();
            $db->table('rbac_user_roles')->where('role_id', $old_id)->delete();
            $db->table('rbac_roles')->where('role_id', $old_id)->delete();
        }
    }

    if ($db->tableExists('rbac_permissions') && $db->fieldExists('tenant_id', 'rbac_permissions')) {
        foreach ($db->table('rbac_permissions')->where('tenant_id', 0)->where('is_system', 0)->get()->getResultArray() as $perm) {
            $tid = $shared ? rbac_oldest_tenant_id($db) : $current;
            if ($tid > 0) {
                $db->table('rbac_permissions')->where('permission_id', (int)$perm['permission_id'])->update(['tenant_id' => $tid]);
            }
        }
    }
}

function rbac_role_row(int $role_id): ?array
{
    if ($role_id <= 0) {
        return null;
    }

    $db = db_connect();
    $builder = $db->table('rbac_roles')->where('role_id', $role_id);
    $tid = rbac_current_tenant_id();
    if ($tid > 0 && $db->fieldExists('tenant_id', 'rbac_roles')) {
        $builder->where('tenant_id', $tid);
    }
    $row = $builder->get(1)->getRowArray();

    return $row ?: null;
}

function rbac_permission_row(int $permission_id): ?array
{
    if ($permission_id <= 0) {
        return null;
    }

    $db = db_connect();
    $builder = $db->table('rbac_permissions')->where('permission_id', $permission_id);
    $tid = rbac_current_tenant_id();
    if ($tid > 0 && $db->fieldExists('tenant_id', 'rbac_permissions')) {
        $builder->groupStart()
            ->where('tenant_id', $tid)
            ->orWhere('tenant_id', 0)
            ->groupEnd();
    }
    $row = $builder->get(1)->getRowArray();

    return $row ?: null;
}

function rbac_register_module($db): void
{
    if (!$db->tableExists('modules')) {
        return;
    }

    if ($db->table('modules')->where('module_id', 'roles')->countAllResults() === 0) {
        $db->table('modules')->insert([
            'module_id'      => 'roles',
            'name_lang_key'  => 'module_roles',
            'desc_lang_key'  => 'module_roles_desc',
            'sort'           => 85,
        ]);
    }

    if ($db->table('permissions')->where('permission_id', 'roles')->countAllResults() === 0) {
        $db->table('permissions')->insert([
            'permission_id' => 'roles',
            'module_id'     => 'roles',
            'location_id'   => null,
        ]);
    }

    if ($db->tableExists('permissions') && $db->table('permissions')->where('permission_id', 'office')->countAllResults() === 0) {
        $db->table('permissions')->insert([
            'permission_id' => 'office',
            'module_id'     => 'office',
            'location_id'   => null,
        ]);
    }

    $employee_ids = $db->table('grants')
        ->select('person_id')
        ->where('permission_id', 'employees')
        ->get()
        ->getResultArray();

    foreach ($employee_ids as $row) {
        $person_id = (int)$row['person_id'];
        if ($person_id <= 0) {
            continue;
        }
        if ($db->tableExists('rbac_user_roles') && $db->table('rbac_user_roles')->where('person_id', $person_id)->countAllResults() > 0) {
            continue;
        }
        if ($db->table('grants')->where(['person_id' => $person_id, 'permission_id' => 'roles'])->countAllResults() > 0) {
            continue;
        }
        $menu = function_exists('rbac_menu_group_for') ? rbac_menu_group_for('roles') : 'office';
        if ($db->fieldExists('menu_group', 'grants')) {
            $db->table('grants')->insert([
                'permission_id' => 'roles',
                'person_id'     => $person_id,
                'menu_group'    => $menu,
            ]);
        } else {
            $db->table('grants')->insert([
                'permission_id' => 'roles',
                'person_id'     => $person_id,
            ]);
        }
    }
}

function rbac_seed_defaults($db): void
{
    $system_codes = rbac_system_codes();
    $has_perm_tenant = $db->fieldExists('tenant_id', 'rbac_permissions');
    $has_role_tenant = $db->fieldExists('tenant_id', 'rbac_roles');
    $tenant_id = rbac_current_tenant_id();

    foreach ($system_codes as $code => $name) {
        $perm_q = $db->table('rbac_permissions')->where('permission_code', $code)->where('is_system', 1);
        if ($has_perm_tenant) {
            $perm_q->groupStart()->where('tenant_id', 0)->orWhere('tenant_id', $tenant_id)->groupEnd();
        }
        if ($perm_q->countAllResults() > 0) {
            continue;
        }
        $perm_data = [
            'permission_code' => $code,
            'permission_name' => $name,
            'description'     => 'POS module',
            'is_system'       => 1,
        ];
        if ($has_perm_tenant) {
            $perm_data['tenant_id'] = 0;
        }
        $db->table('rbac_permissions')->insert($perm_data);
    }

    if ($tenant_id <= 0) {
        rbac_ensure_admin_has_system_codes($db);

        return;
    }

    $roles = [
        'admin'   => ['Admin', 'Can use every shop module, including staff and roles.'],
        'cashier' => ['Cashier', 'Sell, look up customers, and use gift cards.'],
        'staff'   => ['Staff', 'Manage items and receiving only.'],
    ];

    foreach ($roles as $key => $info) {
        $role_q = $db->table('rbac_roles')->where('role_key', $key);
        if ($has_role_tenant) {
            $role_q->where('tenant_id', $tenant_id);
        }
        if ($role_q->countAllResults() > 0) {
            continue;
        }
        $role_data = [
            'role_key'    => $key,
            'role_name'   => $info[0],
            'description' => $info[1],
            'is_system'   => 1,
        ];
        if ($has_role_tenant) {
            $role_data['tenant_id'] = $tenant_id;
        }
        $db->table('rbac_roles')->insert($role_data);
    }

    $codes_by_role = [
        'admin'   => array_keys($system_codes),
        'cashier' => ['home', 'sales', 'customers', 'giftcards'],
        'staff'   => ['home', 'items', 'item_kits', 'receivings', 'suppliers'],
    ];

    $perm_q = $db->table('rbac_permissions')->where('is_system', 1);
    if ($has_perm_tenant) {
        $perm_q->groupStart()->where('tenant_id', 0)->orWhere('tenant_id', $tenant_id)->groupEnd();
    }
    $perm_map = [];
    foreach ($perm_q->get()->getResultArray() as $perm) {
        $perm_map[(string)$perm['permission_code']] = (int)$perm['permission_id'];
    }

    $role_q = $db->table('rbac_roles');
    if ($has_role_tenant) {
        $role_q->where('tenant_id', $tenant_id);
    }
    foreach ($role_q->get()->getResultArray() as $role) {
        $key = (string)$role['role_key'];
        if (!isset($codes_by_role[$key])) {
            continue;
        }
        $role_id = (int)$role['role_id'];
        if ($db->table('rbac_role_permissions')->where('role_id', $role_id)->countAllResults() > 0) {
            continue;
        }
        foreach ($codes_by_role[$key] as $code) {
            if (!isset($perm_map[$code])) {
                continue;
            }
            $link = [
                'role_id'       => $role_id,
                'permission_id' => $perm_map[$code],
            ];
            if ($db->fieldExists('tenant_id', 'rbac_role_permissions')) {
                $link['tenant_id'] = $tenant_id;
            }
            $db->table('rbac_role_permissions')->insert($link);
        }
    }

    rbac_ensure_admin_has_system_codes($db);
}

function rbac_system_codes(): array
{
    return [
        'home'                => 'Home',
        'sales'               => 'Sales',
        'customers'           => 'Customers',
        'items'               => 'Items',
        'item_kits'           => 'Item Kits',
        'suppliers'           => 'Suppliers',
        'receivings'          => 'Receiving',
        'employees'           => 'Employees',
        'roles'               => 'Roles & Permissions',
        'giftcards'           => 'Gift Cards',
        'expenses'            => 'Expenses',
        'expenses_categories' => 'Expense Categories',
        'cashups'             => 'Cashups',
        'reports'             => 'Reports',
        'config'              => 'Configuration',
        'attributes'          => 'Attributes',
        'taxes'               => 'Taxes',
    ];
}

function rbac_ensure_admin_has_system_codes($db): void
{
    $admin_q = $db->table('rbac_roles')->where('role_key', 'admin');
    $tenant_id = rbac_current_tenant_id();
    if ($tenant_id > 0 && $db->fieldExists('tenant_id', 'rbac_roles')) {
        $admin_q->where('tenant_id', $tenant_id);
    }
    $admin = $admin_q->get(1)->getRowArray();
    if (!$admin) {
        return;
    }

    $role_id = (int)$admin['role_id'];
    $perm_q = $db->table('rbac_permissions')->where('is_system', 1);
    if ($tenant_id > 0 && $db->fieldExists('tenant_id', 'rbac_permissions')) {
        $perm_q->groupStart()->where('tenant_id', 0)->orWhere('tenant_id', $tenant_id)->groupEnd();
    }
    $perm_map = [];
    foreach ($perm_q->get()->getResultArray() as $perm) {
        $perm_map[(string)$perm['permission_code']] = (int)$perm['permission_id'];
    }

    $have = [];
    foreach ($db->table('rbac_role_permissions')->select('permission_id')->where('role_id', $role_id)->get()->getResultArray() as $row) {
        $have[(int)$row['permission_id']] = true;
    }

    foreach (array_keys(rbac_system_codes()) as $code) {
        if (!isset($perm_map[$code]) || isset($have[$perm_map[$code]])) {
            continue;
        }
        $link = [
            'role_id'       => $role_id,
            'permission_id' => $perm_map[$code],
        ];
        if ($db->fieldExists('tenant_id', 'rbac_role_permissions')) {
            $link['tenant_id'] = $tenant_id;
        }
        $db->table('rbac_role_permissions')->insert($link);
    }
}

function rbac_permissions(): array
{
    rbac_ensure();
    $db = db_connect();
    $builder = $db->table('rbac_permissions')->orderBy('is_system', 'DESC')->orderBy('permission_code');
    $tid = rbac_current_tenant_id();
    if ($tid > 0 && $db->fieldExists('tenant_id', 'rbac_permissions')) {
        $builder->groupStart()
            ->where('tenant_id', $tid)
            ->orWhere('tenant_id', 0)
            ->groupEnd();
    }

    return $builder->get()->getResultArray();
}

function rbac_roles(): array
{
    rbac_ensure();
    $db = db_connect();
    $builder = $db->table('rbac_roles')->orderBy('is_system', 'DESC')->orderBy('role_id');
    $tid = rbac_current_tenant_id();
    if ($tid > 0 && $db->fieldExists('tenant_id', 'rbac_roles')) {
        $builder->where('tenant_id', $tid);
    }

    return $builder->get()->getResultArray();
}

function rbac_normalize_code(string $code): string
{
    $code = strtolower(trim($code));
    $code = preg_replace('/[^a-z0-9._-]/', '', $code) ?? '';

    return $code;
}

function rbac_save_permission(string $code, string $name, string $description = '', int $permission_id = 0): array
{
    rbac_ensure();
    $db = db_connect();
    $code = rbac_normalize_code($code);
    $name = trim($name);
    $description = trim($description);
    $tenant_id = rbac_current_tenant_id();

    if ($code === '' || $name === '') {
        return ['ok' => false, 'message' => 'Code and name are required.'];
    }
    if (strlen($code) < 3) {
        return ['ok' => false, 'message' => 'Code must be at least 3 characters.'];
    }

    $existing_q = $db->table('rbac_permissions')->where('permission_code', $code);
    if ($tenant_id > 0 && $db->fieldExists('tenant_id', 'rbac_permissions')) {
        $existing_q->groupStart()
            ->where('tenant_id', $tenant_id)
            ->orWhere('tenant_id', 0)
            ->groupEnd();
    }
    $existing = $existing_q->get(1)->getRowArray();
    if ($existing && (int)$existing['permission_id'] !== $permission_id) {
        return ['ok' => false, 'message' => 'This permission code already exists.'];
    }

    $data = [
        'permission_code' => $code,
        'permission_name' => $name,
        'description'     => $description !== '' ? $description : null,
    ];

    if ($permission_id > 0) {
        $row = rbac_permission_row($permission_id);
        if (!$row || ((int)($row['tenant_id'] ?? 0) !== $tenant_id && (int)($row['is_system'] ?? 0) !== 1 && $tenant_id > 0)) {
            return ['ok' => false, 'message' => 'Permission not found.'];
        }
        if ((int)$row['is_system'] === 1) {
            $data['permission_code'] = (string)$row['permission_code'];
        }
        $db->table('rbac_permissions')->where('permission_id', $permission_id)->update($data);
    } else {
        $data['is_system'] = 0;
        if ($db->fieldExists('tenant_id', 'rbac_permissions')) {
            $data['tenant_id'] = $tenant_id;
        }
        $db->table('rbac_permissions')->insert($data);
        if ($db->tableExists('permissions') && $db->table('permissions')->where('permission_id', $code)->countAllResults() === 0) {
            $db->table('permissions')->insert([
                'permission_id' => $code,
                'module_id'     => 'roles',
                'location_id'   => null,
            ]);
        }
    }

    return ['ok' => true, 'message' => 'Permission saved.'];
}

function rbac_delete_permission(int $permission_id): array
{
    rbac_ensure();
    $db = db_connect();
    $row = rbac_permission_row($permission_id);
    $tenant_id = rbac_current_tenant_id();
    if (!$row || ($tenant_id > 0 && (int)($row['tenant_id'] ?? 0) !== $tenant_id)) {
        return ['ok' => false, 'message' => 'Permission not found.'];
    }
    if ((int)$row['is_system'] === 1) {
        return ['ok' => false, 'message' => 'System POS permissions cannot be deleted.'];
    }

    $db->table('rbac_role_permissions')->where('permission_id', $permission_id)->delete();
    $db->table('rbac_permissions')->where('permission_id', $permission_id)->delete();
    if ($db->tableExists('permissions')) {
        $db->table('permissions')->where('permission_id', $row['permission_code'])->where('module_id', 'roles')->delete();
    }

    return ['ok' => true, 'message' => 'Permission deleted.'];
}

function rbac_save_role(string $name, string $description, array $permission_ids, int $role_id = 0): array
{
    rbac_ensure();
    $db = db_connect();
    $name = trim($name);
    $description = trim($description);
    $tenant_id = rbac_current_tenant_id();
    $allowed = [];
    foreach (rbac_permissions() as $permission) {
        $allowed[(int)$permission['permission_id']] = true;
    }
    $permission_ids = array_values(array_unique(array_filter(
        array_map('intval', $permission_ids),
        static fn(int $id): bool => $id > 0 && isset($allowed[$id])
    )));

    if ($name === '') {
        return ['ok' => false, 'message' => 'Role name is required.'];
    }
    if ($permission_ids === []) {
        return ['ok' => false, 'message' => 'Select at least one permission for this role.'];
    }

    if ($role_id > 0) {
        $row = rbac_role_row($role_id);
        if (!$row) {
            return ['ok' => false, 'message' => 'Role not found.'];
        }
        $db->table('rbac_roles')->where('role_id', $role_id)->update([
            'role_name'   => $name,
            'description' => $description !== '' ? $description : null,
        ]);
    } else {
        $key = rbac_normalize_code(str_replace(' ', '_', $name));
        if ($key === '') {
            $key = 'role';
        }
        $base = $key;
        $n = 2;
        while (true) {
            $key_q = $db->table('rbac_roles')->where('role_key', $key);
            if ($tenant_id > 0 && $db->fieldExists('tenant_id', 'rbac_roles')) {
                $key_q->where('tenant_id', $tenant_id);
            }
            if ($key_q->countAllResults() === 0) {
                break;
            }
            $key = $base . '_' . $n;
            $n++;
        }
        $insert = [
            'role_key'    => $key,
            'role_name'   => $name,
            'description' => $description !== '' ? $description : null,
            'is_system'   => 0,
        ];
        if ($db->fieldExists('tenant_id', 'rbac_roles')) {
            $insert['tenant_id'] = $tenant_id;
        }
        $db->table('rbac_roles')->insert($insert);
        $role_id = (int)$db->insertID();
    }

    $db->table('rbac_role_permissions')->where('role_id', $role_id)->delete();
    foreach ($permission_ids as $permission_id) {
        $link = [
            'role_id'       => $role_id,
            'permission_id' => $permission_id,
        ];
        if ($db->fieldExists('tenant_id', 'rbac_role_permissions')) {
            $link['tenant_id'] = $tenant_id;
        }
        $db->table('rbac_role_permissions')->insert($link);
    }

    rbac_resync_role_users($role_id);

    return ['ok' => true, 'message' => 'Role saved.', 'role_id' => $role_id];
}

function rbac_delete_role(int $role_id): array
{
    rbac_ensure();
    $db = db_connect();
    $row = rbac_role_row($role_id);
    if (!$row) {
        return ['ok' => false, 'message' => 'Role not found.'];
    }
    if ((int)$row['is_system'] === 1) {
        return ['ok' => false, 'message' => 'Admin, Cashier, and Staff cannot be deleted.'];
    }
    if ($db->table('rbac_user_roles')->where('role_id', $role_id)->countAllResults() > 0) {
        return ['ok' => false, 'message' => 'This role is still assigned to a user.'];
    }

    $db->table('rbac_role_permissions')->where('role_id', $role_id)->delete();
    $db->table('rbac_roles')->where('role_id', $role_id)->delete();

    return ['ok' => true, 'message' => 'Role deleted.'];
}

function rbac_role_permission_ids(int $role_id): array
{
    rbac_ensure();
    $rows = db_connect()->table('rbac_role_permissions')->select('permission_id')->where('role_id', $role_id)->get()->getResultArray();

    return array_map(static fn(array $row): int => (int)$row['permission_id'], $rows);
}

function rbac_role_permission_codes(int $role_id): array
{
    rbac_ensure();
    $rows = db_connect()->table('rbac_role_permissions AS rp')
        ->select('p.permission_code')
        ->join('rbac_permissions AS p', 'p.permission_id = rp.permission_id')
        ->where('rp.role_id', $role_id)
        ->get()
        ->getResultArray();

    return array_values(array_filter(array_map(static fn(array $row): string => (string)$row['permission_code'], $rows)));
}

function rbac_user_role(?int $person_id): ?array
{
    if ($person_id === null || $person_id <= 0) {
        return null;
    }
    rbac_ensure();
    $row = db_connect()->table('rbac_user_roles AS ur')
        ->select('r.*')
        ->join('rbac_roles AS r', 'r.role_id = ur.role_id')
        ->where('ur.person_id', $person_id);
    $db = db_connect();
    $tid = rbac_current_tenant_id();
    if ($tid > 0 && $db->fieldExists('tenant_id', 'rbac_roles')) {
        $row->where('r.tenant_id', $tid);
    }
    $row = $row->get(1)->getRowArray();

    return $row ?: null;
}

function rbac_set_user_role(int $person_id, int $role_id): bool
{
    if ($person_id <= 0 || $role_id <= 0) {
        return false;
    }
    rbac_ensure();
    $db = db_connect();
    if (rbac_role_row($role_id) === null) {
        return false;
    }

    $db->table('rbac_user_roles')->where('person_id', $person_id)->delete();
    $assign = [
        'person_id' => $person_id,
        'role_id'   => $role_id,
    ];
    if ($db->fieldExists('tenant_id', 'rbac_user_roles')) {
        $assign['tenant_id'] = rbac_current_tenant_id();
    }
    $db->table('rbac_user_roles')->insert($assign);
    rbac_write_person_grants($person_id, rbac_grants_from_role($role_id));

    return true;
}

function rbac_grants_from_role(int $role_id): array
{
    $codes = rbac_role_permission_codes($role_id);
    $grants = [];
    $seen = [];
    foreach ($codes as $code) {
        if ($code === '' || isset($seen[$code])) {
            continue;
        }
        $seen[$code] = true;
        $grants[] = [
            'permission_id' => $code,
            'menu_group'    => rbac_menu_group_for($code),
        ];
    }

    foreach (rbac_implied_permission_ids($codes) as $permission_id) {
        if ($permission_id === '' || isset($seen[$permission_id])) {
            continue;
        }
        $seen[$permission_id] = true;
        $grants[] = [
            'permission_id' => $permission_id,
            'menu_group'    => rbac_menu_group_for($permission_id),
        ];
    }

    if (!isset($seen['home'])) {
        $grants[] = [
            'permission_id' => 'home',
            'menu_group'    => 'home',
        ];
    }

    if (!isset($seen['office']) && rbac_role_has_office_module($codes)) {
        $grants[] = [
            'permission_id' => 'office',
            'menu_group'    => 'home',
        ];
    }

    return $grants;
}

function rbac_office_module_ids(): array
{
    return ['employees', 'roles', 'expenses_categories', 'config', 'attributes'];
}

function rbac_office_display_ids(): array
{
    return ['employees', 'roles', 'expenses_categories', 'config'];
}

function rbac_menu_group_for(string $code): string
{
    if ($code === 'office') {
        return 'home';
    }

    return in_array($code, rbac_office_module_ids(), true) ? 'office' : 'home';
}

function rbac_role_has_office_module(array $codes): bool
{
    foreach (rbac_office_module_ids() as $id) {
        if (in_array($id, $codes, true)) {
            return true;
        }
    }

    return false;
}

function rbac_implied_permission_ids(array $codes): array
{
    $db = db_connect();
    if ($codes === [] || !$db->tableExists('permissions')) {
        return [];
    }

    $implied = [];
    if (
        in_array('employees', $codes, true)
        || in_array('roles', $codes, true)
        || in_array('config', $codes, true)
    ) {
        $implied[] = 'expenses_categories';
    }

    $rows = $db->table('permissions')
        ->select('permission_id, module_id, location_id')
        ->whereIn('module_id', $codes)
        ->get()
        ->getResultArray();

    foreach ($rows as $row) {
        $permission_id = (string)$row['permission_id'];
        $module_id = (string)$row['module_id'];
        $is_stock = str_ends_with($permission_id, '_stock') || $row['location_id'] !== null;
        $is_report = $module_id === 'reports' && str_starts_with($permission_id, 'reports_');
        if ($is_stock || $is_report) {
            $implied[] = $permission_id;
        }
    }

    return array_values(array_unique($implied));
}

function rbac_user_has_code(int $person_id, string $permission_id): bool
{
    if ($person_id <= 0 || $permission_id === '') {
        return false;
    }
    $role = rbac_user_role($person_id);
    if ($role === null) {
        return false;
    }

    $codes = rbac_role_permission_codes((int)$role['role_id']);
    if (in_array($permission_id, $codes, true)) {
        return true;
    }
    if (in_array($permission_id, rbac_implied_permission_ids($codes), true)) {
        return true;
    }

    return false;
}

function rbac_admin_nav_codes(): array
{
    return ['employees', 'roles', 'expenses_categories', 'config', 'attributes'];
}

function rbac_person_is_tenant_owner(int $person_id): bool
{
    $tenant_id = (int)session()->get('tenant_id');
    if ($person_id <= 0 || $tenant_id <= 0) {
        return false;
    }

    try {
        $db = db_connect('platform');
        if (!$db->tableExists('tenant_logins')) {
            return false;
        }
        $row = $db->table('tenant_logins')
            ->where('tenant_id', $tenant_id)
            ->where('person_id', $person_id)
            ->where('is_owner', 1)
            ->get(1)
            ->getRowArray();

        return $row !== null;
    } catch (\Throwable $e) {
        return false;
    }
}

function rbac_person_needs_admin_nav(int $person_id, ?array $role): bool
{
    $role_key = strtolower((string)($role['role_key'] ?? ''));
    $role_name = strtolower((string)($role['role_name'] ?? ''));
    if (in_array($role_key, ['admin', 'owner', 'shop_owner'], true)
        || str_contains($role_name, 'admin')
        || str_contains($role_name, 'owner')
    ) {
        return true;
    }
    if (rbac_person_is_tenant_owner($person_id)) {
        return true;
    }

    return false;
}

function rbac_user_office_display_ids(int $person_id): array
{
    $display = function_exists('rbac_office_display_ids')
        ? rbac_office_display_ids()
        : ['employees', 'roles', 'expenses_categories', 'config'];
    if ($person_id <= 0) {
        return [];
    }

    try {
        rbac_sync_expenses_categories_access($person_id);
        $employee = model(\App\Models\Employee::class);
        $allowed = [];
        foreach ($display as $code) {
            if ($employee->has_grant($code, $person_id)) {
                $allowed[] = $code;
            }
        }

        return $allowed;
    } catch (\Throwable $e) {
        return [];
    }
}

function rbac_person_may_manage_expense_categories(int $person_id): bool
{
    if ($person_id <= 0) {
        return false;
    }

    $role = function_exists('rbac_user_role') ? rbac_user_role($person_id) : null;
    if (function_exists('rbac_person_needs_admin_nav') && rbac_person_needs_admin_nav($person_id, $role)) {
        return true;
    }

    $codes = $role !== null ? rbac_role_permission_codes((int)$role['role_id']) : [];
    foreach (['expenses_categories', 'employees', 'roles', 'config'] as $code) {
        if (in_array($code, $codes, true)) {
            return true;
        }
    }

    $db = db_connect();
    if (!$db->tableExists('grants')) {
        return false;
    }
    foreach (['employees', 'roles', 'config'] as $code) {
        if ($db->table('grants')->where(['person_id' => $person_id, 'permission_id' => $code])->countAllResults() > 0) {
            return true;
        }
    }

    return false;
}

function rbac_sync_expenses_categories_access(int $person_id): void
{
    if ($person_id <= 0) {
        return;
    }

    $db = db_connect();
    if (!$db->tableExists('permissions') || !$db->tableExists('grants')) {
        return;
    }

    if ($db->table('permissions')->where('permission_id', 'expenses_categories')->countAllResults() === 0) {
        $db->table('permissions')->insert([
            'permission_id' => 'expenses_categories',
            'module_id'     => 'expenses_categories',
            'location_id'   => null,
        ]);
    }
    if ($db->tableExists('modules') && $db->table('modules')->where('module_id', 'expenses_categories')->countAllResults() === 0) {
        $db->table('modules')->insert([
            'module_id'      => 'expenses_categories',
            'name_lang_key'  => 'module_expenses_categories',
            'desc_lang_key'  => 'module_expenses_categories_desc',
            'sort'           => 109,
        ]);
    }

    $allowed = rbac_person_may_manage_expense_categories($person_id);
    $has_row = $db->table('grants')->where(['person_id' => $person_id, 'permission_id' => 'expenses_categories'])->countAllResults() > 0;

    if ($allowed && !$has_row) {
        $data = [
            'permission_id' => 'expenses_categories',
            'person_id'     => $person_id,
        ];
        if ($db->fieldExists('menu_group', 'grants')) {
            $data['menu_group'] = function_exists('rbac_menu_group_for') ? rbac_menu_group_for('expenses_categories') : 'office';
        }
        $db->table('grants')->insert($data);
        return;
    }

    if (!$allowed && $has_row) {
        $db->table('grants')->where(['person_id' => $person_id, 'permission_id' => 'expenses_categories'])->delete();
        $db->table('grants')->where(['person_id' => $person_id, 'permission_id' => 'office'])->delete();
    }
}

function rbac_user_can_open_office(int $person_id): bool
{
    if ($person_id <= 0) {
        return false;
    }

    $role = function_exists('rbac_user_role') ? rbac_user_role($person_id) : null;
    if (rbac_person_needs_admin_nav($person_id, $role)) {
        return true;
    }

    return rbac_user_office_display_ids($person_id) !== [];
}

function rbac_merge_admin_nav_grants(array $grants): array
{
    $seen = [];
    foreach ($grants as $grant) {
        $seen[(string)($grant['permission_id'] ?? '')] = true;
    }
    foreach (rbac_admin_nav_codes() as $code) {
        if (isset($seen[$code])) {
            continue;
        }
        $grants[] = [
            'permission_id' => $code,
            'menu_group'    => rbac_menu_group_for($code),
        ];
        $seen[$code] = true;
    }
    if (!isset($seen['office'])) {
        $grants[] = [
            'permission_id' => 'office',
            'menu_group'    => 'home',
        ];
    }

    return $grants;
}

function rbac_ensure_admin_nav_grants(int $person_id): void
{
    $db = db_connect();
    if (!$db->tableExists('permissions') || !$db->tableExists('grants')) {
        return;
    }
    foreach (rbac_admin_nav_codes() as $code) {
        if ($db->table('permissions')->where('permission_id', $code)->countAllResults() === 0) {
            $db->table('permissions')->insert([
                'permission_id' => $code,
                'module_id'     => $code,
                'location_id'   => null,
            ]);
        }
        if ($db->tableExists('modules') && $db->table('modules')->where('module_id', $code)->countAllResults() === 0) {
            $db->table('modules')->insert([
                'module_id'      => $code,
                'name_lang_key'  => 'module_' . $code,
                'desc_lang_key'  => 'module_' . $code . '_desc',
                'sort'           => $code === 'config' ? 900 : 80,
            ]);
        }
    }
}

function rbac_sync_person_from_role(int $person_id): void
{
    $role = rbac_user_role($person_id);
    if ($role === null) {
        if (rbac_person_needs_admin_nav($person_id, null)) {
            rbac_ensure_admin_nav_grants($person_id);
            $existing = [];
            foreach (db_connect()->table('grants')->where('person_id', $person_id)->get()->getResultArray() as $row) {
                $existing[] = [
                    'permission_id' => (string)$row['permission_id'],
                    'menu_group'    => (string)($row['menu_group'] ?? 'home'),
                ];
            }
            rbac_write_person_grants($person_id, rbac_merge_admin_nav_grants($existing));
        }

        return;
    }

    rbac_ensure_admin_has_system_codes(db_connect());
    if (rbac_person_needs_admin_nav($person_id, $role)) {
        rbac_ensure_admin_nav_grants($person_id);
        $wanted = rbac_grants_from_role((int)$role['role_id']);
        $wanted = rbac_merge_admin_nav_grants($wanted);
    } else {
        $wanted = rbac_grants_from_role((int)$role['role_id']);
    }
    $wanted_keys = [];
    foreach ($wanted as $grant) {
        $wanted_keys[] = ($grant['permission_id'] ?? '') . ':' . ($grant['menu_group'] ?? 'home');
    }
    sort($wanted_keys);

    $have_keys = [];
    $have_rows = db_connect()->table('grants')
        ->select('permission_id, menu_group')
        ->where('person_id', $person_id)
        ->get()
        ->getResultArray();
    foreach ($have_rows as $row) {
        $have_keys[] = ($row['permission_id'] ?? '') . ':' . ($row['menu_group'] ?? 'home');
    }
    sort($have_keys);

    if ($wanted_keys === $have_keys) {
        return;
    }

    rbac_write_person_grants($person_id, $wanted);
}

function rbac_resync_role_users(int $role_id): void
{
    $db = db_connect();
    if (!$db->tableExists('grants') || !$db->tableExists('rbac_user_roles')) {
        return;
    }

    $people = $db->table('rbac_user_roles')->select('person_id')->where('role_id', $role_id)->get()->getResultArray();
    $grants = rbac_grants_from_role($role_id);
    foreach ($people as $row) {
        rbac_write_person_grants((int)$row['person_id'], $grants);
    }
}

function rbac_write_person_grants(int $person_id, array $grants): void
{
    if ($person_id <= 0) {
        return;
    }
    $db = db_connect();
    if (!$db->tableExists('grants')) {
        return;
    }

    $valid = [];
    if ($db->tableExists('permissions')) {
        foreach ($db->table('permissions')->select('permission_id')->get()->getResultArray() as $row) {
            $valid[(string)$row['permission_id']] = true;
        }
    }

    $db->transStart();
    $db->table('grants')->where('person_id', $person_id)->delete();
    foreach ($grants as $grant) {
        $permission_id = (string)($grant['permission_id'] ?? '');
        if ($permission_id === '' || ($valid !== [] && !isset($valid[$permission_id]))) {
            continue;
        }
        $data = [
            'permission_id' => $permission_id,
            'person_id'     => $person_id,
        ];
        if ($db->fieldExists('menu_group', 'grants')) {
            $data['menu_group'] = (string)($grant['menu_group'] ?? 'home');
        }
        $db->table('grants')->insert($data);
    }
    $db->transComplete();
}
