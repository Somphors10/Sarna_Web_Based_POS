<?php

namespace App\Controllers;

use App\Libraries\PlatformArchitecture;
use App\Libraries\PlatformMail;
use App\Libraries\Telegram_lib;
use App\Models\Subscription_request;
use Config\OSPOS;

class Saas extends BaseController
{
    /**
     * @return list<array<string, mixed>>
     */
    private function getSubscriptionPlans(): array
    {
        (new PlatformArchitecture())->ensure();
        $plans = (new PlatformArchitecture())->getActivePlans();
        if ($plans !== []) {
            return $plans;
        }

        $db = db_connect('platform');
        if (!$db->tableExists('plans')) {
            return [];
        }

        return $db->table('plans')
            ->where('is_active', 1)
            ->orderBy('price_monthly', 'asc')
            ->get()
            ->getResultArray();
    }

    private function getDefaultPlan(): ?array
    {
        $plans = $this->getSubscriptionPlans();
        foreach ($plans as $plan) {
            if (($plan['plan_code'] ?? '') === PlatformArchitecture::PLAN_PRO) {
                return $plan;
            }
        }

        return $plans[0] ?? null;
    }

    /**
     * @param list<array<string, mixed>> $plans
     */
    private function resolvePlan(array $plans, ?int $plan_id = null, ?string $plan_code = null): ?array
    {
        if ($plan_id !== null && $plan_id > 0) {
            foreach ($plans as $plan) {
                if ((int)($plan['plan_id'] ?? 0) === $plan_id) {
                    return $plan;
                }
            }
        }

        $plan_code = strtolower(trim((string)$plan_code));
        if ($plan_code !== '') {
            foreach ($plans as $plan) {
                if (strtolower((string)($plan['plan_code'] ?? '')) === $plan_code) {
                    return $plan;
                }
            }
        }

        return $this->getDefaultPlan();
    }

    public function index(): string
    {
        $plans = $this->getSubscriptionPlans();

        return view('saas/landing', [
            'config' => config(OSPOS::class)->settings,
            'plan' => $this->getDefaultPlan(),
            'plans' => $plans,
        ]);
    }

    public function register(): string
    {
        $plans = $this->getSubscriptionPlans();
        $plan = $this->resolvePlan(
            $plans,
            (int)$this->request->getGet('plan_id'),
            (string)$this->request->getGet('plan')
        );

        return view('saas/register', [
            'config' => config(OSPOS::class)->settings,
            'plan' => $plan,
            'plans' => $plans,
            'business_types' => saas_business_types(),
            'countries' => saas_signup_countries(),
            'validation' => service('validation'),
            'has_errors' => false
        ]);
    }

    public function postRegister()
    {
        $validation = service('validation');
        $plans = $this->getSubscriptionPlans();
        $selected_plan = $this->resolvePlan($plans, (int)$this->request->getPost('plan_id'));
        $register_view_data = [
            'config' => config(OSPOS::class)->settings,
            'plan' => $selected_plan ?? $this->getDefaultPlan(),
            'plans' => $plans,
            'business_types' => saas_business_types(),
            'countries' => saas_signup_countries(),
            'validation' => $validation,
        ];

        if ($plans === []) {
            $validation->setError('plan_id', 'Subscription plan is not configured. Please contact support.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $rules = [
            'company_name' => 'required|min_length[2]|max_length[255]',
            'tenant_code' => 'required|min_length[1]|max_length[50]',
            'business_type' => 'required|max_length[64]',
            'address' => 'required|min_length[5]|max_length[255]',
            'city' => 'required|min_length[2]|max_length[120]',
            'country' => 'required|min_length[2]|max_length[80]',
            'tax_id' => 'permit_empty|min_length[3]|max_length[64]',
            'owner_first_name' => 'required|min_length[2]|max_length[100]',
            'owner_last_name' => 'required|min_length[2]|max_length[100]',
            'owner_email' => 'required|valid_email',
            'owner_phone' => 'required|min_length[8]|max_length[40]',
            'owner_username' => 'required|min_length[4]|max_length[50]',
            'owner_password' => 'required|strong_password|max_length[255]',
            'plan_id' => 'permit_empty|integer',
            'captcha_code' => 'required|min_length[4]|max_length[8]',
        ];

        if (!$this->validate($rules)) {
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        if (!$this->captchaIsValid((string)$this->request->getPost('captcha_code'))) {
            $validation->setError('captcha_code', 'That code is wrong. Click New picture and try again.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $business_type = strtolower((string)$this->request->getPost('business_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $country = trim((string)$this->request->getPost('country', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        if (!array_key_exists($business_type, saas_business_types())) {
            $validation->setError('business_type', 'Select a valid business type.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }
        if ($country === '' || mb_strlen($country) < 2) {
            $validation->setError('country', 'Enter your country.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $submitted_plan_id = (int)$this->request->getPost('plan_id');
        $selected_plan = $this->getDefaultPlan();
        if ($submitted_plan_id > 0) {
            foreach ($plans as $plan) {
                if ((int)$plan['plan_id'] === $submitted_plan_id) {
                    $selected_plan = $plan;
                    break;
                }
            }
        }

        if ($selected_plan === null) {
            $validation->setError('plan_id', 'Subscription plan is not configured. Please contact support.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $db = db_connect('platform');
        $tenant_code = strtolower(trim((string)$this->request->getPost('tenant_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
        $owner_username = trim((string)$this->request->getPost('owner_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $tenant_exists = $db->table('tenants')->where('tenant_code', $tenant_code)->countAllResults() > 0;
        $username_exists = (new PlatformArchitecture())->usernameExists($owner_username);

        if ($tenant_exists) {
            $validation->setError('tenant_code', 'This company code is already used. Choose another code.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }
        if ($username_exists) {
            $validation->setError('owner_username', 'This POS username is already used. Choose another username.');
            return view('saas/register', $register_view_data + ['has_errors' => true]);
        }

        $company_name = (string)$this->request->getPost('company_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = (string)$this->request->getPost('address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $city = (string)$this->request->getPost('city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tax_id = (string)$this->request->getPost('tax_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $owner_first_name = (string)$this->request->getPost('owner_first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $owner_last_name = (string)$this->request->getPost('owner_last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $owner_email = strtolower((string)$this->request->getPost('owner_email', FILTER_SANITIZE_EMAIL));
        $owner_phone = (string)$this->request->getPost('owner_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $request_model = model(Subscription_request::class);
        $request_id = $request_model->insert([
            'company_name' => $company_name,
            'tenant_code' => $tenant_code,
            'business_type' => $business_type,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'tax_id' => $tax_id,
            'owner_first_name' => $owner_first_name,
            'owner_last_name' => $owner_last_name,
            'owner_email' => $owner_email,
            'owner_phone' => $owner_phone,
            'owner_username' => $owner_username,
            'owner_password_hash' => password_hash((string)$this->request->getPost('owner_password'), PASSWORD_DEFAULT),
            'plan_id' => (int)$selected_plan['plan_id'],
            'payment_reference' => '',
            'status' => 'pending',
            'notes' => 'Submitted from website signup flow'
        ]);

        $verify_token = '';
        if ($request_id !== false && $db->fieldExists('email_verify_token', 'subscription_requests')) {
            $verify_token = bin2hex(random_bytes(20));
            $db->table('subscription_requests')
                ->where('request_id', $request_id)
                ->update([
                    'email_verify_token' => $verify_token,
                    'email_verified_at' => null,
                ]);
        }

        $mail_ok = false;
        $mail_error = '';
        if ($request_id !== false && $verify_token !== '') {
            $request_row = $request_model->get_info_for_review((int)$request_id);
            if ($request_row !== null) {
                $mail = (new PlatformMail())->sendVerifyEmail(
                    $request_row,
                    site_url('saas/verify-email/' . $verify_token)
                );
                $mail_ok = $mail['ok'];
                $mail_error = $mail['error'];
            }
        }

        return view('saas/register_success', [
            'tenant_code' => $tenant_code,
            'owner_email' => $owner_email,
            'email_sent' => $mail_ok,
            'mail_error' => $mail_error,
            'needs_verify' => $verify_token !== '',
        ]);
    }

    public function verifyEmail(string $token): string
    {
        (new PlatformArchitecture())->ensure();
        $request_model = model(Subscription_request::class);
        $request = $request_model->find_by_verify_token($token);

        if ($request === null) {
            return view('saas/verify_email', [
                'ok' => false,
                'already' => false,
                'owner_email' => '',
            ]);
        }

        if ($request_model->is_email_verified($request)) {
            return view('saas/verify_email', [
                'ok' => true,
                'already' => true,
                'owner_email' => (string)$request->owner_email,
            ]);
        }

        db_connect('platform')->table('subscription_requests')
            ->where('request_id', (int)$request->request_id)
            ->update([
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]);

        $this->notifySuperAdminOfRequest($request);

        return view('saas/verify_email', [
            'ok' => true,
            'already' => false,
            'owner_email' => (string)$request->owner_email,
        ]);
    }

    public function postResendVerify()
    {
        (new PlatformArchitecture())->ensure();
        $email = strtolower(trim((string)$this->request->getPost('owner_email', FILTER_SANITIZE_EMAIL)));
        $request_model = model(Subscription_request::class);
        $request = $request_model->find_unverified_by_email($email);

        $mail_ok = false;
        $mail_error = '';
        if ($request !== null) {
            $token = bin2hex(random_bytes(20));
            db_connect('platform')->table('subscription_requests')
                ->where('request_id', (int)$request->request_id)
                ->update(['email_verify_token' => $token]);
            $request->email_verify_token = $token;
            $mail = (new PlatformMail())->sendVerifyEmail(
                $request,
                site_url('saas/verify-email/' . $token)
            );
            $mail_ok = $mail['ok'];
            $mail_error = $mail['error'];
            $email = (string)$request->owner_email;
        }

        return view('saas/register_success', [
            'tenant_code' => (string)($request->tenant_code ?? ''),
            'owner_email' => $email,
            'email_sent' => $mail_ok,
            'mail_error' => $mail_error,
            'needs_verify' => true,
            'resent' => true,
        ]);
    }

    private function notifySuperAdminOfRequest(object $request): void
    {
        $plan = db_connect('platform')->table('plans')->where('plan_id', (int)$request->plan_id)->get(1)->getRow();
        (new Telegram_lib())->notify_new_subscription_request([
            'request_id'        => (int)$request->request_id,
            'company_name'      => (string)$request->company_name,
            'tenant_code'       => (string)$request->tenant_code,
            'business_type'     => saas_business_type_label((string)($request->business_type ?? '')),
            'address'           => (string)($request->address ?? ''),
            'city'              => (string)($request->city ?? ''),
            'country'           => (string)($request->country ?? ''),
            'tax_id'            => (string)($request->tax_id ?? ''),
            'owner_first_name'  => (string)$request->owner_first_name,
            'owner_last_name'   => (string)$request->owner_last_name,
            'owner_email'       => (string)$request->owner_email,
            'owner_phone'       => (string)$request->owner_phone,
            'owner_username'    => (string)$request->owner_username,
            'payment_reference' => '',
            'plan_name'         => (string)($plan->plan_name ?? 'WBPOS'),
            'plan_price'        => saas_monthly_price((float)($plan->price_monthly ?? 0)),
            'review_url'        => site_url('super-admin/requests'),
        ]);
    }

    public function checkout(): string
    {
        return view('saas/checkout', [
            'config' => config(OSPOS::class)->settings,
            'validation' => service('validation'),
            'has_errors' => false,
            'status_message' => '',
            'tenant_code' => (string)$this->request->getGet('code'),
            'owner_email' => (string)$this->request->getGet('email'),
        ]);
    }

    public function postCheckout()
    {
        (new PlatformArchitecture())->ensure();
        $validation = service('validation');
        $tenant_code = strtolower(trim((string)$this->request->getPost('tenant_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
        $owner_email = strtolower(trim((string)$this->request->getPost('owner_email', FILTER_SANITIZE_EMAIL)));
        $view = [
            'config' => config(OSPOS::class)->settings,
            'validation' => $validation,
            'tenant_code' => $tenant_code,
            'owner_email' => $owner_email,
        ];

        if (!$this->validate([
            'tenant_code' => 'required|min_length[1]|max_length[50]',
            'owner_email' => 'required|valid_email',
        ])) {
            return view('saas/checkout', $view + ['has_errors' => true, 'status_message' => '']);
        }

        $request = model(Subscription_request::class)->find_for_checkout($tenant_code, $owner_email);
        if ($request === null) {
            $validation->setError('tenant_code', 'No registration found for that company code and email.');
            return view('saas/checkout', $view + ['has_errors' => true, 'status_message' => '']);
        }

        $status = strtolower((string)$request->status);
        if ($status === 'pending') {
            return view('saas/checkout', $view + [
                'has_errors' => false,
                'status_message' => 'Your registration is still waiting for Super Admin. You cannot pay yet. Come back after they activate it.',
            ]);
        }
        if ($status === 'rejected') {
            return view('saas/checkout', $view + [
                'has_errors' => false,
                'status_message' => 'This registration was not accepted. Please contact support or register again.',
            ]);
        }

        $tenant = db_connect('platform')->table('tenants')
            ->select('tenant_id, status')
            ->where('tenant_code', (string)$request->tenant_code)
            ->get(1)
            ->getRow();
        if (saas_tenant_is_currently_paid($tenant)) {
            return view('saas/pay', $this->payViewData($request, (string)$request->payment_token) + ['already_paid' => true]);
        }

        $token = trim((string)($request->payment_token ?? ''));
        if ($token === '') {
            return view('saas/checkout', $view + [
                'has_errors' => false,
                'status_message' => 'Your shop is not ready for payment yet. Ask Super Admin to activate the request again.',
            ]);
        }

        return redirect()->to('saas/pay/' . $token);
    }

    public function pay(string $token): string
    {
        (new PlatformArchitecture())->ensure();
        $request = model(Subscription_request::class)->find_by_payment_token($token);

        return view('saas/pay', $this->payViewData($request, $token));
    }

    public function postPay(string $token)
    {
        (new PlatformArchitecture())->ensure();
        $validation = service('validation');
        $request_model = model(Subscription_request::class);
        $request = $request_model->find_by_payment_token($token);
        $view_data = $this->payViewData($request, $token, $validation);

        if ($request === null || (string)$request->status !== 'approved') {
            return view('saas/pay', $view_data);
        }

        $tenant = db_connect('platform')->table('tenants')
            ->select('tenant_id, status')
            ->where('tenant_code', (string)$request->tenant_code)
            ->get(1)
            ->getRow();
        if (saas_tenant_is_currently_paid($tenant)) {
            return view('saas/pay', $view_data + ['already_paid' => true]);
        }

        if (!$this->validate(['payment_reference' => 'required|min_length[3]|max_length[100]'])) {
            return view('saas/pay', $this->payViewData($request, $token, $validation) + ['has_errors' => true]);
        }

        $payment_reference = (string)$this->request->getPost('payment_reference', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->activatePaidTenant((string)$request->tenant_code, (int)$request->request_id, $payment_reference);

        return view('saas/pay_success', [
            'config' => config(OSPOS::class)->settings,
            'company_name' => (string)$request->company_name,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payViewData(?object $request, string $token, $validation = null): array
    {
        $tenant = null;
        if ($request !== null) {
            $tenant = db_connect('platform')->table('tenants')
                ->select('tenant_id, status')
                ->where('tenant_code', (string)$request->tenant_code)
                ->get(1)
                ->getRow();
        }

        return [
            'config' => config(OSPOS::class)->settings,
            'request' => $request,
            'token' => $token,
            'validation' => $validation ?? service('validation'),
            'has_errors' => false,
            'already_paid' => saas_tenant_is_currently_paid($tenant),
            'is_renewal' => $tenant !== null
                && strtolower((string)($tenant->status ?? '')) === 'active'
                && saas_tenant_needs_renewal((int)$tenant->tenant_id),
            'qr_image_path' => 'images/payment/aba-khqr-code.png',
        ];
    }

    private function activatePaidTenant(string $tenant_code, int $request_id, string $payment_reference): void
    {
        $db = db_connect('platform');
        $db->table('subscription_requests')
            ->where('request_id', $request_id)
            ->update(['payment_reference' => $payment_reference]);

        $tenant = $db->table('tenants')->where('tenant_code', $tenant_code)->get(1)->getRow();
        if ($tenant === null) {
            return;
        }

        $tenant_id = (int)$tenant->tenant_id;
        $db->table('tenants')
            ->where('tenant_id', $tenant_id)
            ->update(['status' => 'active']);

        saas_activate_or_renew_subscription($tenant_id);
        saas_record_subscription_payment($tenant_id, 'owner_checkout', $payment_reference, saas_monthly_price());
    }

    public function captchaImage()
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        session()->set('saas_captcha', $code);

        if (!function_exists('imagecreatetruecolor')) {
            return $this->response
                ->setHeader('Cache-Control', 'no-store')
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody($code);
        }

        $width = 188;
        $height = 58;
        $image = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($image, 245, 243, 255);
        $ink = imagecolorallocate($image, 76, 29, 149);
        $noise = imagecolorallocate($image, 196, 181, 253);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        for ($i = 0; $i < 10; $i++) {
            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $noise
            );
        }

        $x = 18;
        $length = strlen($code);
        for ($i = 0; $i < $length; $i++) {
            $y = random_int(14, 22);
            imagestring($image, 5, $x, $y, $code[$i], $ink);
            $x += 32;
        }

        ob_start();
        imagepng($image);
        $png = (string)ob_get_clean();
        imagedestroy($image);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Content-Type', 'image/png')
            ->setBody($png);
    }

    private function captchaIsValid(string $answer): bool
    {
        $expected = strtoupper(trim((string)session()->get('saas_captcha')));
        session()->remove('saas_captcha');
        $given = strtoupper(preg_replace('/\s+/', '', $answer) ?? '');

        return $expected !== '' && $given !== '' && hash_equals($expected, $given);
    }
}
