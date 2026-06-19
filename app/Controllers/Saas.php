<?php

namespace App\Controllers;

use App\Models\Subscription_request;
use Config\OSPOS;

class Saas extends BaseController
{
    private const SUBSCRIPTION_PLAN_CODE = 'pos_monthly';

    private function getSubscriptionPlan(): ?array
    {
        $db = db_connect();

        $plan = $db->table('plans')
            ->where('plan_code', self::SUBSCRIPTION_PLAN_CODE)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if ($plan !== null) {
            return $plan;
        }

        return $db->table('plans')
            ->where('is_active', 1)
            ->orderBy('price_monthly', 'desc')
            ->get()
            ->getRowArray();
    }

    public function index(): string
    {
        return view('saas/landing', [
            'config' => config(OSPOS::class)->settings,
            'plan' => $this->getSubscriptionPlan()
        ]);
    }

    public function register(): string
    {
        return view('saas/register', [
            'config' => config(OSPOS::class)->settings,
            'plan' => $this->getSubscriptionPlan(),
            'validation' => service('validation'),
            'has_errors' => false
        ]);
    }

    public function postRegister()
    {
        $validation = service('validation');
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[255]',
            'tenant_code' => 'required|alpha_dash|min_length[3]|max_length[50]',
            'owner_first_name' => 'required|min_length[2]|max_length[100]',
            'owner_last_name' => 'required|min_length[2]|max_length[100]',
            'owner_email' => 'required|valid_email',
            'owner_phone' => 'permit_empty|max_length[255]',
            'owner_username' => 'required|min_length[4]|max_length[50]',
            'owner_password' => 'required|strong_password|max_length[255]',
            'plan_id' => 'required|integer',
            'payment_reference' => 'required|min_length[3]|max_length[100]'
        ];

        $plan = $this->getSubscriptionPlan();
        $register_view_data = [
            'config' => config(OSPOS::class)->settings,
            'plan' => $plan,
            'validation' => $validation,
        ];

        if ($plan === null) {
            $validation->setError('payment_reference', 'Subscription plan is not configured. Please contact support.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        if (!$this->validate($rules)) {
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $submitted_plan_id = (int)$this->request->getPost('plan_id');
        if ($submitted_plan_id !== (int)$plan['plan_id']) {
            $validation->setError('payment_reference', 'Invalid subscription plan selected.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $db = db_connect();
        $tenant_code = strtolower((string)$this->request->getPost('tenant_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $owner_username = (string)$this->request->getPost('owner_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $tenant_exists = $db->table('tenants')->where('tenant_code', $tenant_code)->countAllResults();
        $username_exists = $db->table('employees')->where('username', $owner_username)->countAllResults();
        if ($tenant_exists > 0 || $username_exists > 0) {
            $validation->setError('tenant_code', 'Company code or owner username already exists.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $request_model = model(Subscription_request::class);
        $request_model->insert([
            'company_name' => (string)$this->request->getPost('company_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'tenant_code' => $tenant_code,
            'owner_first_name' => (string)$this->request->getPost('owner_first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'owner_last_name' => (string)$this->request->getPost('owner_last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'owner_email' => (string)$this->request->getPost('owner_email', FILTER_SANITIZE_EMAIL),
            'owner_phone' => (string)$this->request->getPost('owner_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'owner_username' => $owner_username,
            'owner_password_hash' => password_hash((string)$this->request->getPost('owner_password'), PASSWORD_DEFAULT),
            'plan_id' => (int)$plan['plan_id'],
            'payment_reference' => (string)$this->request->getPost('payment_reference', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'status' => 'pending',
            'notes' => 'Submitted from website signup flow'
        ]);

        return view('saas/register_success');
    }
}
