<?php

namespace App\Controllers;

use App\Libraries\TenantContext;
use App\Models\Platform_admin;
use App\Models\Subscription_request;
use App\Models\Tenant;
use CodeIgniter\HTTP\RedirectResponse;
use Config\OSPOS;

class Super_admin extends BaseController
{
    public function login(): string|RedirectResponse
    {
        $config = config(OSPOS::class)->settings;
        $validation = service('validation');
        $data = [
            'config' => $config,
            'validation' => $validation,
            'has_errors' => false
        ];

        $platform_admin = model(Platform_admin::class);
        if ($platform_admin->is_logged_in()) {
            return redirect()->to('super-admin');
        }

        (new TenantContext())->clearTenantDatabaseSession();

        if ($this->request->getMethod() !== 'POST') {
            return view('super_admin/login', $data);
        }

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            $data['has_errors'] = true;
            return view('super_admin/login', $data);
        }

        $username = (string)$this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = (string)$this->request->getPost('password');
        if (!$platform_admin->login($username, $password)) {
            $validation->setError('username', 'Invalid super admin credentials.');
            $data['has_errors'] = true;
            return view('super_admin/login', $data);
        }

        return redirect()->to('super-admin');
    }

    public function index(): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $tenant_model = model(Tenant::class);
        $request_model = model(Subscription_request::class);
        $data = [
            'tenants' => $tenant_model->get_with_owner_summary(),
            'platform_admins' => $platform_admin->get_all_admins(),
            'is_owner' => $platform_admin->is_owner(),
            'subscription_requests' => $request_model->get_pending_with_plan()
        ];

        return view('super_admin/dashboard', $data);
    }

    public function postToggleStatus(int $tenant_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $status = (string)$this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $allowed = ['active', 'suspended', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return redirect()->to('super-admin');
        }

        model(Tenant::class)->set_status($tenant_id, $status);

        return redirect()->to('super-admin');
    }

    public function logout(): RedirectResponse
    {
        model(Platform_admin::class)->logout();
        (new TenantContext())->clearTenantDatabaseSession();
        return redirect()->to('super-admin/login');
    }

    public function postCreateAdmin(): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        if (!$platform_admin->is_owner()) {
            return redirect()->to('super-admin?error=only_owner');
        }

        $username = (string)$this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $full_name = (string)$this->request->getPost('full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = (string)$this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $password = (string)$this->request->getPost('password');

        if (strlen($username) < 4 || strlen($password) < 8 || strlen($full_name) < 2) {
            return redirect()->to('super-admin?error=invalid_admin_input');
        }

        if ($platform_admin->username_exists($username)) {
            return redirect()->to('super-admin?error=admin_username_exists');
        }

        $platform_admin->create_admin($username, $password, $full_name, $email ?: null);

        return redirect()->to('super-admin?admin_created=1');
    }

    public function postApproveRequest(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $request_model = model(Subscription_request::class);
        $request = $request_model->get_info_for_review($request_id);
        if ($request === null || $request->status !== 'pending') {
            return redirect()->to('super-admin?error=request_not_found');
        }

        $db = db_connect();
        $tenant_exists = $db->table('tenants')->where('tenant_code', $request->tenant_code)->countAllResults();
        $username_exists = $db->table('employees')->where('username', $request->owner_username)->countAllResults();
        if ($tenant_exists > 0 || $username_exists > 0) {
            return redirect()->to('super-admin?error=tenant_or_user_exists');
        }

        $db->transStart();

        $tenant_insert = [
            'tenant_code' => $request->tenant_code,
            'company_name' => $request->company_name,
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
            'first_name' => $request->owner_first_name,
            'last_name' => $request->owner_last_name,
            'gender' => null,
            'phone_number' => (string)($request->owner_phone ?? ''),
            'email' => $request->owner_email,
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
            'username' => $request->owner_username,
            'password' => $request->owner_password_hash,
            'deleted' => 0,
            'hash_version' => 2,
            'tenant_id' => $tenant_id
        ]);

        $db->table('tenant_users')->insert([
            'tenant_id' => $tenant_id,
            'person_id' => $person_id,
            'tenant_role' => 'owner',
            'is_active' => 1
        ]);

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

        $db->table('tenant_config')->insertBatch([
            ['tenant_id' => $tenant_id, 'config_key' => 'company', 'config_value' => $request->company_name],
            ['tenant_id' => $tenant_id, 'config_key' => 'timezone', 'config_value' => 'UTC'],
            ['tenant_id' => $tenant_id, 'config_key' => 'currency_code', 'config_value' => 'USD']
        ]);

        $db->table('subscriptions')->insert([
            'tenant_id' => $tenant_id,
            'plan_id' => (int)$request->plan_id,
            'status' => 'active',
            'trial_ends_at' => null,
            'period_start' => date('Y-m-d H:i:s'),
            'period_end' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'cancel_at_period_end' => 0
        ]);

        $db->table('subscription_requests')
            ->where('request_id', $request_id)
            ->update([
                'status' => 'approved',
                'reviewed_by_admin_id' => (int)session()->get('platform_admin_id'),
                'reviewed_at' => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('super-admin?error=approve_failed');
        }

        return redirect()->to('super-admin?request_approved=1');
    }

    public function postRejectRequest(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        db_connect()->table('subscription_requests')
            ->where('request_id', $request_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_by_admin_id' => (int)session()->get('platform_admin_id'),
                'reviewed_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->to('super-admin?request_rejected=1');
    }
}
