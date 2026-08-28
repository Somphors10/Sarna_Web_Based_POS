<?php

namespace App\Controllers;

use App\Libraries\PlatformMail;
use App\Libraries\TenantContext;
use App\Libraries\MY_Migration;
use App\Models\Employee;
use App\Models\Password_reset_request;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Model;
use Config\OSPOS;
use Config\Services;
use Throwable;

/**
 * @property employee employee
 */
class Login extends BaseController
{
    private const SUPPORT_EMAIL = 'support@wbpos.com';

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
     * Forgot password — email a one-time reset link (no Super Admin approval).
     */
    public function forgotPassword(): string|RedirectResponse
    {
        (new TenantContext())->clearTenantDatabaseSession();

        $validation = Services::validation();
        $data = [
            'has_errors' => false,
            'validation' => $validation,
            'support_email' => self::SUPPORT_EMAIL,
        ];

        if ($this->request->getMethod() !== 'POST') {
            return view('login/forgot_password', $data);
        }

        $email = strtolower(trim((string)$this->request->getPost('email', FILTER_SANITIZE_EMAIL)));

        if ($email === '') {
            $validation->setError('email', 'Enter the email saved on your shop account.');
            $data['has_errors'] = true;

            return view('login/forgot_password', $data);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validation->setError('email', 'Enter a valid email address.');
            $data['has_errors'] = true;

            return view('login/forgot_password', $data);
        }

        $reset_model = model(Password_reset_request::class);

        if (!$reset_model->table_ready()) {
            $validation->setError('email', 'Password reset is not available yet. Please contact ' . self::SUPPORT_EMAIL . '.');
            $data['has_errors'] = true;

            return view('login/forgot_password', $data);
        }

        try {
            $employee = $reset_model->resolve_employee_by_email($email);

            if ($employee !== null) {
                $employee->email = $email;
                $token = $reset_model->create_token($employee, $email);
                $reset_url = site_url('login/reset-password/' . $token);
                $mail = new PlatformMail();
                $result = $mail->sendPasswordReset(
                    $employee,
                    (string)($employee->company_name ?? 'your shop'),
                    $reset_url
                );
                if (!$result['ok']) {
                    log_message('error', 'Password reset mail failed for ' . $email . ': ' . ($result['error'] ?? ''));
                } else {
                    log_message('info', 'Password reset mail sent to ' . $email);
                }
            } else {
                log_message('info', 'Password reset: no employee matched email ' . $email);
            }
        } catch (Throwable $e) {
            log_message('error', 'Forgot password failed: ' . $e->getMessage());
        }

        return redirect()->to('login/forgot-success');
    }

    public function forgotPasswordSuccess(): string
    {
        (new TenantContext())->clearTenantDatabaseSession();

        return view('login/forgot_password_success', [
            'support_email' => self::SUPPORT_EMAIL,
        ]);
    }

    /**
     * Set a new password using the email reset link.
     */
    public function resetPassword(string $token = ''): string|RedirectResponse
    {
        (new TenantContext())->clearTenantDatabaseSession();

        $validation = Services::validation();
        $reset_model = model(Password_reset_request::class);
        $request_row = $reset_model->find_valid_token($token);

        $data = [
            'has_errors' => false,
            'validation' => $validation,
            'token' => $token,
            'invalid_token' => $request_row === null,
            'support_email' => self::SUPPORT_EMAIL,
        ];

        if ($request_row === null) {
            return view('login/reset_password', $data);
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('login/reset_password', $data);
        }

        $rules = [
            'password' => 'required|strong_password|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            $data['has_errors'] = true;

            return view('login/reset_password', $data);
        }

        helper('password');

        $plain_password = (string)$this->request->getPost('password');
        $context = new TenantContext();
        $context->applyRuntimeConnection((int)$request_row->tenant_id);

        $updated = db_connect()->table('employees')
            ->where('person_id', (int)$request_row->person_id)
            ->where('tenant_id', (int)$request_row->tenant_id)
            ->update([
                'password' => password_hash($plain_password, PASSWORD_DEFAULT),
                'hash_version' => 2,
            ]);

        $context->restoreSharedConnection();

        if (!$updated) {
            $validation->setError('password', 'Could not update your password. Please request a new reset link.');
            $data['has_errors'] = true;

            return view('login/reset_password', $data);
        }

        $reset_model->mark_used((int)$request_row->request_id);

        return redirect()->to('login?password_reset=1');
    }
}
