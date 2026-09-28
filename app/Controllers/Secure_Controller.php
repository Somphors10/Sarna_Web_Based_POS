<?php

namespace App\Controllers;

use App\Libraries\TenantContext;
use App\Models\Employee;
use App\Models\Module;

use CodeIgniter\Model;
use CodeIgniter\Session\Session;
use Config\OSPOS;
use Config\Services;
use Throwable;

/**
 * Controllers that are considered secure extend Secure_Controller, optionally a $module_id can
 * be set to also check if a user can access a particular module in the system.
 *
 * @property employee employee
 * @property module module
 * @property array global_view_data
 * @property session session
 *
 */
class Secure_Controller extends BaseController
{
    public array $global_view_data;
    protected Employee $employee;
    protected Module $module;
    protected Session $session;

    /**
     * @param string $module_id
     * @param string|null $submodule_id
     * @param string|null $menu_group
     */
    public function __construct(string $module_id = '', ?string $submodule_id = null, ?string $menu_group = null)
    {
        $this->session = session();

        try {
            $this->bootSecureController($module_id, $submodule_id, $menu_group);
        } catch (Throwable $e) {
            $dump = $e->getMessage() . PHP_EOL . $e->getFile() . ':' . $e->getLine() . PHP_EOL . PHP_EOL . $e->getTraceAsString();
            if (defined('FCPATH')) {
                @file_put_contents(FCPATH . 'last-crash.txt', $dump);
            }
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo $dump;
            exit;
        }
    }

    private function bootSecureController(string $module_id, ?string $submodule_id, ?string $menu_group): void
    {

        $bootstrap_tenant_id = (int)($this->session->get('tenant_id') ?? 0);
        if ($bootstrap_tenant_id > 0) {
            (new TenantContext())->applyRuntimeConnection($bootstrap_tenant_id);
        }

        $this->employee = model(Employee::class);
        $this->module = model(Module::class);
        $validation = Services::validation();

        if (function_exists('is_platform_super_admin') && is_platform_super_admin()) {
            refresh_super_admin_pos_session();
        }

        if (!$this->employee->is_logged_in()) {
            header("Location:" . base_url('login'));
            exit();
        }

        $logged_in_employee_info = $this->employee->get_logged_in_employee_info();
        if (!is_object($logged_in_employee_info) || empty($logged_in_employee_info->person_id)) {
            header('Location:' . base_url('login'));
            exit();
        }
        $is_super_admin = function_exists('is_platform_super_admin') && is_platform_super_admin();
        $tenant_id = (int)($this->session->get('tenant_id') ?? 0);
        if ($tenant_id <= 0) {
            $tenant_id = (int)($logged_in_employee_info->tenant_id ?? 0);
            if ($tenant_id <= 0) {
                try {
                    $platform_db = db_connect('platform');
                    if ($platform_db->tableExists('tenants')) {
                        $tenant_row = $platform_db
                            ->table('tenants')
                            ->select('tenant_id')
                            ->where('tenant_code', 'default')
                            ->get(1)
                            ->getRow();
                        $tenant_id = (int)($tenant_row->tenant_id ?? 1);
                    } else {
                        $tenant_id = 1;
                    }
                } catch (Throwable $e) {
                    $tenant_id = 1;
                }
            }
            $this->session->set('tenant_id', $tenant_id);
        }

        // Mid-session: warn by email (Gmail); expired shops stay in view-only mode.
        if (!$is_super_admin && $tenant_id > 0) {
            if (
                function_exists('saas_tenant_needs_renewal')
                && saas_tenant_needs_renewal($tenant_id)
            ) {
                try {
                    (new \App\Libraries\SubscriptionExpiryNotifier())->notifyTenant($tenant_id);
                } catch (Throwable $e) {
                    log_message('error', 'Expiry email on POS load failed: ' . $e->getMessage());
                }
            }

            $this->enforceSubscriptionViewOnly($tenant_id);
        }

        (new TenantContext())->applyRuntimeConnection($tenant_id);

        helper('rbac');
        $person_id = (int)($logged_in_employee_info->person_id ?? 0);
        if ($person_id > 0 && function_exists('rbac_sync_person_from_role')) {
            try {
                rbac_sync_person_from_role($person_id);
            } catch (Throwable $e) {
                log_message('error', 'RBAC grant sync failed: ' . $e->getMessage());
            }
        }

        // After tenant is known: drop inherited demo email / other-shop logos.
        try {
            $appconfig = model(\App\Models\Appconfig::class);
            $email_fixed = $appconfig->repairPlaceholderShopEmail();
            $logo_fixed = $appconfig->repairInheritedCompanyLogo();
            if ($email_fixed !== null || $logo_fixed) {
                config(OSPOS::class)->update_settings();
            }
        } catch (Throwable $e) {
            log_message('error', 'Shop profile repair failed: ' . $e->getMessage());
        }

        $config = config(OSPOS::class)->settings;
        $logo = trim((string)($config['company_logo'] ?? ''));
        $prefix = 'tenants/' . $tenant_id . '/company_logo.';
        if ($logo !== '' && !str_starts_with($logo, $prefix)) {
            $config['company_logo'] = '';
        }

        if (
            !$this->employee->has_module_grant($module_id, (int)$logged_in_employee_info->person_id)
            || (isset($submodule_id) && !$this->employee->has_module_grant($submodule_id, (int)$logged_in_employee_info->person_id))
        ) {
            header("Location:" . base_url("no_access/$module_id/$submodule_id"));
            exit();
        }

        if (
            $module_id !== ''
            && function_exists('tenant_feature_enabled')
            && !tenant_feature_enabled($module_id)
        ) {
            header("Location:" . base_url("no_access/$module_id/$submodule_id"));
            exit();
        }

        // Load up global global_view_data visible to all the loaded views
        if ($menu_group == null) {
            $menu_group = $this->session->get('menu_group') ?: 'home';
        }
        $office_ids = function_exists('rbac_office_module_ids')
            ? rbac_office_module_ids()
            : ['employees', 'roles', 'expenses_categories', 'config'];
        if ($module_id === 'office' || in_array($module_id, $office_ids, true)) {
            $menu_group = 'office';
        } elseif ($module_id === 'home') {
            $menu_group = 'home';
        }
        $this->session->set('menu_group', $menu_group);

        $allowed_modules = $menu_group == 'home'
            ? $this->module->get_allowed_home_modules($logged_in_employee_info->person_id)
            : $this->module->get_allowed_office_modules($logged_in_employee_info->person_id);

        $this->global_view_data = [];
        $this->global_view_data['allowed_modules'] = [];
        $hidden_modules = $is_super_admin
            ? array_values(array_unique(array_merge(
                ['messages', 'migrate', 'office'],
                function_exists('platform_disabled_feature_ids') ? platform_disabled_feature_ids() : []
            )))
            : hidden_ui_module_ids();

        if ($is_super_admin) {
            $seen_modules = [];
            $super_admin_modules = array_merge(
                $this->module->get_allowed_home_modules($logged_in_employee_info->person_id)->getResult(),
                $this->module->get_allowed_office_modules($logged_in_employee_info->person_id)->getResult()
            );
            foreach ($super_admin_modules as $module) {
                if (isset($seen_modules[$module->module_id]) || in_array($module->module_id, $hidden_modules, true)) {
                    continue;
                }
                $seen_modules[$module->module_id] = true;
                $this->global_view_data['allowed_modules'][] = $module;
            }

            usort(
                $this->global_view_data['allowed_modules'],
                static fn($a, $b) => (int)($a->sort ?? 0) <=> (int)($b->sort ?? 0)
            );
        } else {
            $office_ids = function_exists('rbac_office_module_ids')
                ? rbac_office_module_ids()
                : ['employees', 'roles', 'expenses_categories', 'config'];
            $person_id = (int)$logged_in_employee_info->person_id;
            if (function_exists('rbac_sync_expenses_categories_access')) {
                rbac_sync_expenses_categories_access($person_id);
            }

            if ($menu_group === 'office') {
                $this->restoreOwnerOfficeGrants($person_id);
                $display_ids = function_exists('rbac_office_display_ids')
                    ? rbac_office_display_ids()
                    : ['employees', 'roles', 'expenses_categories', 'config'];
                $can_manage_categories = $this->employee->has_grant('employees', $person_id)
                    || $this->employee->has_grant('roles', $person_id)
                    || $this->employee->has_grant('config', $person_id);
                $seen_office = [];
                foreach ($display_ids as $office_id) {
                    if ($office_id === '' || isset($seen_office[$office_id])) {
                        continue;
                    }
                    $is_expense_categories = $office_id === 'expenses_categories';
                    if (!$is_expense_categories && in_array($office_id, $hidden_modules, true)) {
                        continue;
                    }
                    $allowed = $this->employee->has_grant($office_id, $person_id);
                    if ($is_expense_categories && $can_manage_categories) {
                        $allowed = true;
                    }
                    if (!$allowed) {
                        continue;
                    }
                    $seen_office[$office_id] = true;
                    $this->global_view_data['allowed_modules'][] = (object) [
                        'module_id'     => $office_id,
                        'name_lang_key' => 'module_' . $office_id,
                        'desc_lang_key' => 'module_' . $office_id . '_desc',
                        'sort'          => $is_expense_categories ? 90 : 80,
                    ];
                }
                $home_modules = $this->module->get_allowed_home_modules($person_id)->getResult();
                foreach ($home_modules as $home_module) {
                    if (($home_module->module_id ?? '') === 'home') {
                        array_unshift($this->global_view_data['allowed_modules'], $home_module);
                        break;
                    }
                }
            } else {
                foreach ($allowed_modules->getResult() as $module) {
                    if (
                        in_array($module->module_id, $hidden_modules, true)
                        || in_array($module->module_id, $office_ids, true)
                        || ($module->module_id ?? '') === 'office'
                    ) {
                        continue;
                    }
                    $this->global_view_data['allowed_modules'][] = $module;
                }
                $this->appendOfficeNavItem($person_id, $office_ids);
            }
        }

        $this->global_view_data += [
            'user_info'               => $logged_in_employee_info,
            'controller_name'         => $module_id !== '' ? $module_id : (function_exists('get_controller') ? get_controller() : ''),
            'config'                  => $config,
            'subscription_view_only'  => (bool)$this->session->get('subscription_view_only'),
            'show_office_nav'         => !$is_super_admin && $this->userCanOpenOffice((int)$logged_in_employee_info->person_id),
        ];
        view('viewData', $this->global_view_data);
    }

    private function userCanOpenOffice(int $person_id): bool
    {
        if (function_exists('rbac_user_can_open_office')) {
            return rbac_user_can_open_office($person_id);
        }

        foreach (['employees', 'roles', 'expenses_categories', 'config'] as $code) {
            if ($this->employee->has_grant($code, $person_id)) {
                return true;
            }
        }

        return false;
    }

    private function restoreOwnerOfficeGrants(int $person_id): void
    {
        try {
            $role = function_exists('rbac_user_role') ? rbac_user_role($person_id) : null;
            if (!function_exists('rbac_person_needs_admin_nav') || !rbac_person_needs_admin_nav($person_id, $role)) {
                return;
            }
            rbac_ensure_admin_nav_grants($person_id);
            $existing = [];
            foreach (db_connect()->table('grants')->where('person_id', $person_id)->get()->getResultArray() as $row) {
                $existing[] = [
                    'permission_id' => (string)$row['permission_id'],
                    'menu_group'    => (string)($row['menu_group'] ?? 'home'),
                ];
            }
            rbac_write_person_grants($person_id, rbac_merge_admin_nav_grants($existing));
        } catch (Throwable $e) {
            log_message('error', 'Office grants restore failed: ' . $e->getMessage());
        }
    }

    private function appendOfficeNavItem(int $person_id, array $office_ids): void
    {
        if (!$this->userCanOpenOffice($person_id)) {
            return;
        }

        $this->restoreOwnerOfficeGrants($person_id);

        foreach ($this->global_view_data['allowed_modules'] as $module) {
            if (($module->module_id ?? '') === 'office') {
                return;
            }
        }

        $office = null;
        try {
            $office = method_exists($this->module, 'get_office_module')
                ? $this->module->get_office_module()
                : null;
        } catch (Throwable $e) {
            $office = null;
        }
        if (!$office) {
            $office = (object) [
                'module_id'     => 'office',
                'name_lang_key' => 'module_office',
                'desc_lang_key' => 'module_office_desc',
                'sort'          => 999,
            ];
        }
        $this->global_view_data['allowed_modules'][] = $office;
    }

    /**
     * Expired shops may stay logged in to view data, but cannot mutate until they renew.
     */
    private function enforceSubscriptionViewOnly(int $tenant_id): void
    {
        if (!function_exists('saas_tenant_subscription_info')) {
            $this->session->set('subscription_view_only', false);

            return;
        }

        $info = saas_tenant_subscription_info($tenant_id);
        $view_only = !empty($info['is_expired']);
        $this->session->set('subscription_view_only', $view_only);

        if (!$view_only) {
            return;
        }

        $request = Services::request();
        $method = strtoupper((string)$request->getMethod(true));
        $segments = array_values(array_map('strtolower', $request->getUri()->getSegments()));
        if (($segments[0] ?? '') === 'index.php') {
            array_shift($segments);
        }

        $controller = (string)($segments[0] ?? '');
        $action = (string)($segments[1] ?? '');
        $path = implode('/', $segments);

        // Renew / pay (Saas is not Secure_Controller, but keep allowlist for safety),
        // logout, language switch, and access-denied pages.
        if (
            $controller === 'saas'
            || str_starts_with($path, 'home/logout')
            || str_starts_with($path, 'home/language')
            || $controller === 'no_access'
            || $controller === 'login'
        ) {
            return;
        }

        $mutating_get = false;
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            $mutating_needles = [
                'delete', 'remove', 'void', 'cancel', 'complete', 'suspend', 'restore',
                'save', 'update', 'clear', 'change_mode', 'changemode', 'setmode',
            ];
            foreach ($mutating_needles as $needle) {
                if ($action !== '' && str_contains($action, $needle)) {
                    $mutating_get = true;
                    break;
                }
            }

            if (!$mutating_get) {
                return;
            }
        }

        $message = lang('Login.subscription_view_only');
        if ($request->isAJAX()) {
            $response = Services::response();
            $response->setStatusCode(403);
            $response->setHeader('Content-Type', 'application/json; charset=UTF-8');
            $response->setBody(json_encode([
                'success' => false,
                'message' => $message,
            ]));
            $response->send();
            exit();
        }

        header('Location:' . base_url('home') . '?view_only=1');
        exit();
    }

    public function sanitizeSortColumn($headers, $field, $default): string
    {
        if (!is_array($headers) || $headers === []) {
            return (string)$default;
        }

        try {
            $allowed = array_keys(array_merge(...array_values($headers)));
        } catch (Throwable $e) {
            return (string)$default;
        }

        return $field != null && in_array($field, $allowed, true) ? (string)$field : (string)$default;
    }

    /**
     * AJAX function used to confirm whether values sent in the request are numeric
     * @return void
     * @noinspection PhpUnused
     */
    public function getCheckNumeric(): void
    {
        foreach ($this->request->getGet() as $value) {
            if (parse_decimals($value) === false) {
                echo 'false';
                return;
            }
        }
        echo 'true';
    }

    /**
     * @param $key
     * @return mixed|void
     */
    public function getConfig($key)
    {
        if (isset($config[$key])) {
            return $config[$key];
        }
    }

    /**
     * @return false
     */
    public function getIndex()
    {
        return false;
    }

    /**
     * @return false
     */
    public function getSearch()
    {
        return false;
    }

    /**
     * @return false
     */
    public function suggest_search()
    {
        return false;
    }

    /**
     * @param int $data_item_id
     * @return false
     */
    public function getView(int $data_item_id = -1)
    {
        return false;
    }

    /**
     * @param int $data_item_id
     * @return false
     */
    public function postSave(int $data_item_id = -1)
    {
        return false;
    }

    /**
     * @return false
     */
    public function postDelete()
    {
        return false;
    }
}
