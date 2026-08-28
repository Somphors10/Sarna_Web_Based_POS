<?php

namespace App\Controllers;

use App\Libraries\PlatformArchitecture;
use App\Libraries\PlatformMail;
use App\Libraries\Telegram_lib;
use App\Libraries\TemplateSync;
use App\Libraries\TenantContext;
use App\Libraries\TenantDatabaseProvisioner;
use App\Libraries\TenantSeeder;
use App\Models\Platform_admin;
use App\Models\Subscription_request;
use App\Models\Tenant;
use CodeIgniter\HTTP\RedirectResponse;
use Config\OSPOS;
use Throwable;

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

        login_super_admin_pos_session();
        (new PlatformArchitecture())->ensure();

        return redirect()->to('super-admin');
    }

    public function sendPayment(int $request_id): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        (new PlatformArchitecture())->ensure();
        $request = model(Subscription_request::class)->get_info_for_review($request_id);
        if ($request === null || (string)$request->status !== 'approved') {
            return redirect()->to('super-admin/requests?error=request_not_found');
        }

        $db = db_connect('platform');
        $plan = $db->table('plans')->where('plan_id', (int)$request->plan_id)->get(1)->getRow();
        $tenant = $db->table('tenants')->where('tenant_code', (string)$request->tenant_code)->get(1)->getRow();
        $token = trim((string)($request->payment_token ?? ''));

        return view('super_admin/send_payment', [
            'request' => $request,
            'plan' => $plan,
            'tenant' => $tenant,
            'pay_url' => $token !== '' ? site_url('saas/pay/' . $token) : '',
            'checkout_url' => site_url('saas/checkout'),
            'qr_image_path' => 'images/payment/aba-khqr-code.png',
            'paid' => service('request')->getGet('paid') === '1',
            'email_sent' => service('request')->getGet('email') === '1',
            'email_failed' => service('request')->getGet('email') === '0',
            'email_error' => (string)session()->getFlashdata('khqr_email_error'),
            'delivery' => PlatformMail::deliveryInfo(),
        ]);
    }

    public function postSaveGmailSmtp(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $gmail = strtolower(trim((string)$this->request->getPost('gmail_user')));
        $app_password = preg_replace('/\s+/', '', (string)$this->request->getPost('gmail_app_password'));
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('khqr_email_error', 'Enter a valid Gmail address and a Google App Password.');
            return redirect()->to('super-admin/send-payment/' . $request_id . '?email=0');
        }
        if (strlen($app_password) < 8) {
            session()->setFlashdata('khqr_email_error', 'Paste the 16-character Google App Password (not your normal Gmail password).');
            return redirect()->to('super-admin/send-payment/' . $request_id . '?email=0');
        }

        $arch = new PlatformArchitecture();
        $arch->ensure();
        $arch->setTemplateMeta('email_smtp_user', $gmail);
        $arch->setTemplateMeta('email_smtp_pass', PlatformMail::encryptPass($app_password));

        return $this->postResendPaymentEmail($request_id);
    }

    public function postSaveGmailSettings(): RedirectResponse
    {
        $saved = $this->saveGmailFromPost();
        if (!$saved['ok']) {
            session()->setFlashdata('gmail_error', $saved['error']);
            return redirect()->to('super-admin/email?error=gmail');
        }

        return redirect()->to('super-admin/email?gmail_saved=1');
    }

    public function postTestGmail(): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $to = strtolower(trim((string)$this->request->getPost('test_email', FILTER_SANITIZE_EMAIL)));
        $mail = (new PlatformMail())->sendTest($to);
        if (!$mail['ok']) {
            session()->setFlashdata('gmail_error', $mail['error']);
            return redirect()->to('super-admin/email?error=gmail');
        }

        return redirect()->to('super-admin/email?gmail_test=1');
    }

    public function postResendOwnerVerify(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $request_model = model(Subscription_request::class);
        $request = $request_model->get_info_for_review($request_id);
        if ($request === null || $request->status !== 'pending') {
            return redirect()->to('super-admin/requests?error=request_not_found');
        }
        if ($request_model->is_email_verified($request)) {
            return redirect()->to('super-admin/requests');
        }

        $token = bin2hex(random_bytes(20));
        db_connect('platform')->table('subscription_requests')
            ->where('request_id', $request_id)
            ->update(['email_verify_token' => $token]);
        $request->email_verify_token = $token;

        $mail = (new PlatformMail())->sendVerifyEmail(
            $request,
            site_url('saas/verify-email/' . $token)
        );
        if (!$mail['ok']) {
            session()->setFlashdata('gmail_error', $mail['error']);
            return redirect()->to('super-admin/requests?error=verify_not_sent');
        }

        return redirect()->to('super-admin/requests?verify_sent=1');
    }

    /**
     * @return array{ok:bool, error:string}
     */
    private function saveGmailFromPost(): array
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return ['ok' => false, 'error' => 'Not logged in.'];
        }

        $gmail = strtolower(trim((string)$this->request->getPost('gmail_user')));
        $app_password = preg_replace('/\s+/', '', (string)$this->request->getPost('gmail_app_password'));
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Enter a valid Gmail address and a Google App Password.'];
        }
        if (strlen($app_password) < 8) {
            return ['ok' => false, 'error' => 'Paste the 16-character Google App Password (not your normal Gmail password).'];
        }

        $arch = new PlatformArchitecture();
        $arch->ensure();
        $arch->setTemplateMeta('email_smtp_user', $gmail);
        $arch->setTemplateMeta('email_smtp_pass', PlatformMail::encryptPass($app_password));

        return ['ok' => true, 'error' => ''];
    }

    public function postResendPaymentEmail(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $request = model(Subscription_request::class)->get_info_for_review($request_id);
        if ($request === null || (string)$request->status !== 'approved') {
            return redirect()->to('super-admin/requests?error=request_not_found');
        }

        $token = trim((string)($request->payment_token ?? ''));
        $plan = db_connect('platform')->table('plans')->where('plan_id', (int)$request->plan_id)->get(1)->getRow();
        $mail = (new PlatformMail())->sendKhqrPayment(
            $request,
            $token !== '' ? site_url('saas/pay/' . $token) : site_url('saas/checkout'),
            (float)saas_monthly_price((float)($plan->price_monthly ?? 0))
        );
        session()->setFlashdata('khqr_email_error', $mail['error']);

        return redirect()->to('super-admin/send-payment/' . $request_id . '?email=' . ($mail['ok'] ? '1' : '0'));
    }

    public function previewKhqrEmail(int $request_id): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $path = PlatformMail::outboxPath($request_id);
        if (!is_file($path)) {
            return redirect()->to('super-admin/send-payment/' . $request_id);
        }

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody((string)file_get_contents($path));
    }

    public function postConfirmPayment(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $request = model(Subscription_request::class)->get_info_for_review($request_id);
        if ($request === null || (string)$request->status !== 'approved') {
            return redirect()->to('super-admin/requests?error=request_not_found');
        }

        $reference = trim((string)$this->request->getPost('payment_reference', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        if ($reference === '') {
            $reference = 'Confirmed by Super Admin ' . date('Y-m-d H:i');
        }

        $db = db_connect('platform');
        $db->table('subscription_requests')
            ->where('request_id', $request_id)
            ->update(['payment_reference' => $reference]);

        $tenant = $db->table('tenants')->where('tenant_code', (string)$request->tenant_code)->get(1)->getRow();
        if ($tenant !== null) {
            $db->table('tenants')
                ->where('tenant_id', (int)$tenant->tenant_id)
                ->update(['status' => 'active']);
            if ($db->tableExists('subscriptions')) {
                $db->table('subscriptions')
                    ->where('tenant_id', (int)$tenant->tenant_id)
                    ->update(['status' => 'active']);
            }
        }

        return redirect()->to('super-admin/send-payment/' . $request_id . '?paid=1');
    }

    public function index(string $page = 'overview'): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        (new PlatformArchitecture())->ensure();
        refresh_super_admin_pos_session();

        $allowed_pages = ['overview', 'businesses', 'admins', 'requests', 'history', 'features', 'plans', 'email'];
        if (!in_array($page, $allowed_pages, true)) {
            return redirect()->to('super-admin/overview');
        }

        return $this->renderDashboard($platform_admin, $page);
    }

    public function feature(string $module_id): string|RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        (new PlatformArchitecture())->ensure();
        refresh_super_admin_pos_session();

        $features = platform_pos_features();
        if (!isset($features[$module_id])) {
            return redirect()->to('super-admin/features');
        }

        return $this->renderDashboard($platform_admin, 'feature', $module_id);
    }

    public function postToggleFeature(string $module_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        if (!isset(platform_pos_features()[$module_id])) {
            return redirect()->to('super-admin/features');
        }

        $enabled = (string) $this->request->getPost('enabled') === '1';
        if (!platform_set_feature_enabled($module_id, $enabled)) {
            return redirect()->to('super-admin/features/' . $module_id . '?error=feature_update_failed');
        }

        return redirect()->to('super-admin/features/' . $module_id . '?feature_updated=1');
    }

    public function postTogglePlanFeature(int $plan_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $feature_id = (string)$this->request->getPost('feature_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $enabled = (string)$this->request->getPost('enabled') === '1';
        if (!(new PlatformArchitecture())->setPlanFeature($plan_id, $feature_id, $enabled)) {
            return redirect()->to('super-admin/plans?error=feature_update_failed');
        }

        return redirect()->to('super-admin/plans?plan_updated=1');
    }

    public function postSyncTemplate(): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $result = (new TemplateSync())->deploy();
        session()->setFlashdata('template_sync', $result);

        return redirect()->to('super-admin/plans?template_synced=1');
    }

    public function postIsolateTenants(): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $report = (new TenantDatabaseProvisioner())->isolateAllSharedTenants();
        session()->setFlashdata('isolate_report', $report);

        return redirect()->to('super-admin/businesses?tenants_isolated=1');
    }

    public function postIsolateTenant(int $tenant_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $result = (new TenantDatabaseProvisioner())->isolateExisting($tenant_id);
        if (empty($result['success'])) {
            return redirect()->to('super-admin/businesses?error=isolate_failed');
        }

        return redirect()->to('super-admin/businesses?tenant_isolated=1');
    }

    private function renderDashboard(Platform_admin $platform_admin, string $page, ?string $module_id = null): string
    {
        $tenant_model = model(Tenant::class);
        $request_model = model(Subscription_request::class);
        $arch = new PlatformArchitecture();
        $subscription_requests = $request_model->get_pending_with_plan();
        $unverified_requests = $request_model->get_unverified_pending_with_plan();
        $subscription_request_history = $request_model->get_history_with_plan();
        $system_features = platform_features_for_view();
        $current_feature = null;
        if ($module_id !== null) {
            foreach ($system_features as $feature) {
                if ($feature['id'] === $module_id) {
                    $current_feature = $feature;
                    break;
                }
            }
        }

        $tenants = $tenant_model->get_with_owner_summary();
        $this->attachPaymentLinks($tenants);

        return view('super_admin/dashboard', [
            'tenants' => $tenants,
            'platform_admins' => $platform_admin->get_all_admins(),
            'logged_in_admin' => $platform_admin->get_logged_in_admin(),
            'is_owner' => $platform_admin->is_owner(),
            'subscription_requests' => $subscription_requests,
            'unverified_requests' => $unverified_requests,
            'mail_delivery' => PlatformMail::deliveryInfo(),
            'subscription_request_history' => $subscription_request_history,
            'latest_registration_request_id' => $request_model->get_latest_pending_id(),
            'active_page' => $page,
            'system_features' => $system_features,
            'current_feature' => $current_feature,
            'subscription_plans' => $arch->getActivePlans(),
            'plan_feature_matrix' => $arch->getPlanFeatureMatrix(),
            'template_meta' => $arch->getTemplateMeta(),
            'template_sync' => session()->getFlashdata('template_sync'),
            'isolate_report' => session()->getFlashdata('isolate_report'),
            'activation_pay_url' => session()->getFlashdata('activation_pay_url'),
            'activation_email_ok' => session()->getFlashdata('activation_email_ok'),
        ]);
    }

    public function postToggleStatus(int $tenant_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $status = (string)$this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $allowed = ['active', 'suspended', 'cancelled', 'awaiting_payment'];
        if (!in_array($status, $allowed, true)) {
            return redirect()->to('super-admin/businesses');
        }

        model(Tenant::class)->set_status($tenant_id, $status);
        if ($status === 'active') {
            $db = db_connect('platform');
            if ($db->tableExists('subscriptions')) {
                $db->table('subscriptions')->where('tenant_id', $tenant_id)->update(['status' => 'active']);
            }

            $tenant = $db->table('tenants')
                ->select('tenant_code')
                ->where('tenant_id', $tenant_id)
                ->get(1)
                ->getRow();

            if (
                $tenant !== null
                && $db->tableExists('subscription_requests')
                && $db->fieldExists('payment_reference', 'subscription_requests')
            ) {
                $request = $db->table('subscription_requests')
                    ->select('request_id, payment_reference')
                    ->where('tenant_code', (string)$tenant->tenant_code)
                    ->where('status', 'approved')
                    ->orderBy('request_id', 'DESC')
                    ->get(1)
                    ->getRow();

                if ($request !== null && trim((string)($request->payment_reference ?? '')) === '') {
                    $db->table('subscription_requests')
                        ->where('request_id', (int)$request->request_id)
                        ->update([
                            'payment_reference' => 'Confirmed by Super Admin ' . date('Y-m-d H:i'),
                        ]);
                }
            }
        }

        return redirect()->to('super-admin/businesses');
    }

    public function logout(): RedirectResponse
    {
        model(Platform_admin::class)->logout();
        logout_super_admin_pos_session();
        return redirect()->to('super-admin/login');
    }

    /**
     * Poll for new business registration requests (Super Admin dashboard alerts).
     */
    public function getNotificationPoll()
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $since_id = (int)($this->request->getGet('since') ?? 0);
        $request_model = model(Subscription_request::class);

        $pending_registrations = $request_model->count_pending();
        $new_registrations = $request_model->get_registrations_since_id($since_id);

        $mapped = [];
        foreach ($new_registrations as $row) {
            $mapped[] = [
                'request_id'     => (int)$row['request_id'],
                'company_name'   => (string)($row['company_name'] ?? ''),
                'tenant_code'    => (string)($row['tenant_code'] ?? ''),
                'owner_username' => (string)($row['owner_username'] ?? ''),
                'owner_email'    => (string)($row['owner_email'] ?? ''),
                'plan_name'      => (string)($row['plan_name'] ?? ''),
                'created_at'     => (string)($row['created_at'] ?? ''),
                'review_url'     => site_url('super-admin/requests'),
            ];
        }

        return $this->response->setJSON([
            'pending_registrations'            => $pending_registrations,
            'pending_total'                    => $pending_registrations,
            'latest_registration_request_id'   => $request_model->get_latest_pending_id(),
            'new_registrations'                => $mapped,
        ]);
    }

    public function postCreateAdmin(): RedirectResponse
    {
        return redirect()->to('super-admin?error=admin_creation_disabled');
    }

    public function postApproveRequest(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        $arch = new PlatformArchitecture();
        $arch->ensure();

        $request_model = model(Subscription_request::class);
        $request = $request_model->get_info_for_review($request_id);
        if ($request === null || $request->status !== 'pending') {
            return redirect()->to('super-admin/requests?error=request_not_found');
        }
        if (!model(Subscription_request::class)->is_email_verified($request)) {
            return redirect()->to('super-admin/requests?error=email_not_verified');
        }

        $db = db_connect('platform');
        if ($arch->usernameExists((string)$request->owner_username) || $db->table('tenants')->where('tenant_code', $request->tenant_code)->countAllResults() > 0) {
            return redirect()->to('super-admin/requests?error=tenant_or_user_exists');
        }

        $payment_token = bin2hex(random_bytes(20));
        $db->transStart();

        $tenant_insert = [
            'tenant_code' => $request->tenant_code,
            'company_name' => $request->company_name,
            'status' => 'awaiting_payment',
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

        $db->table('subscriptions')->insert([
            'tenant_id' => $tenant_id,
            'plan_id' => (int)$request->plan_id,
            'status' => 'trialing',
            'trial_ends_at' => null,
            'period_start' => date('Y-m-d H:i:s'),
            'period_end' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'cancel_at_period_end' => 0
        ]);

        $request_update = [
            'status' => 'approved',
            'reviewed_by_admin_id' => (int)session()->get('platform_admin_id'),
            'reviewed_at' => date('Y-m-d H:i:s')
        ];
        if ($db->fieldExists('payment_token', 'subscription_requests')) {
            $request_update['payment_token'] = $payment_token;
        }
        $db->table('subscription_requests')
            ->where('request_id', $request_id)
            ->update($request_update);

        $db->transComplete();

        if (!$db->transStatus() || $tenant_id <= 0) {
            return redirect()->to('super-admin/requests?error=approve_failed');
        }

        $owner = [
            'first_name' => (string)$request->owner_first_name,
            'last_name' => (string)$request->owner_last_name,
            'email' => (string)$request->owner_email,
            'phone' => (string)($request->owner_phone ?? ''),
            'username' => (string)$request->owner_username,
            'password_hash' => (string)$request->owner_password_hash,
            'company_name' => (string)$request->company_name,
            'address' => (string)($request->address ?? ''),
            'city' => (string)($request->city ?? ''),
            'country' => (string)($request->country ?? ''),
            'tax_id' => (string)($request->tax_id ?? ''),
            'business_type' => (string)($request->business_type ?? ''),
        ];

        $provisioned = (new TenantDatabaseProvisioner())->provisionNew($tenant_id, $owner);
        if (empty($provisioned['success'])) {
            $this->seedSharedTenant($tenant_id, $owner);
        } else {
            $this->applyShopProfile($tenant_id, $owner);
        }

        $arch->upsertTenantLogin(
            $tenant_id,
            $this->ownerPersonId($tenant_id, $owner['username']),
            $owner['username'],
            trim($owner['first_name'] . ' ' . $owner['last_name']),
            true
        );

        $pay_url = site_url('saas/pay/' . $payment_token);
        $checkout_url = site_url('saas/checkout');
        $plan = $db->table('plans')->where('plan_id', (int)$request->plan_id)->get(1)->getRow();
        $price = saas_monthly_price((float)($plan->price_monthly ?? 0));
        $mail = (new PlatformMail())->sendKhqrPayment($request, $pay_url, $price);
        session()->setFlashdata('khqr_email_error', $mail['error']);
        (new Telegram_lib())->notify_activation_payment([
            'company_name' => (string)$request->company_name,
            'tenant_code' => (string)$request->tenant_code,
            'owner_phone' => (string)($request->owner_phone ?? ''),
            'owner_email' => (string)$request->owner_email,
            'plan_name' => (string)($plan->plan_name ?? 'POS'),
            'plan_price' => $price,
            'checkout_url' => $checkout_url,
            'pay_url' => $pay_url,
            'qr_path' => FCPATH . 'images/payment/aba-khqr-code.png',
        ]);

        return redirect()->to('super-admin/send-payment/' . $request_id . '?email=' . ($mail['ok'] ? '1' : '0'));
    }

    /**
     * @param array<string, string> $owner
     */
    private function seedSharedTenant(int $tenant_id, array $owner): void
    {
        $db = db_connect('platform');
        $db->table('people')->insert([
            'first_name' => $owner['first_name'],
            'last_name' => $owner['last_name'],
            'gender' => null,
            'phone_number' => $owner['phone'],
            'email' => $owner['email'],
            'address_1' => $owner['address'] ?? '',
            'address_2' => '',
            'city' => $owner['city'] ?? '',
            'state' => '',
            'zip' => '',
            'country' => $owner['country'] ?? '',
            'comments' => '',
            'tenant_id' => $tenant_id
        ]);
        $person_id = (int)$db->insertID();

        $db->table('employees')->insert([
            'person_id' => $person_id,
            'username' => $owner['username'],
            'password' => $owner['password_hash'],
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

        $db->table('tenant_config')->insertBatch($this->shopProfileConfigRows($tenant_id, $owner));

        (new TenantSeeder())->seedForTenant($tenant_id);
        $this->applyShopProfile($tenant_id, $owner);
    }

    /**
     * @param array<string, string> $owner
     * @return list<array{tenant_id:int, config_key:string, config_value:string}>
     */
    private function shopProfileConfigRows(int $tenant_id, array $owner): array
    {
        $address = trim((string)($owner['address'] ?? ''));
        $city = trim((string)($owner['city'] ?? ''));
        $country = trim((string)($owner['country'] ?? ''));
        $full_address = trim($address . ($city !== '' ? "\n" . $city : '') . ($country !== '' ? "\n" . $country : ''));

        $country_codes = [
            'Cambodia' => 'kh',
            'Vietnam' => 'vn',
            'Laos' => 'la',
            'United States' => 'us',
        ];

        return [
            ['tenant_id' => $tenant_id, 'config_key' => 'company', 'config_value' => (string)($owner['company_name'] ?? '')],
            ['tenant_id' => $tenant_id, 'config_key' => 'address', 'config_value' => $full_address],
            ['tenant_id' => $tenant_id, 'config_key' => 'phone', 'config_value' => (string)($owner['phone'] ?? '')],
            ['tenant_id' => $tenant_id, 'config_key' => 'tax_id', 'config_value' => (string)($owner['tax_id'] ?? '')],
            ['tenant_id' => $tenant_id, 'config_key' => 'country_codes', 'config_value' => $country_codes[$country] ?? 'kh'],
            ['tenant_id' => $tenant_id, 'config_key' => 'timezone', 'config_value' => 'Asia/Phnom_Penh'],
            ['tenant_id' => $tenant_id, 'config_key' => 'currency_code', 'config_value' => 'USD'],
        ];
    }

    /**
     * Write store profile into the shop database so receipts show the real company.
     *
     * @param array<string, string> $owner
     */
    private function applyShopProfile(int $tenant_id, array $owner): void
    {
        $context = new TenantContext();
        $context->applyRuntimeConnection($tenant_id);
        try {
            $db = db_connect();
            foreach ($this->shopProfileConfigRows($tenant_id, $owner) as $row) {
                if ($db->tableExists('app_config')) {
                    $db->table('app_config')->replace([
                        'key' => $row['config_key'],
                        'value' => $row['config_value'],
                    ]);
                }
                if ($db->tableExists('tenant_config')) {
                    $db->table('tenant_config')->replace([
                        'tenant_id' => $tenant_id,
                        'config_key' => $row['config_key'],
                        'config_value' => $row['config_value'],
                    ]);
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'Could not apply shop profile: ' . $e->getMessage());
        }
        $context->restoreSharedConnection();
    }

    private function ownerPersonId(int $tenant_id, string $username): int
    {
        $login = db_connect('platform')->table('tenant_logins')
            ->where('tenant_id', $tenant_id)
            ->where('username', $username)
            ->get(1)
            ->getRow();
        if ($login) {
            return (int)$login->person_id;
        }

        $context = new TenantContext();
        $context->applyRuntimeConnection($tenant_id);
        try {
            $row = db_connect()->table('employees')->where('username', $username)->get(1)->getRow();
            $context->restoreSharedConnection();
            return $row ? (int)$row->person_id : 0;
        } catch (\Throwable $e) {
            $context->restoreSharedConnection();
            return 0;
        }
    }

    /**
     * @param list<array<string, mixed>> $tenants
     */
    private function attachPaymentLinks(array &$tenants): void
    {
        $db = db_connect('platform');
        if (!$db->tableExists('subscription_requests') || !$db->fieldExists('payment_token', 'subscription_requests')) {
            return;
        }

        $rows = $db->table('subscription_requests')
            ->select('subscription_requests.request_id, subscription_requests.tenant_code, subscription_requests.payment_token, subscription_requests.payment_reference, subscription_requests.owner_email, subscription_requests.owner_phone, subscription_requests.owner_username, subscription_requests.address, subscription_requests.city, subscription_requests.country, subscription_requests.tax_id, subscription_requests.business_type, subscription_requests.created_at, plans.plan_name')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.status', 'approved')
            ->where('subscription_requests.payment_token !=', '')
            ->get()
            ->getResultArray();

        $by_code = [];
        foreach ($rows as $row) {
            $by_code[(string)$row['tenant_code']] = $row;
        }

        foreach ($tenants as &$tenant) {
            $code = (string)($tenant['tenant_code'] ?? '');
            if (!isset($by_code[$code])) {
                continue;
            }
            $tenant['payment_token'] = (string)$by_code[$code]['payment_token'];
            $tenant['payment_reference'] = (string)($by_code[$code]['payment_reference'] ?? '');
            $tenant['payment_url'] = site_url('saas/pay/' . $tenant['payment_token']);
            $tenant['request_id'] = (int)$by_code[$code]['request_id'];
            $tenant['owner_email'] = (string)($by_code[$code]['owner_email'] ?? '');
            $tenant['owner_phone'] = (string)($by_code[$code]['owner_phone'] ?? '');
            $tenant['owner_username'] = (string)($by_code[$code]['owner_username'] ?? '');
            $tenant['address'] = (string)($by_code[$code]['address'] ?? '');
            $tenant['city'] = (string)($by_code[$code]['city'] ?? '');
            $tenant['country'] = (string)($by_code[$code]['country'] ?? '');
            $tenant['tax_id'] = (string)($by_code[$code]['tax_id'] ?? '');
            $tenant['business_type'] = (string)($by_code[$code]['business_type'] ?? '');
            $tenant['plan_name'] = (string)($by_code[$code]['plan_name'] ?? '');
            $tenant['registered_at'] = (string)($by_code[$code]['created_at'] ?? '');
        }
        unset($tenant);
    }

    public function postRejectRequest(int $request_id): RedirectResponse
    {
        $platform_admin = model(Platform_admin::class);
        if (!$platform_admin->is_logged_in()) {
            return redirect()->to('super-admin/login');
        }

        db_connect('platform')->table('subscription_requests')
            ->where('request_id', $request_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_by_admin_id' => (int)session()->get('platform_admin_id'),
                'reviewed_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->to('super-admin/history?request_rejected=1');
    }
}
