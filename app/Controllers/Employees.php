<?php

namespace App\Controllers;

/**
 *
 *
 * @property module module
 *
 */
class Employees extends Persons
{
    public function __construct()
    {
        parent::__construct('employees');

        try {
            $this->syncShopLogins();
        } catch (\Throwable $e) {
            log_message('error', 'Employee shop login sync failed: ' . $e->getMessage());
        }
    }

    private function syncShopLogins(): void
    {
        $tenant_id = (int)(session()->get('tenant_id') ?? 0);
        if ($tenant_id <= 0 || !class_exists(\App\Libraries\PlatformArchitecture::class)) {
            return;
        }

        (new \App\Libraries\PlatformArchitecture())->syncTenantEmployeeLogins($tenant_id);
    }

    /**
     * Returns employee table data rows. This will be called with AJAX.
     *
     * @return void
     */
    public function getSearch(): void
    {
        try {
            $search = (string)($this->request->getGet('search') ?? '');
            $limit  = (int)$this->request->getGet('limit');
            $offset = (int)$this->request->getGet('offset');
            $sort   = $this->sanitizeSortColumn(person_headers(), $this->request->getGet('sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS), 'people.person_id');
            $order  = strtolower((string)($this->request->getGet('order') ?? 'asc'));
            if (!in_array($order, ['asc', 'desc'], true)) {
                $order = 'asc';
            }

            $employees = $this->employee->search($search, $limit, $offset, $sort, $order, false, list_deleted_flag());
            $total_rows = $this->employee->get_found_rows($search, list_deleted_flag());

            $data_rows = [];
            foreach ($employees->getResult() as $person) {
                $data_rows[] = get_person_data_row($person);
            }

            echo json_encode(['total' => $total_rows, 'rows' => $data_rows]);
        } catch (\Throwable $e) {
            log_message('error', 'Employee search failed: ' . $e->getMessage());
            echo json_encode(['total' => 0, 'rows' => []]);
        }
    }

    /**
     * AJAX called function gives search suggestions based on what is being searched for.
     *
     * @return void
     */
    public function getSuggest(): void
    {
        $search = $this->request->getGet('term');
        $suggestions = $this->employee->get_search_suggestions($search, 25, true);

        echo json_encode($suggestions);
    }

    /**
     * @return void
     */
    public function suggest_search(): void
    {
        $search = $this->request->getPost('term');
        $suggestions = $this->employee->get_search_suggestions($search);

        echo json_encode($suggestions);
    }

    /**
     * Loads the employee edit form
     */
    public function getView(int $employee_id = NEW_ENTRY): void
    {
        $person_info = $this->employee->get_info($employee_id);
        foreach (get_object_vars($person_info) as $property => $value) {
            $person_info->$property = $value;
        }
        $data['person_info'] = $person_info;
        $data['employee_id'] = $employee_id;
        $data['rbac_roles'] = [];
        $data['selected_role_id'] = 0;
        $data['rbac_role_codes'] = [];
        try {
            helper('rbac');
            rbac_ensure();
            $data['rbac_roles'] = rbac_roles();
            $assigned = rbac_user_role((int)($person_info->person_id ?? 0));
            $data['selected_role_id'] = (int)($assigned['role_id'] ?? 0);
            foreach ($data['rbac_roles'] as $role) {
                $data['rbac_role_codes'][(int)$role['role_id']] = rbac_role_permission_codes((int)$role['role_id']);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Employee role list failed: ' . $e->getMessage());
        }

        $modules = [];
        foreach ($this->module->get_all_modules()->getResult() as $module) {
            if (in_array($module->module_id, hidden_ui_module_ids(), true)) {
                continue;
            }
            $module->grant = $this->employee->has_grant($module->module_id, $person_info->person_id);
            $module->menu_group = $this->employee->get_menu_group($module->module_id, $person_info->person_id);

            $modules[] = $module;
        }
        $data['all_modules'] = $modules;

        $permissions = [];
        foreach ($this->module->get_all_subpermissions()->getResult() as $permission) {    // TODO: subpermissions does not follow naming standards.
            if (in_array($permission->module_id, hidden_ui_module_ids(), true)) {
                continue;
            }
            $permission->permission_id = str_replace(' ', '_', $permission->permission_id);
            $permission->grant = $this->employee->has_grant($permission->permission_id, $person_info->person_id);

            $permissions[] = $permission;
        }
        $data['all_subpermissions'] = $permissions;

        echo view('employees/form', $data);
    }

    /**
     * Inserts/updates an employee
     */
    public function postSave(int $employee_id = NEW_ENTRY): void
    {
        $first_name = $this->request->getPost('first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);    // TODO: duplicated code
        $last_name = $this->request->getPost('last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = strtolower($this->request->getPost('email', FILTER_SANITIZE_EMAIL));

        // format first and last name properly
        $first_name = $this->nameize($first_name);
        $last_name = $this->nameize($last_name);

        $person_data = [
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'gender'       => $this->request->getPost('gender', FILTER_SANITIZE_NUMBER_INT),
            'email'        => $email,
            'phone_number' => $this->request->getPost('phone_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'address_1'    => $this->request->getPost('address_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'address_2'    => $this->request->getPost('address_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'city'         => $this->request->getPost('city', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'state'        => $this->request->getPost('state', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'zip'          => $this->request->getPost('zip', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'country'      => $this->request->getPost('country', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'comments'     => $this->request->getPost('comments', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ];

        $username = (string)$this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($this->employee->username_exists($employee_id === NEW_ENTRY ? 0 : $employee_id, $username)) {
            echo json_encode([
                'success' => false,
                'message' => lang('Employees.username_duplicate'),
                'id'      => $employee_id
            ]);

            return;
        }

        helper('rbac');
        rbac_ensure();
        $role_id = (int)$this->request->getPost('role_id');
        $grants_array = [];
        if ($role_id > 0) {
            $grants_array = rbac_grants_from_role($role_id);
        } else {
            foreach ($this->module->get_all_permissions()->getResult() as $permission) {
                if (in_array($permission->module_id, hidden_ui_module_ids(), true)) {
                    continue;
                }
                $grants = [];
                $grant = $this->request->getPost('grant_' . $permission->permission_id) != null ? $this->request->getPost('grant_' . $permission->permission_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';

                if ($grant == $permission->permission_id) {
                    $grants['permission_id'] = $permission->permission_id;
                    $grants['menu_group'] = $this->request->getPost('menu_group_' . $permission->permission_id) != null ? $this->request->getPost('menu_group_' . $permission->permission_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '--';
                    $grants_array[] = $grants;
                }
            }
        }

        // Password has been changed OR first time password set
        if (!empty($this->request->getPost('password')) && ENVIRONMENT != 'testing') {
            $plain_password = (string)$this->request->getPost('password');
            if (!is_strong_password($plain_password)) {
                echo json_encode([
                    'success' => false,
                    'message' => lang('Employees.password_strong'),
                    'id'      => $employee_id
                ]);

                return;
            }

            // Language field is hidden; new/updated employees use system language.
            $employee_data = [
                'username'      => $this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                'password'      => password_hash($plain_password, PASSWORD_DEFAULT),
                'hash_version'  => 2,
                'language_code' => '',
                'language'      => ''
            ];
        } else { // Password not changed
            $employee_data = [
                'username'      => $this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                'language_code' => '',
                'language'      => ''
            ];
        }

        if ($this->employee->save_employee($person_data, $employee_data, $grants_array, $employee_id)) {
            $saved_id = (int)($employee_data['person_id'] ?? $employee_id);
            if ($saved_id > 0 && $role_id > 0) {
                rbac_set_user_role($saved_id, $role_id);
            }
            // New employee
            if ($employee_id == NEW_ENTRY) {
                echo json_encode([
                    'success' => true,
                    'message' => lang('Employees.successful_adding') . ' ' . format_person_name($first_name, $last_name),
                    'id'      => $employee_data['person_id']
                ]);
            } else { // Existing employee
                echo json_encode([
                    'success' => true,
                    'message' => lang('Employees.successful_updating') . ' ' . format_person_name($first_name, $last_name),
                    'id'      => $employee_id
                ]);
            }
        } else { // Failure
            echo json_encode([
                'success' => false,
                'message' => lang('Employees.error_adding_updating') . ' ' . format_person_name($first_name, $last_name),
                'id'      => NEW_ENTRY
            ]);
        }
    }

    /**
     * This deletes employees from the employees table
     */
    public function postDelete(): void
    {
        $employees_to_delete = normalize_post_ids($this->request->getPost('ids'));

        if (empty($employees_to_delete)) {
            echo json_encode(['success' => false, 'message' => lang('Employees.cannot_be_deleted')]);
            return;
        }

        if ($this->employee->delete_list($employees_to_delete)) {
            echo json_encode([
                'success' => true,
                'message' => lang('Employees.successful_deleted') . ' ' . count($employees_to_delete) . ' ' . lang('Employees.one_or_multiple')
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => lang('Employees.cannot_be_deleted')]);
        }
    }

    /**
     * Restores hidden employees.
     */
    public function postRestore(): void
    {
        $employees_to_restore = normalize_post_ids($this->request->getPost('ids'));

        if (empty($employees_to_restore)) {
            echo json_encode(['success' => false, 'message' => lang('Common.cannot_be_restored')]);
            return;
        }

        json_soft_restore_result($this->employee->undelete_list($employees_to_restore), count($employees_to_restore), 'Employees');
    }

    /**
     * Checks an employee username against the database. Used in app\Views\employees\form.php
     *
     * @param $employee_id
     * @return void
     * @noinspection PhpUnused
     */
    public function getCheckUsername($employee_id): void
    {
        $exists = $this->employee->username_exists($employee_id, $this->request->getGet('username'));
        echo !$exists ? 'true' : 'false';
    }
}
