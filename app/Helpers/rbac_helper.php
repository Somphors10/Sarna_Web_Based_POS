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
            'permission_code' => ['type' => 'VARCHAR', 'constraint' => 64],
            'permission_name' => ['type' => 'VARCHAR', 'constraint' => 128],
            'description'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_system'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $forge->addKey('permission_id', true);
        $forge->addUniqueKey('permission_code');
        $forge->createTable('rbac_permissions', true);
    }

    if (!$db->tableExists('rbac_roles')) {
        $forge->addField([
            'role_id'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_key'    => ['type' => 'VARCHAR', 'constraint' => 32],
            'role_name'   => ['type' => 'VARCHAR', 'constraint' => 64],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_system'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $forge->addKey('role_id', true);
        $forge->addUniqueKey('role_key');
        $forge->createTable('rbac_roles', true);
    }

    if (!$db->tableExists('rbac_role_permissions')) {
        $forge->addField([
            'role_id'       => ['type' => 'INT', 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $forge->addKey(['role_id', 'permission_id'], true);
        $forge->createTable('rbac_role_permissions', true);
    }

    if (!$db->tableExists('rbac_user_roles')) {
        $forge->addField([
            'person_id' => ['type' => 'INT', 'unsigned' => true],
            'role_id'   => ['type' => 'INT', 'unsigned' => true],
        ]);
        $forge->addKey('person_id', true);
        $forge->createTable('rbac_user_roles', true);
    }

    rbac_register_module($db);
    rbac_seed_defaults($db);
    $ready = true;
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

    foreach ($system_codes as $code => $name) {
        if ($db->table('rbac_permissions')->where('permission_code', $code)->countAllResults() > 0) {
            continue;
        }
        $db->table('rbac_permissions')->insert([
            'permission_code' => $code,
            'permission_name' => $name,
            'description'     => 'POS module',
            'is_system'       => 1,
        ]);
    }

    $roles = [
        'admin'   => ['Admin', 'Can use every shop module, including staff and roles.'],
        'cashier' => ['Cashier', 'Sell, look up customers, and use gift cards.'],
        'staff'   => ['Staff', 'Manage items and receiving only.'],
    ];

    foreach ($roles as $key => $info) {
        if ($db->table('rbac_roles')->where('role_key', $key)->countAllResults() > 0) {
            continue;
        }
        $db->table('rbac_roles')->insert([
            'role_key'    => $key,
            'role_name'   => $info[0],
            'description' => $info[1],
            'is_system'   => 1,
        ]);
    }

    $codes_by_role = [
        'admin'   => array_keys($system_codes),
        'cashier' => ['home', 'sales', 'customers', 'giftcards'],
        'staff'   => ['home', 'items', 'item_kits', 'receivings', 'suppliers'],
    ];

    $perm_rows = $db->table('rbac_permissions')->get()->getResultArray();
    $perm_map = [];
    foreach ($perm_rows as $perm) {
        $perm_map[(string)$perm['permission_code']] = (int)$perm['permission_id'];
    }

    $role_rows = $db->table('rbac_roles')->get()->getResultArray();
    foreach ($role_rows as $role) {
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
            $db->table('rbac_role_permissions')->insert([
                'role_id'       => $role_id,
                'permission_id' => $perm_map[$code],
            ]);
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
    $admin = $db->table('rbac_roles')->where('role_key', 'admin')->get(1)->getRowArray();
    if (!$admin) {
        return;
    }

    $role_id = (int)$admin['role_id'];
    $perm_rows = $db->table('rbac_permissions')->get()->getResultArray();
    $perm_map = [];
    foreach ($perm_rows as $perm) {
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
        $db->table('rbac_role_permissions')->insert([
            'role_id'       => $role_id,
            'permission_id' => $perm_map[$code],
        ]);
    }
}

function rbac_permissions(): array
{
    rbac_ensure();
    return db_connect()->table('rbac_permissions')->orderBy('is_system', 'DESC')->orderBy('permission_code')->get()->getResultArray();
}

function rbac_roles(): array
{
    rbac_ensure();
    return db_connect()->table('rbac_roles')->orderBy('role_id')->get()->getResultArray();
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

    if ($code === '' || $name === '') {
        return ['ok' => false, 'message' => 'Code and name are required.'];
    }
    if (strlen($code) < 3) {
        return ['ok' => false, 'message' => 'Code must be at least 3 characters.'];
    }

    $existing = $db->table('rbac_permissions')->where('permission_code', $code)->get(1)->getRowArray();
    if ($existing && (int)$existing['permission_id'] !== $permission_id) {
        return ['ok' => false, 'message' => 'This permission code already exists.'];
    }

    $data = [
        'permission_code' => $code,
        'permission_name' => $name,
        'description'     => $description !== '' ? $description : null,
    ];

    if ($permission_id > 0) {
        $row = $db->table('rbac_permissions')->where('permission_id', $permission_id)->get(1)->getRowArray();
        if (!$row) {
            return ['ok' => false, 'message' => 'Permission not found.'];
        }
        if ((int)$row['is_system'] === 1) {
            $data['permission_code'] = (string)$row['permission_code'];
        }
        $db->table('rbac_permissions')->where('permission_id', $permission_id)->update($data);
    } else {
        $data['is_system'] = 0;
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
    $row = $db->table('rbac_permissions')->where('permission_id', $permission_id)->get(1)->getRowArray();
    if (!$row) {
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
    $permission_ids = array_values(array_unique(array_filter(array_map('intval', $permission_ids))));

    if ($name === '') {
        return ['ok' => false, 'message' => 'Role name is required.'];
    }
    if ($permission_ids === []) {
        return ['ok' => false, 'message' => 'Select at least one permission for this role.'];
    }

    if ($role_id > 0) {
        $row = $db->table('rbac_roles')->where('role_id', $role_id)->get(1)->getRowArray();
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
        while ($db->table('rbac_roles')->where('role_key', $key)->countAllResults() > 0) {
            $key = $base . '_' . $n;
            $n++;
        }
        $db->table('rbac_roles')->insert([
            'role_key'    => $key,
            'role_name'   => $name,
            'description' => $description !== '' ? $description : null,
            'is_system'   => 0,
        ]);
        $role_id = (int)$db->insertID();
    }

    $db->table('rbac_role_permissions')->where('role_id', $role_id)->delete();
    foreach ($permission_ids as $permission_id) {
        $db->table('rbac_role_permissions')->insert([
            'role_id'       => $role_id,
            'permission_id' => $permission_id,
        ]);
    }

    rbac_resync_role_users($role_id);

    return ['ok' => true, 'message' => 'Role saved.', 'role_id' => $role_id];
}

function rbac_delete_role(int $role_id): array
{
    rbac_ensure();
    $db = db_connect();
    $row = $db->table('rbac_roles')->where('role_id', $role_id)->get(1)->getRowArray();
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
        ->where('ur.person_id', $person_id)
        ->get(1)
        ->getRowArray();

    return $row ?: null;
}

function rbac_set_user_role(int $person_id, int $role_id): bool
{
    if ($person_id <= 0 || $role_id <= 0) {
        return false;
    }
    rbac_ensure();
    $db = db_connect();
    if ($db->table('rbac_roles')->where('role_id', $role_id)->countAllResults() === 0) {
        return false;
    }

    $db->table('rbac_user_roles')->where('person_id', $person_id)->delete();
    $db->table('rbac_user_roles')->insert([
        'person_id' => $person_id,
        'role_id'   => $role_id,
    ]);
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
    if (in_array('expenses', $codes, true)) {
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

    $ids = array_column(
        db_connect()->table('grants')->select('permission_id')->where('person_id', $person_id)->get()->getResultArray(),
        'permission_id'
    );
    $shop = ['sales', 'items', 'customers', 'suppliers', 'receivings', 'cashups'];
    $has_shop = count(array_intersect($shop, $ids)) >= 5;
    $has_staff = in_array('employees', $ids, true) || in_array('config', $ids, true);

    return $has_shop && !$has_staff;
}

function rbac_user_can_open_office(int $person_id): bool
{
    if ($person_id <= 0) {
        return false;
    }

    try {
        $employee = model(\App\Models\Employee::class);
        foreach (['employees', 'roles', 'expenses_categories', 'config'] as $code) {
            if ($employee->has_grant($code, $person_id)) {
                return true;
            }
        }
    } catch (\Throwable $e) {
    }

    $role = function_exists('rbac_user_role') ? rbac_user_role($person_id) : null;

    return rbac_person_needs_admin_nav($person_id, $role);
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
