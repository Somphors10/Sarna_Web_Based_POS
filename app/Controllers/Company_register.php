<?php

namespace App\Controllers;

use App\Models\Platform_admin;
use App\Models\Tenant;
use App\Libraries\TenantContext;
use CodeIgniter\HTTP\RedirectResponse;
use Config\OSPOS;

class Company_register extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $config = config(OSPOS::class)->settings;
        $validation = service('validation');
        $data = [
            'config' => $config,
            'validation' => $validation,
            'has_errors' => false
        ];

        if ($this->request->getMethod() !== 'POST') {
            return view('company_register', $data);
        }

        $rules = [
            'tenant_code' => 'required|alpha_dash|min_length[3]|max_length[50]',
            'company_name' => 'required|min_length[2]|max_length[255]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'username' => 'required|min_length[4]|max_length[50]',
            'password' => 'required|min_length[8]|max_length[255]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            $data['has_errors'] = true;
            return view('company_register', $data);
        }

        $tenant_code = strtolower((string)$this->request->getPost('tenant_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $company_name = (string)$this->request->getPost('company_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $first_name = (string)$this->request->getPost('first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = (string)$this->request->getPost('last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = (string)$this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = (string)$this->request->getPost('password');
        $email = (string)$this->request->getPost('email', FILTER_SANITIZE_EMAIL);

        $tenant_model = model(Tenant::class);
        $db = db_connect();

        if ($tenant_model->code_exists($tenant_code)) {
            $validation->setError('tenant_code', 'Company code already exists.');
            $data['has_errors'] = true;
            return view('company_register', $data);
        }

        $existing_user = $db->table('employees')->where('username', $username)->countAllResults();
        if ($existing_user > 0) {
            $validation->setError('username', 'Username already exists.');
            $data['has_errors'] = true;
            return view('company_register', $data);
        }

        $db->transStart();

        $tenant_insert = [
            'tenant_code' => $tenant_code,
            'company_name' => $company_name,
            'status' => 'active',
            'timezone' => 'UTC',
            'currency_code' => 'USD'
        ];

        if ($db->fieldExists('db_name', 'tenants')) {
            $tenant_insert += [
                'db_hostname' => null,
                'db_port' => null,
                'db_name' => null,
                'db_username' => null,
                'db_password' => null,
                'db_prefix' => null
            ];
        }

        $db->table('tenants')->insert($tenant_insert);
        $tenant_id = (int)$db->insertID();

        $db->table('people')->insert([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'gender' => null,
            'phone_number' => '',
            'email' => $email,
            'address_1' => '',
            'address_2' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'comments' => '',
            'tenant_id' => $tenant_id
        ]);
        $person_id = (int)$db->insertID();

        $db->table('employees')->insert([
            'person_id' => $person_id,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'deleted' => 0,
            'hash_version' => 2,
            'tenant_id' => $tenant_id
        ]);

        // Enforce one company owner admin at registration time.
        $owner_exists = $db->table('tenant_users')
            ->where('tenant_id', $tenant_id)
            ->where('tenant_role', 'owner')
            ->countAllResults();

        if ($owner_exists == 0) {
            $db->table('tenant_users')->insert([
                'tenant_id' => $tenant_id,
                'person_id' => $person_id,
                'tenant_role' => 'owner',
                'is_active' => 1
            ]);
        }

        // Clone all grants from system admin user (person_id=1) to new owner.
        $grants_builder = $db->table('grants');
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

        // Tenant config defaults
        $db->table('tenant_config')->insertBatch([
            ['tenant_id' => $tenant_id, 'config_key' => 'company', 'config_value' => $company_name],
            ['tenant_id' => $tenant_id, 'config_key' => 'timezone', 'config_value' => 'UTC'],
            ['tenant_id' => $tenant_id, 'config_key' => 'currency_code', 'config_value' => 'USD']
        ]);

        (new \App\Libraries\TenantSeeder())->seedForTenant($tenant_id);

        $db->transComplete();

        if (!$db->transStatus()) {
            $validation->setError('username', 'Registration failed. Please try again.');
            $data['has_errors'] = true;
            return view('company_register', $data);
        }

        // Keep platform admin session on control-plane DB.
        (new TenantContext())->clearTenantDatabaseSession();

        return redirect()->to('super-admin?company_created=1');
    }
}
