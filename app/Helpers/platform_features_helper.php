<?php

/**
 * POS modules Super Admin can enable/disable for every subscription.
 * Shop data stays private; this only controls which features shops can use.
 */
function platform_pos_features(): array
{
    return [
        'sales' => [
            'label' => 'Sales',
            'description' => 'Process sales, payments, returns, and receipts at the register.',
        ],
        'customers' => [
            'label' => 'Customers',
            'description' => 'Add, edit, delete, and search customer records.',
        ],
        'items' => [
            'label' => 'Items',
            'description' => 'Add, edit, delete, and search products and stock.',
        ],
        'item_kits' => [
            'label' => 'Item Kits',
            'description' => 'Group products into kits for faster selling.',
        ],
        'suppliers' => [
            'label' => 'Suppliers',
            'description' => 'Add, edit, delete, and search suppliers.',
        ],
        'receivings' => [
            'label' => 'Purchase',
            'description' => 'Buy stock from suppliers and add it to inventory.',
        ],
        'employees' => [
            'label' => 'Employees',
            'description' => 'Add, edit, delete, and search store staff and permissions.',
        ],
        'giftcards' => [
            'label' => 'Gift Cards',
            'description' => 'Issue, search, and redeem gift cards.',
        ],
        'expenses' => [
            'label' => 'Expenses',
            'description' => 'Record and search store expenses.',
        ],
        'cashups' => [
            'label' => 'Cashups',
            'description' => 'Open and close cashier shifts and cash drawers.',
        ],
        'reports' => [
            'label' => 'Reports',
            'description' => 'View sales, inventory, and customer reports.',
        ],
        'taxes' => [
            'label' => 'Taxes',
            'description' => 'Configure tax codes, rates, and categories.',
        ],
        'config' => [
            'label' => 'Configuration',
            'description' => 'Store settings such as company name, receipt, and locale.',
        ],
        'attributes' => [
            'label' => 'Attributes',
            'description' => 'Extra item fields such as size, color, or brand.',
        ],
    ];
}

function platform_features_file(): string
{
    return WRITEPATH . 'cache/platform_features.json';
}

function platform_feature_states(): array
{
    $defaults = [];
    foreach (array_keys(platform_pos_features()) as $id) {
        $defaults[$id] = true;
    }

    $file = platform_features_file();
    if (!is_file($file)) {
        return $defaults;
    }

    $json = json_decode((string) file_get_contents($file), true);
    if (!is_array($json)) {
        return $defaults;
    }

    foreach ($defaults as $id => $enabled) {
        if (array_key_exists($id, $json)) {
            $defaults[$id] = (bool) $json[$id];
        }
    }

    return $defaults;
}

function platform_feature_enabled(string $module_id): bool
{
    $states = platform_feature_states();

    return array_key_exists($module_id, $states) ? (bool) $states[$module_id] : true;
}

function tenant_plan_id(): ?int
{
    $cached = session()->get('plan_id');
    if ($cached !== null && $cached !== false && $cached !== '') {
        return (int)$cached;
    }

    $tenant_id = (int)(session()->get('tenant_id') ?? 0);
    if ($tenant_id <= 0) {
        return null;
    }

    $plan_id = (new \App\Libraries\PlatformArchitecture())->getTenantPlanId($tenant_id);
    if ($plan_id !== null) {
        session()->set('plan_id', $plan_id);
    }

    return $plan_id;
}

function tenant_feature_enabled(string $module_id): bool
{
    if (!array_key_exists($module_id, platform_pos_features())) {
        return true;
    }

    if (function_exists('is_platform_super_admin') && is_platform_super_admin()) {
        return true;
    }

    if (!platform_feature_enabled($module_id)) {
        return false;
    }

    $plan_id = tenant_plan_id();
    if ($plan_id === null || $plan_id <= 0) {
        return true;
    }

    $enabled = (new \App\Libraries\PlatformArchitecture())->getEnabledFeatureIdsForPlan($plan_id);

    return in_array($module_id, $enabled, true);
}

function platform_disabled_feature_ids(): array
{
    $disabled = [];
    foreach (platform_feature_states() as $id => $enabled) {
        if (!$enabled) {
            $disabled[] = $id;
        }
    }

    return $disabled;
}

function tenant_disabled_feature_ids(): array
{
    $disabled = platform_disabled_feature_ids();
    if (function_exists('is_platform_super_admin') && is_platform_super_admin()) {
        return $disabled;
    }

    foreach (array_keys(platform_pos_features()) as $id) {
        if (!tenant_feature_enabled($id) && !in_array($id, $disabled, true)) {
            $disabled[] = $id;
        }
    }

    return $disabled;
}

function platform_set_feature_enabled(string $module_id, bool $enabled): bool
{
    if (!array_key_exists($module_id, platform_pos_features())) {
        return false;
    }

    $states = platform_feature_states();
    $states[$module_id] = $enabled;
    $file = platform_features_file();
    $dir = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    return file_put_contents($file, json_encode($states, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function platform_features_for_view(): array
{
    $states = platform_feature_states();
    $rows = [];
    foreach (platform_pos_features() as $id => $feature) {
        $icon = 'images/nav/' . $id . '.svg';
        $rows[] = [
            'id' => $id,
            'label' => $feature['label'],
            'description' => $feature['description'],
            'enabled' => !empty($states[$id]),
            'icon' => is_file(FCPATH . $icon) ? $icon : 'images/nav/office.svg',
        ];
    }

    return $rows;
}

function is_platform_super_admin(): bool
{
    return (int) session()->get('platform_admin_id') > 0;
}

function super_admin_pos_username(): string
{
    return 'wbpos_superadmin';
}

/**
 * POS modules Super Admin can open (same screens as store Admin).
 *
 * @return array<int, array{id: string, label: string, icon: string, url: string}>
 */
function super_admin_pos_nav_modules(): array
{
    $home_icon = is_file(FCPATH . 'images/nav/home.svg') ? 'images/nav/home.svg' : 'images/nav/office.svg';
    $rows = [[
        'id' => 'home',
        'label' => 'Home',
        'icon' => $home_icon,
        'url' => site_url('home'),
    ]];

    foreach (platform_features_for_view() as $feature) {
        $rows[] = [
            'id' => $feature['id'],
            'label' => $feature['label'],
            'icon' => $feature['icon'],
            'url' => site_url($feature['id']),
        ];
    }

    return $rows;
}

function super_admin_pos_workspace_code(): string
{
    return 'platform';
}

function super_admin_pos_workspace_tenant_id(): int
{
    $db = db_connect();
    if (!$db->tableExists('tenants')) {
        return 1;
    }

    $tenant = $db->table('tenants')->where('tenant_code', super_admin_pos_workspace_code())->get(1)->getRowArray();
    if ($tenant) {
        return (int) $tenant['tenant_id'];
    }

    $insert = [
        'tenant_code' => super_admin_pos_workspace_code(),
        'company_name' => 'WBPOS Platform',
        'status' => 'active',
        'timezone' => 'UTC',
        'currency_code' => 'USD',
    ];
    if ($db->fieldExists('db_name', 'tenants')) {
        $insert += [
            'db_hostname' => null,
            'db_port' => null,
            'db_name' => null,
            'db_username' => null,
            'db_password' => null,
            'db_prefix' => null,
        ];
    }

    $db->table('tenants')->insert($insert);
    $tenant_id = (int) $db->insertID();
    if ($tenant_id <= 0) {
        return 1;
    }

    (new \App\Libraries\TenantSeeder())->seedForTenant($tenant_id);

    if ($db->tableExists('tenant_config')) {
        $exists = $db->table('tenant_config')
            ->where('tenant_id', $tenant_id)
            ->where('config_key', 'company')
            ->countAllResults();
        if ($exists > 0) {
            $db->table('tenant_config')
                ->where('tenant_id', $tenant_id)
                ->where('config_key', 'company')
                ->update(['config_value' => 'WBPOS Platform']);
        } else {
            $db->table('tenant_config')->insert([
                'tenant_id' => $tenant_id,
                'config_key' => 'company',
                'config_value' => 'WBPOS Platform',
            ]);
        }
    }

    return $tenant_id;
}

function ensure_super_admin_pos_grants($db, int $person_id): void
{
    if ($db->table('grants')->where('person_id', $person_id)->countAllResults() > 0) {
        return;
    }

    if ($db->fieldExists('menu_group', 'grants')) {
        $sql = 'INSERT INTO ' . $db->prefixTable('grants') . ' (permission_id, person_id, menu_group)
                SELECT permission_id, ?, menu_group
                FROM ' . $db->prefixTable('grants') . '
                WHERE person_id = 1';
        $db->query($sql, [$person_id]);
    } else {
        $sql = 'INSERT INTO ' . $db->prefixTable('grants') . ' (permission_id, person_id)
                SELECT permission_id, ?
                FROM ' . $db->prefixTable('grants') . '
                WHERE person_id = 1';
        $db->query($sql, [$person_id]);
    }
}

/**
 * @return array{person_id: int, tenant_id: int}|null
 */
function ensure_super_admin_pos_identity(): ?array
{
    $db = db_connect();
    $username = super_admin_pos_username();
    $tenant_id = super_admin_pos_workspace_tenant_id();

    $employee = $db->table('employees')->where('username', $username)->get(1)->getRowArray();
    if ($employee) {
        $person_id = (int) $employee['person_id'];
        if ((int) ($employee['deleted'] ?? 0) === 1) {
            $db->table('employees')->where('person_id', $person_id)->update(['deleted' => 0]);
        }
        if ((int) ($employee['tenant_id'] ?? 0) !== $tenant_id) {
            $db->table('employees')->where('person_id', $person_id)->update(['tenant_id' => $tenant_id]);
            if ($db->fieldExists('tenant_id', 'people')) {
                $db->table('people')->where('person_id', $person_id)->update(['tenant_id' => $tenant_id]);
            }
        }
        ensure_super_admin_pos_grants($db, $person_id);

        return ['person_id' => $person_id, 'tenant_id' => $tenant_id];
    }

    $db->transStart();
    $db->table('people')->insert([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'gender' => null,
        'phone_number' => '',
        'email' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
        'comments' => 'Platform Super Admin POS identity',
        'tenant_id' => $tenant_id,
    ]);
    $person_id = (int) $db->insertID();
    $db->table('employees')->insert([
        'person_id' => $person_id,
        'username' => $username,
        'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
        'deleted' => 0,
        'hash_version' => 2,
        'tenant_id' => $tenant_id,
    ]);
    ensure_super_admin_pos_grants($db, $person_id);
    $db->transComplete();

    if (!$db->transStatus() || $person_id <= 0) {
        return null;
    }

    return ['person_id' => $person_id, 'tenant_id' => $tenant_id];
}

function login_super_admin_pos_session(): bool
{
    $identity = ensure_super_admin_pos_identity();
    if ($identity === null) {
        return false;
    }

    session()->set([
        'person_id' => $identity['person_id'],
        'tenant_id' => $identity['tenant_id'],
        'menu_group' => 'home',
        'super_admin_pos' => 1,
    ]);

    (new \App\Libraries\TenantContext())->bootstrapSessionTenantDatabase($identity['tenant_id']);
    model(\App\Models\Appconfig::class)->ensureCompleteConfig($identity['tenant_id']);
    config(\Config\OSPOS::class)->update_settings();

    return true;
}

function refresh_super_admin_pos_session(): bool
{
    if (
        (int) session()->get('person_id') > 0
        && (int) session()->get('tenant_id') === super_admin_pos_workspace_tenant_id()
    ) {
        return true;
    }

    return login_super_admin_pos_session();
}

function logout_super_admin_pos_session(): void
{
    session()->remove(['person_id', 'tenant_id', 'super_admin_pos', 'menu_group']);
    (new \App\Libraries\TenantContext())->clearTenantDatabaseSession();
}

function saas_monthly_price(?float $stored = null): float
{
    return 20.00;
}

/**
 * @return array<string, string>
 */
function saas_business_types(): array
{
    return [
        'retail' => 'Retail store',
        'grocery' => 'Grocery / Minimart',
        'fashion' => 'Fashion / Apparel',
        'electronics' => 'Electronics',
        'wholesale' => 'Wholesale',
        'services' => 'Services',
        'other' => 'Other',
    ];
}

/**
 * @return list<string>
 */
function saas_signup_countries(): array
{
    return [
        'Cambodia',
        'Vietnam',
        'Laos',
        'United States',
        'Other',
    ];
}

function saas_business_type_label(?string $type): string
{
    $types = saas_business_types() + [
        'restaurant' => 'Restaurant / Cafe',
        'pharmacy' => 'Pharmacy',
    ];
    $key = strtolower(trim((string)$type));

    return $types[$key] ?? (string)$type;
}
