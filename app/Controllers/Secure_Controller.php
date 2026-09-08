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
        (new TenantContext())->applyRuntimeConnection($tenant_id);

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
            !$this->employee->has_module_grant($module_id, $logged_in_employee_info->person_id)
            || (isset($submodule_id) && !$this->employee->has_module_grant($submodule_id, $logged_in_employee_info->person_id))
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
            $this->session->set('menu_group', $menu_group);
        } else {
            $this->session->set('menu_group', $menu_group);
        }

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
            foreach ($allowed_modules->getResult() as $module) {
                if (in_array($module->module_id, $hidden_modules, true)) {
                    continue;
                }

                $this->global_view_data['allowed_modules'][] = $module;
            }
        }

        $this->global_view_data += [
            'user_info'       => $logged_in_employee_info,
            'controller_name' => $module_id,
            'config'          => $config
        ];
        view('viewData', $this->global_view_data);
    }

    public function sanitizeSortColumn($headers, $field, $default): string
    {
        return $field != null && in_array($field, array_keys(array_merge(...$headers))) ? $field : $default;
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
