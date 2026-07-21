<?php

namespace App\Controllers;

use App\Libraries\TenantContext;
use App\Libraries\MY_Migration;
use App\Models\Employee;
use App\Models\Password_reset_request;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Model;
use Config\OSPOS;
use Config\Services;

/**
 * @property employee employee
 */
class Login extends BaseController
{
    public Model $employee;

    /**
     * @return RedirectResponse|string
     */
    public function index(): string|RedirectResponse
    {
        $this->employee = model(Employee::class);
        if (!$this->employee->is_logged_in()) {
            // Login must always start from neutral DB context.
            // Stale tenant DB session overrides can break auth and render Whoops.
            (new TenantContext())->clearTenantDatabaseSession();

            $migration = new MY_Migration(config('Migrations'));
            $config = config(OSPOS::class)->settings;

            $gcaptcha_enabled = array_key_exists('gcaptcha_enable', $config)
                ? $config['gcaptcha_enable']
                : false;

            $migration->migrate_to_ci4();

            $validation = Services::validation();

            $data = [
                'has_errors'       => false,
                'is_latest'        => $migration->is_latest(),
                'latest_version'   => $migration->get_latest_migration(),
                'gcaptcha_enabled' => $gcaptcha_enabled,
                'config'           => $config,
                'validation'       => $validation
            ];

            if ($this->request->getMethod() !== 'POST') {
                return view('login', $data);
            }

            $rules = ['username' => 'required|login_check[data]'];
            $messages = [
                'username' => [
                    'required'    => lang('Login.required_username'),
                    'login_check' => lang('Login.invalid_username_and_password'),
                ]
            ];

            if (!$this->validate($rules, $messages)) {
                $data['has_errors'] = !empty($validation->getErrors());

                return view('login', $data);
            }

            if (!$data['is_latest']) {
                set_time_limit(3600);

                $migration->setNamespace('App')->latest();
                return redirect()->to('login');
            }
        }

        return redirect()->to('home');
    }

    /**
     * Forgot password — submit reset request for platform admin approval.
     */
    public function forgotPassword(): string|RedirectResponse
    {
        (new TenantContext())->clearTenantDatabaseSession();

        $validation = Services::validation();
        $data = [
            'has_errors' => false,
            'validation' => $validation,
        ];

        if ($this->request->getMethod() !== 'POST') {
            return view('login/forgot_password', $data);
        }

        $rules = [
            'tenant_code'      => 'required|alpha_dash|min_length[2]|max_length[64]',
            'username'         => 'required|min_length[2]|max_length[50]',
            'password'         => 'required|strong_password|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            $data['has_errors'] = true;

            return view('login/forgot_password', $data);
        }

        helper('password');

        $tenant_code = strtolower(trim((string)$this->request->getPost('tenant_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
        $username = trim((string)$this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $plain_password = (string)$this->request->getPost('password');

        $reset_model = model(Password_reset_request::class);

        if (!$reset_model->db->tableExists('password_reset_requests')) {
            $validation->setError('username', 'Password reset is not available yet. Please contact platform support.');
            $data['has_errors'] = true;

            return view('login/forgot_password', $data);
        }

        $employee = $reset_model->resolve_employee($tenant_code, $username);

        if ($employee !== null && !$reset_model->has_pending($tenant_code, $username)) {
            $reset_model->insert([
                'tenant_code'       => $tenant_code,
                'username'          => $username,
                'person_id'         => (int)$employee->person_id,
                'tenant_id'         => (int)$employee->tenant_id,
                'new_password_hash' => password_hash($plain_password, PASSWORD_DEFAULT),
                'status'            => 'pending',
            ]);
        }

        // Always show success — do not reveal whether the account exists.
        return redirect()->to('login/forgot-success');
    }

    public function forgotPasswordSuccess(): string
    {
        (new TenantContext())->clearTenantDatabaseSession();

        return view('login/forgot_password_success');
    }
}
