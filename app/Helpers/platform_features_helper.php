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
            'label' => 'Receiving',
            'description' => 'Receive stock from suppliers and add it to inventory.',
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
            'description' => 'Advanced tax codes, jurisdictions, categories, and rates (usually not needed for simple VAT).',
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
        // Advanced Taxes module is off by default; shops use item VAT / Config tax instead.
        $defaults[$id] = ($id !== 'taxes');
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

    // Platform-wide off applies to shops and Super Admin POS shell.
    if (!platform_feature_enabled($module_id)) {
        return false;
    }

    // Super Admin ignores per-plan limits (can still open enabled platform features).
    if (function_exists('is_platform_super_admin') && is_platform_super_admin()) {
        return true;
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

/**
 * Same sidebar icon path used by shop POS and Super Admin POS Features.
 * Prefers the round nav icons, then classic menubar icons.
 */
function pos_module_nav_icon(string $module_id): string
{
    $module_id = preg_replace('/[^a-z0-9_]/', '', strtolower($module_id)) ?: 'office';
    $candidates = [
        'images/nav/' . $module_id . '.svg',
        'images/menubar/' . $module_id . '.svg',
        'images/nav/office.svg',
        'images/menubar/office.svg',
    ];

    foreach ($candidates as $path) {
        if (is_file(FCPATH . $path)) {
            return $path;
        }
    }

    return 'images/nav/office.svg';
}

/**
 * KHQR card HTML — red bar, dashed line, QR image (no merchant name on the picture).
 */
function khqr_scan_card_markup(string $qr_url, string $wrapper_class = ''): string
{
    $url = esc($qr_url, 'attr');
    $extra = trim($wrapper_class) !== '' ? ' ' . esc(trim($wrapper_class), 'attr') : '';

    return '<div class="khqr-scan-card' . $extra . '">'
        . '<div class="khqr-scan-card__bar">KHQR</div>'
        . '<div class="khqr-scan-card__rule" aria-hidden="true"></div>'
        . '<div class="khqr-scan-card__code">'
        . '<img src="' . $url . '" alt="ABA KHQR" width="240" height="240" loading="lazy">'
        . '</div></div>';
}

/**
 * Module order from the POS modules table (same as shop sidebar).
 *
 * @return list<string>
 */
function pos_sidebar_module_order(): array
{
    $db = db_connect();
    if (!$db->tableExists('modules')) {
        return array_keys(platform_pos_features());
    }

    $module = model(\App\Models\Module::class);
    $module->ensure_module_catalog();

    $order = [];
    foreach ($module->get_all_modules()->getResult() as $row) {
        $order[] = (string)$row->module_id;
    }

    return $order;
}

function platform_features_for_view(): array
{
    $states = platform_feature_states();
    $features = platform_pos_features();
    $rows = [];

    foreach (pos_sidebar_module_order() as $id) {
        if (!array_key_exists($id, $features)) {
            continue;
        }

        $feature = $features[$id];
        $rows[] = [
            'id' => $id,
            'label' => $feature['label'],
            'description' => $feature['description'],
            'enabled' => !empty($states[$id]),
            'icon' => pos_module_nav_icon($id),
        ];
    }

    foreach ($features as $id => $feature) {
        if (in_array($id, pos_sidebar_module_order(), true)) {
            continue;
        }

        $rows[] = [
            'id' => $id,
            'label' => $feature['label'],
            'description' => $feature['description'],
            'enabled' => !empty($states[$id]),
            'icon' => pos_module_nav_icon($id),
        ];
    }

    return $rows;
}

function is_platform_super_admin(): bool
{
    return (int) session()->get('platform_admin_id') > 0;
}

/**
 * Profile dropdown header copy for the POS top bar (matches Super Admin card layout).
 *
 * @return array{display_name: string, username: string, email: string, role_label: string}
 */
function pos_profile_card_context(object $user_info): array
{
    $display_name = format_person_name(
        (string) ($user_info->first_name ?? ''),
        (string) ($user_info->last_name ?? '')
    );
    $username = trim((string) ($user_info->username ?? ''));
    $email = trim((string) ($user_info->email ?? ''));
    $role_label = 'Team Member';

    if (is_platform_super_admin()) {
        $platform_admin = model(\App\Models\Platform_admin::class);
        $admin = $platform_admin->get_logged_in_admin();
        if ($admin) {
            $display_name = trim((string) ($admin->full_name ?? '')) ?: 'Platform Super Admin';
            $username = trim((string) ($admin->username ?? $username));
            $email = trim((string) ($admin->email ?? $email));
        } else {
            $display_name = 'Platform Super Admin';
            $username = super_admin_pos_username();
        }
        $role_label = $platform_admin->is_owner() ? 'Platform Owner' : 'Platform Admin';
    } else {
        $tenant_id = (int) (session()->get('tenant_id') ?? 0);
        $person_id = (int) ($user_info->person_id ?? 0);
        if ($tenant_id > 0 && $person_id > 0) {
            $role_row = db_connect()->table('tenant_users')
                ->where('tenant_id', $tenant_id)
                ->where('person_id', $person_id)
                ->get(1)
                ->getRow();
            $roles = [
                'owner' => 'Shop Owner',
                'admin' => 'Shop Admin',
                'manager' => 'Manager',
                'cashier' => 'Cashier',
            ];
            if ($role_row && isset($roles[$role_row->tenant_role])) {
                $role_label = $roles[$role_row->tenant_role];
            }
        }
    }

    return [
        'display_name' => $display_name,
        'username' => $username,
        'email' => $email,
        'role_label' => $role_label,
    ];
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
    $features = platform_pos_features();
    $hidden = array_merge(['messages', 'migrate', 'office'], platform_disabled_feature_ids());
    $rows = [[
        'id' => 'home',
        'label' => 'Home',
        'icon' => pos_module_nav_icon('home'),
        'url' => site_url('home'),
    ]];

    foreach (pos_sidebar_module_order() as $module_id) {
        if ($module_id === 'home' || in_array($module_id, $hidden, true)) {
            continue;
        }

        if (!array_key_exists($module_id, $features)) {
            continue;
        }

        $feature = $features[$module_id];
        $rows[] = [
            'id' => $module_id,
            'label' => $feature['label'],
            'icon' => pos_module_nav_icon($module_id),
            'url' => site_url($module_id),
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
 * Days before period_end when shops see an in-POS renewal warning.
 */
function saas_subscription_warning_days(): int
{
    return 7;
}

/**
 * @return array{period_end:?string,days_left:?int,is_expired:bool,is_warning:bool,has_period:bool}
 */
function saas_tenant_subscription_info(int $tenant_id): array
{
    $empty = [
        'period_end' => null,
        'days_left' => null,
        'is_expired' => false,
        'is_warning' => false,
        'has_period' => false,
    ];

    if ($tenant_id <= 0) {
        return $empty;
    }

    try {
        $db = db_connect('platform');
        if (!$db->tableExists('subscriptions') || !$db->fieldExists('period_end', 'subscriptions')) {
            return $empty;
        }

        $row = $db->table('subscriptions')
            ->select('period_end')
            ->where('tenant_id', $tenant_id)
            ->orderBy('subscription_id', 'DESC')
            ->get(1)
            ->getRow();

        if ($row === null || trim((string)($row->period_end ?? '')) === '') {
            return $empty;
        }

        $end = strtotime((string)$row->period_end);
        if ($end === false) {
            return $empty;
        }

        $now = time();
        $is_expired = $end < $now;
        $days_left = (int)floor(($end - $now) / 86400);

        return [
            'period_end' => date('Y-m-d H:i:s', $end),
            'days_left' => $days_left,
            'is_expired' => $is_expired,
            'is_warning' => !$is_expired && $days_left <= saas_subscription_warning_days(),
            'has_period' => true,
        ];
    } catch (Throwable $e) {
        return $empty;
    }
}

/**
 * True when the shop may use POS (subscription period not past end, or no period configured).
 */
function saas_tenant_subscription_usable(int $tenant_id): bool
{
    $info = saas_tenant_subscription_info($tenant_id);
    if (!$info['has_period']) {
        return true;
    }

    return !$info['is_expired'];
}

/**
 * Whether tenant is currently paid and does not need renewal yet
 * (more than 7 days left). Used by pay/checkout "already paid".
 */
function saas_tenant_is_currently_paid(?object $tenant): bool
{
    if ($tenant === null) {
        return false;
    }

    $status = strtolower((string)($tenant->status ?? ''));
    if ($status !== 'active') {
        return false;
    }

    $tenant_id = (int)($tenant->tenant_id ?? 0);
    if ($tenant_id <= 0) {
        return false;
    }

    // Allow renew during warning window or after expiry.
    if (saas_tenant_needs_renewal($tenant_id)) {
        return false;
    }

    return saas_tenant_subscription_usable($tenant_id);
}

/**
 * True when the shop should renew now (expired, or within the 7-day warning window).
 */
function saas_tenant_needs_renewal(int $tenant_id): bool
{
    if ($tenant_id <= 0) {
        return false;
    }

    $info = saas_tenant_subscription_info($tenant_id);
    if (!$info['has_period']) {
        return false;
    }

    return !empty($info['is_expired']) || !empty($info['is_warning']);
}

/**
 * Extend subscription by N months from max(now, current period_end). Activates subscription row.
 */
function saas_extend_tenant_subscription(int $tenant_id, int $months = 1): void
{
    if ($tenant_id <= 0 || $months < 1) {
        return;
    }

    try {
        $db = db_connect('platform');
        if (!$db->tableExists('subscriptions')) {
            return;
        }

        $row = $db->table('subscriptions')
            ->where('tenant_id', $tenant_id)
            ->orderBy('subscription_id', 'DESC')
            ->get(1)
            ->getRow();

        $now = time();
        $base = $now;
        if ($row !== null && trim((string)($row->period_end ?? '')) !== '') {
            $current_end = strtotime((string)$row->period_end);
            if ($current_end !== false && $current_end > $base) {
                $base = $current_end;
            }
        }

        $period_start = date('Y-m-d H:i:s', $now);
        $period_end = date('Y-m-d H:i:s', strtotime('+' . $months . ' month', $base));

        $payload = [
            'status' => 'active',
            'period_start' => $period_start,
            'period_end' => $period_end,
        ];

        if ($db->fieldExists('expiry_mail_stage', 'subscriptions')) {
            $payload['expiry_mail_stage'] = null;
            $payload['expiry_mail_period_end'] = null;
        }

        if ($row !== null) {
            $db->table('subscriptions')
                ->where('subscription_id', (int)$row->subscription_id)
                ->update($payload);
        }
    } catch (Throwable $e) {
        // Ignore — activation still sets tenant status.
    }
}

/**
 * First payment: keep remaining period if still valid and not in warning.
 * Renew (warning or expired): add one month from max(now, period_end).
 */
function saas_activate_or_renew_subscription(int $tenant_id): void
{
    if ($tenant_id <= 0) {
        return;
    }

    $info = saas_tenant_subscription_info($tenant_id);
    if (!$info['has_period'] || !empty($info['is_expired']) || !empty($info['is_warning'])) {
        saas_extend_tenant_subscription($tenant_id, 1);

        return;
    }

    try {
        $db = db_connect('platform');
        if ($db->tableExists('subscriptions')) {
            $db->table('subscriptions')
                ->where('tenant_id', $tenant_id)
                ->update(['status' => 'active']);
        }
    } catch (Throwable $e) {
        // Ignore.
    }
}

function saas_format_period_end(?string $period_end): string
{
    $period_end = trim((string)$period_end);
    if ($period_end === '') {
        return '—';
    }

    $ts = strtotime($period_end);
    if ($ts === false) {
        return $period_end;
    }

    return date('d M Y', $ts);
}

/**
 * Request + expiry dates for a shop (Configuration Information tab).
 *
 * @return array{
 *   request_date:?string,
 *   expires_date:?string,
 *   request_label:string,
 *   expires_label:string,
 *   days_left:?int,
 *   is_expired:bool,
 *   is_warning:bool,
 *   has_data:bool
 * }
 */
function saas_shop_subscription_dates(int $tenant_id): array
{
    $empty = [
        'request_date' => null,
        'expires_date' => null,
        'request_label' => '—',
        'expires_label' => '—',
        'days_left' => null,
        'is_expired' => false,
        'is_warning' => false,
        'has_data' => false,
    ];

    if ($tenant_id <= 0) {
        return $empty;
    }

    try {
        $db = db_connect('platform');
        if (!$db->tableExists('tenants')) {
            return $empty;
        }

        $tenant = $db->table('tenants')
            ->select('tenant_code, created_at')
            ->where('tenant_id', $tenant_id)
            ->get(1)
            ->getRow();

        if ($tenant === null) {
            return $empty;
        }

        $request_date = trim((string)($tenant->created_at ?? ''));
        $tenant_code = strtolower(trim((string)($tenant->tenant_code ?? '')));

        if (
            $tenant_code !== ''
            && $db->tableExists('subscription_requests')
            && $db->fieldExists('created_at', 'subscription_requests')
        ) {
            $request = $db->table('subscription_requests')
                ->select('created_at')
                ->where('tenant_code', $tenant_code)
                ->orderBy('request_id', 'ASC')
                ->get(1)
                ->getRow();
            if ($request !== null && trim((string)($request->created_at ?? '')) !== '') {
                $request_date = (string)$request->created_at;
            }
        }

        $sub = saas_tenant_subscription_info($tenant_id);
        $expires_date = $sub['period_end'] ?? null;

        $has_data = ($request_date !== '' && $request_date !== null) || !empty($sub['has_period']);

        return [
            'request_date' => $request_date !== '' ? $request_date : null,
            'expires_date' => $expires_date,
            'request_label' => saas_format_period_end($request_date),
            'expires_label' => saas_format_period_end($expires_date),
            'days_left' => $sub['days_left'] ?? null,
            'is_expired' => !empty($sub['is_expired']),
            'is_warning' => !empty($sub['is_warning']),
            'has_data' => $has_data,
        ];
    } catch (Throwable $e) {
        return $empty;
    }
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

/**
 * Extract Super Admin rejection comment from subscription request notes.
 */
function saas_rejection_reason(?string $notes): string
{
    $notes = trim((string)$notes);
    if ($notes === '') {
        return '';
    }

    if (preg_match('/Rejected:\s*(.+)$/s', $notes, $matches)) {
        return trim($matches[1]);
    }

    return '';
}
