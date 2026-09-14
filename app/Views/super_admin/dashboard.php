<?php
/**
 * @var array $tenants
 * @var array $platform_admins
 * @var bool $is_owner
 * @var array $subscription_requests
 * @var array $subscription_request_history
 * @var array $recent_payments
 * @var array $platform_alerts
 * @var array $tenants
 * @var object|null $logged_in_admin
 * @var string $active_page
 */

$unverified_requests = $unverified_requests ?? [];
$mail_delivery = $mail_delivery ?? \App\Libraries\PlatformMail::deliveryInfo();
$mail_ready = in_array((string)($mail_delivery['mode'] ?? ''), ['gmail', 'smtp'], true);

$format_request_date = static function (?string $value): string {
    if ($value === null || $value === '') {
        return '';
    }

    $timestamp = strtotime($value);

    return $timestamp !== false ? date('Y-m-d', $timestamp) : $value;
};

$format_relative_time = static function (?string $value): string {
    if ($value === null || $value === '') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    $diff = time() - $timestamp;
    if ($diff < 60) {
        return 'Just now';
    }
    if ($diff < 3600) {
        return (int) floor($diff / 60) . ' min ago';
    }
    if ($diff < 86400) {
        return (int) floor($diff / 3600) . ' hours ago';
    }
    if ($diff < 604800) {
        return (int) floor($diff / 86400) . ' days ago';
    }
    if ($diff < 2592000) {
        return (int) floor($diff / 604800) . ' weeks ago';
    }

    return date('Y-m-d', $timestamp);
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= base_url() ?>">
    <title>Super Admin Console</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/layout-sidebar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/responsive.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/super-admin.css?v=56') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme/profile-menu.css?v=3') ?>">
    <link rel="stylesheet" href="<?= base_url('css/password-toggle.css?v=2') ?>">
    <style>
        .sa-mail { display:grid; gap:16px; max-width:920px; }
        .sa-mail-hero { display:flex; gap:16px; align-items:center; padding:20px 22px; border-radius:16px; border:1px solid #e2e8f0; background:#fff; box-shadow:0 1px 2px rgba(15,23,42,.04),0 4px 16px rgba(15,23,42,.04); }
        .sa-mail-hero.is-on { background:linear-gradient(135deg,#ecfdf5 0%,#fff 58%); border-color:#a7f3d0; }
        .sa-mail-hero.is-off { background:linear-gradient(135deg,#f5f3ff 0%,#fff 58%); border-color:#ddd6fe; }
        .sa-mail-hero__icon { width:52px; height:52px; border-radius:14px; display:grid; place-items:center; flex-shrink:0; }
        .sa-mail-hero.is-on .sa-mail-hero__icon { background:#059669; color:#fff; }
        .sa-mail-hero.is-off .sa-mail-hero__icon { background:#7c3aed; color:#fff; }
        .sa-mail-hero__badge { display:inline-block; margin-bottom:6px; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
        .sa-mail-hero.is-on .sa-mail-hero__badge { background:#d1fae5; color:#047857; }
        .sa-mail-hero.is-off .sa-mail-hero__badge { background:#ede9fe; color:#6d28d9; }
        .sa-mail-hero h2 { margin:0 0 6px; font-size:1.2rem; letter-spacing:-.02em; }
        .sa-mail-hero p { margin:0; color:#64748b; }
        .sa-mail-steps { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin:0; padding:0; list-style:none; }
        .sa-mail-steps li { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:16px; box-shadow:0 1px 2px rgba(15,23,42,.04); }
        .sa-mail-steps span { display:grid; place-items:center; width:28px; height:28px; margin-bottom:10px; border-radius:999px; background:#ede9fe; color:#7c3aed; font-size:12px; font-weight:800; }
        .sa-mail-steps strong { display:block; margin-bottom:4px; }
        .sa-mail-steps em { color:#64748b; font-style:normal; font-size:.85rem; }
        .sa-mail-grid { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr); gap:16px; align-items:start; }
        .sa-mail-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; box-shadow:0 1px 2px rgba(15,23,42,.04),0 4px 16px rgba(15,23,42,.04); }
        .sa-mail-card--soft { background:#f8fafc; }
        .sa-mail-card h3 { margin:0 0 4px; font-size:1.02rem; }
        .sa-mail-card .sa-mail-card__head p { margin:0 0 16px; color:#64748b; font-size:.88rem; }
        .sa-mail-form { display:grid; gap:6px; }
        .sa-mail-label { font-size:12px; font-weight:700; color:#475569; }
        .sa-mail .sa-input { margin-bottom:8px; background:#fff; }
        .sa-mail-help { margin:0 0 12px; color:#7c3aed; font-size:.82rem; font-weight:700; text-decoration:none; }
        .sa-mail-help:hover { text-decoration:underline; }
        .sa-mail .sa-btn { height:42px; width:100%; border-radius:10px; }
        .sa-modal--pos-password {
            width: min(560px, 100%);
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            box-shadow: 0 12px 40px rgba(124, 58, 237, 0.1);
            overflow: hidden;
        }
        .sa-pos-password__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            background: #f5f3ff;
            border-bottom: 1px solid #ddd6fe;
        }
        .sa-pos-password__title {
            margin: 0;
            color: #5b21b6;
            font-size: 1.05rem;
            font-weight: 600;
        }
        .sa-pos-password__close {
            border: 0;
            background: transparent;
            color: #7c3aed;
            font-size: 1.5rem;
            line-height: 1;
            opacity: 0.7;
            cursor: pointer;
            padding: 0 4px;
        }
        .sa-pos-password__close:hover,
        .sa-pos-password__close:focus {
            opacity: 1;
            color: #6d28d9;
        }
        .sa-pos-password__body {
            padding: 16px 18px 8px;
            background: #fff;
        }
        .sa-pos-password__required {
            margin: 0 0 14px;
            font-style: italic;
            color: #64748b;
            font-size: 0.88rem;
        }
        .sa-change-password__error {
            margin: 0 0 12px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 0.88rem;
            font-weight: 600;
        }
        .sa-pos-password__row {
            display: grid;
            grid-template-columns: minmax(120px, 28%) minmax(0, 1fr);
            gap: 10px 12px;
            align-items: start;
            margin-bottom: 14px;
        }
        .sa-pos-password__label {
            padding-top: 8px;
            text-align: right;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .sa-pos-password__label.required::after {
            content: " *";
            color: #dc2626;
        }
        .sa-pos-input-group {
            display: flex;
            align-items: stretch;
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }
        .sa-pos-input-group:focus-within {
            border-color: #a78bfa;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
        }
        .sa-pos-input-group__addon {
            display: grid;
            place-items: center;
            width: 38px;
            flex-shrink: 0;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            color: #475569;
        }
        .sa-pos-input-group__addon svg {
            width: 16px;
            height: 16px;
        }
        .sa-pos-input-group__input {
            flex: 1 1 auto;
            min-width: 0;
            border: 0;
            outline: none;
            padding: 8px 10px;
            font-size: 0.92rem;
            color: #0f172a;
            background: #fff;
        }
        .sa-pos-input-group__input[readonly] {
            background: #f1f5f9;
            color: #475569;
        }
        .sa-pos-password__help {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 0.82rem;
        }
        .sa-pos-password__footer {
            display: flex;
            justify-content: flex-end;
            padding: 12px 16px;
            background: #fafafa;
            border-top: 1px solid #e2e8f0;
        }
        .sa-pos-password__submit {
            min-width: 96px;
            height: 38px;
            padding: 0 18px;
            border: 0;
            border-radius: 8px;
            background: #7c3aed;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
        }
        .sa-pos-password__submit:hover,
        .sa-pos-password__submit:focus {
            background: #6d28d9;
        }
        .sa-pos-password__submit:disabled {
            opacity: 0.65;
            cursor: wait;
        }
        .sa-pos-input-group.input-group {
            display: flex;
        }
        .sa-pos-input-group .password-toggle__addon,
        .sa-pos-input-group > .input-group-addon.password-toggle__addon {
            display: grid;
            place-items: center;
            width: 38px;
            flex-shrink: 0;
            background: #f8fafc;
            border: 0;
            border-left: 1px solid #e2e8f0;
            color: #64748b;
            padding: 0;
        }
        .sa-pos-input-group .password-toggle__btn {
            width: 100%;
            height: 100%;
            min-height: 36px;
            border: 0;
            background: transparent;
            color: inherit;
            cursor: pointer;
            display: grid;
            place-items: center;
            padding: 0;
        }
        .sa-pos-input-group .password-toggle__btn svg {
            width: 18px;
            height: 18px;
        }
        .sa-pos-input-group__input.form-control {
            box-shadow: none;
            border-radius: 0;
            height: auto;
        }
        @media (max-width: 560px) {
            .sa-pos-password__row {
                grid-template-columns: 1fr;
                gap: 6px;
            }
            .sa-pos-password__label {
                text-align: left;
                padding-top: 0;
            }
        }
        @media (max-width:860px) {
            .sa-mail-hero { align-items:flex-start; }
            .sa-mail-steps, .sa-mail-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body class="sa-dashboard">
<div id="sa_toast_stack" class="sa-toast-stack" aria-live="polite" aria-atomic="true"></div>
<script>
    (function() {
        const key = 'ospos_sidebar_collapsed';
        if (window.innerWidth > 992 && localStorage.getItem(key) === '1') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    })();
</script>
<?php
    $total_tenants = count($tenants);
    $pending_count = count($subscription_requests);
    $admins_count = count($platform_admins);
    $active_page = $active_page ?? 'overview';
    $subscription_plans = $subscription_plans ?? [];
    $plan_feature_matrix = $plan_feature_matrix ?? [];
    $template_meta = $template_meta ?? ['template_version' => '1', 'last_sync_at' => null];
    $template_sync = $template_sync ?? null;
    $isolate_report = $isolate_report ?? null;
    $subscription_request_history = $subscription_request_history ?? [];

    $active_tenants = 0;
    $suspended_tenants = 0;
    $cancelled_tenants = 0;
    $awaiting_payment_tenants = 0;
    $expired_tenants = 0;
    $expiring_soon_tenants = 0;
    $isolated_tenants = 0;
    $shared_tenants = 0;
    $platform_db_name = (string)(config('Database')->platform['database'] ?? 'wbpos');
    $tenant_is_isolated = static function (array $tenant) use ($platform_db_name): bool {
        $name = trim((string)($tenant['db_name'] ?? ''));

        return $name !== '' && strcasecmp($name, $platform_db_name) !== 0;
    };
    foreach ($tenants as $tenant) {
        $status = strtolower((string)($tenant['status'] ?? ''));
        $billing = strtolower((string)($tenant['billing'] ?? 'none'));
        if ($status === 'active') {
            $active_tenants++;
        } elseif ($status === 'suspended') {
            $suspended_tenants++;
        } elseif ($status === 'cancelled') {
            $cancelled_tenants++;
        } elseif ($status === 'awaiting_payment') {
            $awaiting_payment_tenants++;
        }
        if ($billing === 'expired') {
            $expired_tenants++;
        } elseif ($billing === 'warning') {
            $expiring_soon_tenants++;
        }
        if ($tenant_is_isolated($tenant)) {
            $isolated_tenants++;
        } else {
            $shared_tenants++;
        }
    }

    $page_meta = [
        'overview' => [
            'title' => 'Tenant Management',
            'subtitle' => 'Control business accounts, platform admins, and signup approvals.',
        ],
        'businesses' => [
            'title' => 'Businesses',
            'subtitle' => 'View and update tenant status for every registered company.',
        ],
        'admins' => [
            'title' => 'Platform Admins',
            'subtitle' => 'Accounts allowed to operate this platform dashboard.',
        ],
        'requests' => [
            'title' => 'New shop applications',
            'subtitle' => 'Approve → send KHQR → owner pays → then login.',
        ],
        'email' => [
            'title' => 'Email',
            'subtitle' => 'Save Gmail once. After that, every new registration emails the owner a verify link automatically.',
        ],
        'history' => [
            'title' => 'Request History',
            'subtitle' => 'View approved and rejected website registrations.',
        ],
        'features' => [
            'title' => 'Master POS Features',
            'subtitle' => 'Global kill switches for the shared POS template. Plan assignment is on Plans & Sync.',
        ],
        'plans' => [
            'title' => 'Plans & Template Sync',
            'subtitle' => 'One $20 plan with every POS module. Push template updates without touching shop sales data.',
        ],
        'feature' => [
            'title' => $current_feature['label'] ?? 'Feature',
            'subtitle' => 'This feature is part of the shared WBPOS system. Super Admin cannot see another shop’s customers, items, or sales.',
        ],
    ];
    $current_meta = $page_meta[$active_page] ?? $page_meta['overview'];
    $page_eyebrow = in_array($active_page, ['features', 'feature', 'plans'], true) ? 'Master Template' : 'Subscriptions';

    $flash_messages = [];
    if (service('request')->getGet('request_approved') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Shop approved. Owner still cannot log in until paid.'];
    }
    if (service('request')->getGet('request_rejected') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Registration request rejected.'];
    }
    if (service('request')->getGet('plan_updated') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Plan feature assignment saved. Shops receive it on their next login or refresh.'];
    }
    if (service('request')->getGet('template_synced') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Template sync completed. Shop sales, stock, and customers were not changed.'];
    }
    if (service('request')->getGet('tenants_isolated') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Shop databases were provisioned. Each business now has a private database.'];
    }
    if (service('request')->getGet('tenant_isolated') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'This shop now has its own isolated database.'];
    }
    if (service('request')->getGet('gmail_saved') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Gmail saved. New registrations will send a verify link to the owner.'];
    }
    if (service('request')->getGet('gmail_test') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Test email sent. Check that inbox (and Spam).'];
    }
    if (service('request')->getGet('verify_sent') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Verification email sent to the owner. They must click the link in Gmail.'];
    }
    if (service('request')->getGet('password_changed') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Password changed successfully.'];
    }
    if (service('request')->getGet('admin_created') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Platform admin created.'];
    }
    if (service('request')->getGet('admin_updated') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Platform admin status updated.'];
    }
    if (service('request')->getGet('extended') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Subscription extended.'];
    }
    if (service('request')->getGet('expiry_set') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Subscription expiry date saved.'];
    }
    if (service('request')->getGet('renewed') === '1') {
        $flash_messages[] = ['type' => 'success', 'text' => 'Payment confirmed and subscription renewed. Invoice recorded.'];
    }

    $gmail_error = trim((string)session()->getFlashdata('gmail_error'));
    if ($gmail_error !== '') {
        $flash_messages[] = ['type' => 'error', 'text' => $gmail_error];
    }

    $error_code = (string)service('request')->getGet('error');
    $error_messages = [
        'request_not_found' => 'Request not found or already processed.',
        'email_not_verified' => 'This owner has not clicked the verify link in their email yet.',
        'verify_not_sent' => 'Could not send the verify email. Save Gmail on Email settings, then send again.',
        'tenant_or_user_exists' => 'Tenant code or owner username already exists.',
        'approve_failed' => 'Could not approve the request. Please try again.',
        'reject_comment_required' => 'Enter a rejection reason (at least 3 characters).',
        'feature_update_failed' => 'Could not update the feature. Please try again.',
        'isolate_failed' => 'Could not create a private database for this shop. Check MySQL CREATE DATABASE privileges.',
        'admin_not_allowed' => 'Only the owner Super Admin can manage platform admins.',
        'admin_invalid' => 'Enter a valid username (3–40 chars), full name, and password (8+ chars).',
        'admin_exists' => 'That admin username already exists.',
        'admin_create_failed' => 'Could not create the admin. Please try again.',
        'admin_self' => 'You cannot disable your own account.',
        'admin_owner_locked' => 'The owner Super Admin account cannot be disabled.',
        'admin_update_failed' => 'Could not update admin status.',
        'tenant_not_found' => 'Shop not found.',
        'expiry_invalid' => 'Enter a valid expiry date (YYYY-MM-DD).',
    ];
    if ($error_code !== '' && isset($error_messages[$error_code])) {
        $flash_messages[] = ['type' => 'error', 'text' => $error_messages[$error_code]];
    }

    $admin = $logged_in_admin ?? null;
    $admin_display_name = trim((string)($admin->full_name ?? ''));
    if ($admin_display_name === '') {
        $admin_display_name = trim((string)($admin->username ?? 'Super Admin'));
    }
    $admin_username = trim((string)($admin->username ?? ''));
    $admin_email = trim((string)($admin->email ?? ''));
    $admin_initials = '';
    foreach (preg_split('/\s+/', $admin_display_name) ?: [] as $part) {
        if ($part !== '') {
            $admin_initials .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($admin_initials) >= 2) {
            break;
        }
    }
    if ($admin_initials === '') {
        $admin_initials = 'SA';
    }

    $recent_payments = $recent_payments ?? [];
    $platform_alerts = $platform_alerts ?? [];

    $notification_items = [];

    // Paid / renewed first — so Super Admin sees who continues using the system.
    foreach ($platform_alerts as $alert) {
        $type = (string)($alert['alert_type'] ?? 'renewed');
        if ($type === '') {
            $type = 'renewed';
        }
        $created = (string)($alert['created_at'] ?? '');
        $link = trim((string)($alert['link_path'] ?? 'super-admin/businesses'));
        if ($link !== '' && strpos($link, 'http') !== 0) {
            $link = site_url(ltrim($link, '/'));
        }
        $notification_items[] = [
            'type' => $type === 'renewed' || $type === 'payment' ? 'renewed' : $type,
            'id' => (int)($alert['alert_id'] ?? 0),
            'key' => 'alert-' . (string)($alert['dedupe_key'] ?? ($alert['alert_id'] ?? uniqid('a', true))),
            'title' => (string)($alert['title'] ?? 'Payment received'),
            'subtitle' => (string)($alert['company_name'] ?? 'Shop'),
            'body' => (string)($alert['body'] ?? ''),
            'meta' => (string)($alert['meta'] ?? ''),
            'created_at' => $format_request_date($created),
            'relative_time' => $format_relative_time($created),
            'review_url' => $link !== '' ? $link : site_url('super-admin/businesses'),
        ];
    }

    foreach ($subscription_requests as $request) {
        $owner = format_person_name($request['owner_first_name'] ?? '', $request['owner_last_name'] ?? '');
        $notification_items[] = [
            'type' => 'registration',
            'id' => (int)$request['request_id'],
            'key' => 'registration-' . (int)$request['request_id'],
            'title' => 'New registration',
            'subtitle' => (string)($request['company_name'] ?? 'New registration'),
            'body' => 'Needs Approve & send KHQR. Owner: ' . ($owner !== '' ? $owner : ($request['owner_username'] ?? '')) . '. Plan: ' . ($request['plan_name'] ?? 'N/A') . '.',
            'meta' => (string)($request['owner_email'] ?? ''),
            'created_at' => $format_request_date($request['created_at'] ?? ''),
            'relative_time' => $format_relative_time($request['created_at'] ?? ''),
            'review_url' => site_url('super-admin/requests'),
        ];
    }

    foreach ($tenants as $tenant) {
        $status = strtolower((string)($tenant['status'] ?? ''));
        $billing = strtolower((string)($tenant['billing'] ?? 'none'));
        $company = trim((string)($tenant['company_name'] ?? ''));
        if ($company === '') {
            $company = 'Shop #' . (int)($tenant['tenant_id'] ?? 0);
        }
        $code = trim((string)($tenant['tenant_code'] ?? ''));
        $tid = (int)($tenant['tenant_id'] ?? 0);
        $period_end = (string)($tenant['period_end'] ?? '');
        $days_left = $tenant['days_left'] ?? null;

        if ($status === 'awaiting_payment') {
            $notification_items[] = [
                'type' => 'payment',
                'id' => $tid,
                'key' => 'awaiting-payment-' . $tid,
                'title' => 'Waiting for $20 payment',
                'subtitle' => $company,
                'body' => ($code !== '' ? $code . ' · ' : '') . 'Approved. Waiting for shop to pay KHQR.',
                'meta' => (string)($tenant['email'] ?? $tenant['owner_email'] ?? ''),
                'created_at' => $format_request_date((string)($tenant['registered_at'] ?? $tenant['created_at'] ?? '')),
                'relative_time' => $format_relative_time((string)($tenant['registered_at'] ?? $tenant['created_at'] ?? '')),
                'review_url' => site_url('super-admin/businesses?status=awaiting_payment'),
            ];
        }

        if ($billing === 'expired' && !in_array($status, ['cancelled', 'suspended'], true)) {
            $notification_items[] = [
                'type' => 'expired',
                'id' => $tid,
                'key' => 'expired-' . $tid . '-' . $period_end,
                'title' => 'Subscription expired',
                'subtitle' => $company,
                'body' => ($code !== '' ? $code . ' · ' : '') . 'Period ended ' . saas_format_period_end($period_end) . '. Account still open — needs renew or suspend.',
                'meta' => '',
                'created_at' => $period_end,
                'relative_time' => $period_end !== '' ? ('Ended ' . saas_format_period_end($period_end)) : '',
                'review_url' => site_url('super-admin/businesses?status=expired'),
            ];
        } elseif ($billing === 'warning' && $status === 'active') {
            $notification_items[] = [
                'type' => 'warning',
                'id' => $tid,
                'key' => 'expiring-' . $tid . '-' . $period_end,
                'title' => 'Expiring soon',
                'subtitle' => $company,
                'body' => ($code !== '' ? $code . ' · ' : '') . (int)$days_left . ' day(s) left · ends ' . saas_format_period_end($period_end) . '.',
                'meta' => '',
                'created_at' => $period_end,
                'relative_time' => (int)$days_left . 'd left',
                'review_url' => site_url('super-admin/businesses?status=expiring_soon'),
            ];
        }
    }

    // Fallback: invoice_payments not already represented by platform_alerts.
    $alert_keys = [];
    foreach ($platform_alerts as $alert) {
        $alert_keys[(string)($alert['dedupe_key'] ?? '')] = true;
        $alert_keys['paid-invoice-' . (int)($alert['alert_id'] ?? 0)] = true;
    }
    foreach ($recent_payments as $payment) {
        $pay_id = (int)($payment['payment_id'] ?? 0);
        $dedupe = 'paid-invoice-' . $pay_id;
        if (isset($alert_keys[$dedupe]) || $pay_id <= 0) {
            continue;
        }
        $company = trim((string)($payment['company_name'] ?? ''));
        if ($company === '') {
            $company = 'Shop #' . (int)($payment['tenant_id'] ?? 0);
        }
        $code = trim((string)($payment['tenant_code'] ?? ''));
        $ref = trim((string)($payment['provider_payment_id'] ?? ''));
        $amount = $payment['amount'] ?? '';
        $notification_items[] = [
            'type' => 'renewed',
            'id' => $pay_id,
            'key' => 'payment-' . $pay_id,
            'title' => 'Payment received',
            'subtitle' => $company,
            'body' => ($code !== '' ? $code . ' · ' : '') . 'Paid $' . (string)$amount . ($ref !== '' ? ' · Ref: ' . $ref : '') . '. Shop continues on the system.',
            'meta' => (string)($payment['provider'] ?? ''),
            'created_at' => $format_request_date((string)($payment['paid_at'] ?? '')),
            'relative_time' => $format_relative_time((string)($payment['paid_at'] ?? '')),
            'review_url' => site_url('super-admin/businesses'),
        ];
    }

    $notify_count = count($notification_items);
?>
<div class="neo-layout sa-layout">
    <aside class="neo-global-sidebar sa-sidebar">
        <div class="neo-global-brand-row">
            <a class="neo-global-brand" href="<?= site_url('super-admin/overview') ?>">
                <span class="neo-global-brand-full">WBPOS</span>
                <span class="neo-global-brand-mini">W</span>
            </a>
            <button id="sa_sidebar_toggle" class="neo-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">
                <svg class="neo-sidebar-toggle__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        </div>
        <div class="neo-global-sidebar-body">
                    <nav class="neo-global-menu">
                        <?= view('partial/super_admin_nav', [
                            'sa_active' => $active_page,
                            'pos_modules' => super_admin_pos_nav_modules(),
                        ]) ?>
                    </nav>
            <div class="neo-sidebar-footer">
                <a class="neo-sidebar-logout js-super-admin-logout" href="<?= site_url('super-admin/logout') ?>" title="Logout">
                    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/logout.svg') ?>" alt=""></span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>
    <div id="sa_sidebar_backdrop" class="neo-sidebar-backdrop" aria-hidden="true"></div>

    <main class="neo-global-content sa-main">
        <header class="sa-top-navbar">
            <div class="sa-top-navbar__start">
                <button id="sa_mobile_sidebar_toggle" class="neo-mobile-menu-toggle sa-top-navbar__menu" type="button" aria-label="Open menu" aria-expanded="false">
                    <span class="neo-hamburger-icon" aria-hidden="true"></span>
                </button>
            </div>
            <div class="sa-top-navbar__end">
                <div class="sa-top-navbar__search">
                    <div class="sa-search-wrap sa-search-wrap--pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                        </svg>
                        <input type="text" id="sa_navbar_search" class="sa-input sa-input--pill" placeholder="Search businesses, admins, requests..." autocomplete="off">
                    </div>
                </div>
                <div class="sa-top-navbar__actions">
                    <button type="button" class="sa-notify-btn" id="sa_notify_btn" aria-label="Notifications" aria-expanded="false" aria-controls="sa_notify_panel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="sa-notify-badge<?= $notify_count > 0 ? '' : ' sa-notify-badge--hidden' ?>" id="sa_navbar_badge"><?= $notify_count ?></span>
                    </button>
                <div class="sa-dropdown-wrap">
                    <button type="button" class="sa-profile-btn" id="sa_profile_btn" aria-label="Super admin profile" aria-expanded="false" aria-haspopup="true">
                        <span class="sa-profile-avatar" aria-hidden="true"><?= esc($admin_initials) ?></span>
                    </button>
                    <div class="sa-dropdown sa-dropdown--profile" id="sa_profile_dropdown" hidden>
                        <div class="sa-profile-card">
                            <span class="sa-profile-card__avatar" aria-hidden="true"><?= esc($admin_initials) ?></span>
                            <div class="sa-profile-card__copy">
                                <strong><?= esc($admin_display_name) ?></strong>
                                <?php if ($admin_username !== ''): ?>
                                    <span>@<?= esc($admin_username) ?></span>
                                <?php endif; ?>
                                <?php if ($admin_email !== ''): ?>
                                    <span><?= esc($admin_email) ?></span>
                                <?php endif; ?>
                                <span class="sa-profile-card__role"><?= !empty($is_owner) ? 'Platform Owner' : 'Platform Admin' ?></span>
                            </div>
                        </div>
                        <div class="sa-dropdown__menu">
                            <a class="sa-dropdown__menu-item" href="#" id="sa_change_password_btn" role="button">Change Password</a>
                            <a class="sa-dropdown__menu-item" href="<?= site_url('super-admin/admins') ?>">Platform Admins</a>
                            <a class="sa-dropdown__menu-item js-super-admin-logout" href="<?= site_url('super-admin/logout') ?>">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </header>
        <div class="sa-main-body">
        <header class="sa-page-header">
            <div class="sa-page-header__content">
                <p class="sa-page-header__eyebrow"><?= esc($page_eyebrow) ?></p>
                <h1 class="sa-page-header__title"><?= esc($current_meta['title']) ?></h1>
                <p class="sa-page-header__subtitle"><?= esc($current_meta['subtitle']) ?></p>
            </div>
            <?php if (in_array($active_page, ['businesses', 'admins', 'requests', 'history'], true)): ?>
            <div class="sa-toolbar sa-toolbar--filter-only">
                <?php if ($active_page === 'businesses'): ?>
                <select id="super_admin_status_filter" class="sa-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="awaiting_payment">Approved · waiting $20</option>
                    <option value="suspended">Suspended</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="expired">Expired period</option>
                    <option value="expiring_soon">Expiring soon</option>
                </select>
                <?php elseif ($active_page === 'history'): ?>
                <select id="super_admin_status_filter" class="sa-select">
                    <option value="">All History</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <?php else: ?>
                <select id="super_admin_status_filter" class="sa-select" hidden aria-hidden="true">
                    <option value=""></option>
                </select>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </header>

        <?php foreach ($flash_messages as $flash): ?>
            <div class="sa-alert sa-alert--<?= esc($flash['type']) ?>">
                <?= esc($flash['text']) ?>
            </div>
        <?php endforeach; ?>
        <?php $activation_pay_url = trim((string)($activation_pay_url ?? '')); ?>
        <?php if ($activation_pay_url !== ''): ?>
            <div class="sa-alert sa-alert--info sa-pay-flash">
                <p>
                    <?php if ((string)($activation_email_ok ?? '') === '1'): ?>
                        Payment email sent. Also copy this KHQR link and send it to the owner if needed:
                    <?php else: ?>
                        Email may not have sent (SMTP is often off on local XAMPP). Copy this KHQR payment link and send it to the owner:
                    <?php endif; ?>
                </p>
                <div class="sa-copy-row">
                    <input class="sa-copy-row__input" id="sa_activation_pay_url" type="text" readonly value="<?= esc($activation_pay_url, 'attr') ?>">
                    <button type="button" class="sa-btn sa-btn--primary js-copy-pay-url" data-target="sa_activation_pay_url">Copy link</button>
                    <a class="sa-btn sa-btn--ghost" href="<?= esc($activation_pay_url, 'attr') ?>" target="_blank" rel="noopener">Open</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($active_page === 'overview'): ?>
        <section class="sa-stat-grid">
            <a class="sa-stat-card sa-stat-card--purple" href="<?= site_url('super-admin/businesses') ?>" title="View all registered businesses">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">Total Businesses</p>
                    <p class="sa-stat-card__value"><?= $total_tenants ?></p>
                    <p class="sa-stat-card__hint">View all businesses →</p>
                </div>
            </a>
            <a class="sa-stat-card sa-stat-card--amber" href="<?= site_url('super-admin/requests') ?>" title="View pending registration requests">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3H10l-2-3H2"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">New applications</p>
                    <p class="sa-stat-card__value" id="sa_pending_stat_value"><?= $pending_count ?></p>
                    <p class="sa-stat-card__hint">View pending requests →</p>
                </div>
            </a>
            <a class="sa-stat-card sa-stat-card--blue" href="<?= site_url('super-admin/admins') ?>" title="View platform admin accounts">
                <div class="sa-stat-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="sa-stat-card__body">
                    <p class="sa-stat-card__label">Platform Admins</p>
                    <p class="sa-stat-card__value"><?= $admins_count ?></p>
                    <p class="sa-stat-card__hint">View platform admins →</p>
                </div>
            </a>
        </section>

        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Tenant Status Breakdown</h2>
                <p class="sa-panel__subtitle">How your <?= $total_tenants ?> registered businesses are split by status.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-metrics sa-metrics--status">
                    <a class="sa-metric sa-metric--active" href="<?= site_url('super-admin/businesses?status=active') ?>" title="View active businesses">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Active</span>
                        </div>
                        <div class="sa-metric__value"><?= $active_tenants ?></div>
                        <div class="sa-metric__hint">View active businesses →</div>
                    </a>
                    <a class="sa-metric sa-metric--suspended" href="<?= site_url('super-admin/businesses?status=suspended') ?>" title="View suspended businesses">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Suspended</span>
                        </div>
                        <div class="sa-metric__value"><?= $suspended_tenants ?></div>
                        <div class="sa-metric__hint">View suspended businesses →</div>
                    </a>
                    <a class="sa-metric sa-metric--cancelled" href="<?= site_url('super-admin/businesses?status=cancelled') ?>" title="View cancelled businesses">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Cancelled</span>
                        </div>
                        <div class="sa-metric__value"><?= $cancelled_tenants ?></div>
                        <div class="sa-metric__hint">View cancelled businesses →</div>
                    </a>
                    <a class="sa-metric sa-metric--suspended" href="<?= site_url('super-admin/businesses?status=awaiting_payment') ?>" title="View shops waiting to pay">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Approved · waiting $20</span>
                        </div>
                        <div class="sa-metric__value"><?= $awaiting_payment_tenants ?></div>
                        <div class="sa-metric__hint">View unpaid activations →</div>
                    </a>
                    <a class="sa-metric sa-metric--cancelled" href="<?= site_url('super-admin/businesses?status=expired') ?>" title="View shops with expired period">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Expired</span>
                        </div>
                        <div class="sa-metric__value"><?= $expired_tenants ?></div>
                        <div class="sa-metric__hint">Period ended →</div>
                    </a>
                    <a class="sa-metric sa-metric--suspended" href="<?= site_url('super-admin/businesses?status=expiring_soon') ?>" title="View shops expiring within 7 days">
                        <div class="sa-metric__top">
                            <span class="sa-metric__dot"></span>
                            <span class="sa-metric__label">Expiring soon</span>
                        </div>
                        <div class="sa-metric__value"><?= $expiring_soon_tenants ?></div>
                        <div class="sa-metric__hint">Within 7 days →</div>
                    </a>
                </div>
            </div>
        </section>

        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Quick Actions</h2>
                <p class="sa-panel__subtitle">Jump to the pages you use most often.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-actions">
                    <a class="sa-action-card" href="<?= site_url('super-admin/requests') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--amber" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3H10l-2-3H2"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Pending Registrations<?= $pending_count > 0 ? ' (' . $pending_count . ')' : '' ?></strong>
                            <span>Review and approve incoming business signups.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="sa-action-card" href="<?= site_url('super-admin/businesses') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--purple" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Manage Businesses<?= $suspended_tenants > 0 ? ' (' . $suspended_tenants . ' suspended)' : '' ?></strong>
                            <span>Update tenant status and view owner accounts.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                    <a class="sa-action-card" href="<?= site_url('super-admin/admins') ?>">
                        <span class="sa-action-card__icon sa-action-card__icon--blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        </span>
                        <span class="sa-action-card__text">
                            <strong>Platform Admins (<?= $admins_count ?>)</strong>
                            <span>View who can access this console.</span>
                        </span>
                        <span class="sa-action-card__arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'businesses'): ?>
        <?php
            $sa_display_text = static function (?string $value): string {
                $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
            };
        ?>
        <section class="sa-panel sa-panel--businesses">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">All Businesses</h2>
                <p class="sa-panel__subtitle"><?= (int) $total_tenants ?> shops · expiry from each shop’s subscription period</p>
            </div>
            <div class="sa-table-wrap sa-table-wrap--stack">
                <table class="sa-table sa-table--businesses">
                    <thead>
                    <tr>
                        <th>Business</th>
                        <th>Owner</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($tenants)): ?>
                        <tr><td colspan="5" class="sa-empty">No businesses yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($tenants as $tenant): ?>
                        <?php
                            $status = strtolower((string)($tenant['status'] ?? ''));
                            $billing = strtolower((string)($tenant['billing'] ?? 'none'));
                            $period_end = (string)($tenant['period_end'] ?? '');
                            $days_left = $tenant['days_left'] ?? null;
                            $company_name = $sa_display_text($tenant['company_name'] ?? '');
                            $owner_name = $sa_display_text(format_person_name($tenant['first_name'] ?? '', $tenant['last_name'] ?? ''));
                            $username = trim((string)($tenant['username'] ?? ''));
                            $tenant_code = trim((string)($tenant['tenant_code'] ?? ''));

                            // Account status + billing period — avoid "Active" alone when period expired.
                            if ($status === 'awaiting_payment') {
                                $status_label = 'Waiting payment';
                                $status_class = 'pending';
                            } elseif ($status === 'suspended') {
                                $status_label = 'Suspended';
                                $status_class = 'suspended';
                            } elseif ($status === 'cancelled') {
                                $status_label = 'Cancelled';
                                $status_class = 'cancelled';
                            } elseif ($status === 'active' && $billing === 'expired') {
                                $status_label = 'Active · Expired';
                                $status_class = 'expired';
                            } elseif ($status === 'active' && $billing === 'warning') {
                                $status_label = 'Active · Expiring soon';
                                $status_class = 'pending';
                            } elseif ($status === 'active') {
                                $status_label = 'Active';
                                $status_class = 'active';
                            } else {
                                $status_label = (string)($tenant['status'] ?? '—');
                                $status_class = $status !== '' ? $status : 'pending';
                            }
                        ?>
                        <tr class="js-searchable-row<?= $billing === 'expired' ? ' sa-biz-row--expired' : ($billing === 'warning' ? ' sa-biz-row--warning' : '') ?>"
                            data-group="tenant"
                            data-status="<?= esc($status) ?>"
                            data-billing="<?= esc($billing) ?>"
                            data-search="<?= esc(strtolower(trim($tenant_code . ' ' . $company_name . ' ' . ($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '') . ' ' . $username . ' ' . ($tenant['email'] ?? '') . ' ' . ($tenant['owner_email'] ?? '') . ' ' . ($tenant['phone_number'] ?? '')))) ?>">
                            <td data-label="Business">
                                <div class="sa-biz-cell">
                                    <strong class="sa-biz-cell__title"><?= esc($company_name !== '' ? $company_name : 'Untitled shop') ?></strong>
                                    <span class="sa-biz-cell__sub"><?= esc($tenant_code) ?> · #<?= esc($tenant['tenant_id']) ?></span>
                                </div>
                            </td>
                            <td data-label="Owner">
                                <div class="sa-biz-cell">
                                    <strong class="sa-biz-cell__title"><?= esc($owner_name !== '' ? $owner_name : '—') ?></strong>
                                    <?php if ($username !== ''): ?>
                                        <span class="sa-biz-cell__sub">@<?= esc($username) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Expires">
                                <?php if ($period_end === ''): ?>
                                    <span class="sa-muted">—</span>
                                <?php else: ?>
                                    <div class="sa-expiry">
                                        <strong><?= esc(saas_format_period_end($period_end)) ?></strong>
                                        <?php if ($billing === 'expired'): ?>
                                            <span class="sa-status sa-status--expired">Expired</span>
                                        <?php elseif ($billing === 'warning'): ?>
                                            <span class="sa-status sa-status--pending"><?= (int)$days_left ?>d left</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Status">
                                <span class="sa-status sa-status--<?= esc($status_class) ?>"><?= esc($status_label) ?></span>
                            </td>
                            <td data-label="Action">
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-kind="business"
                                            data-title="<?= esc($company_name !== '' ? $company_name : ('Business #' . (int)$tenant['tenant_id']), 'attr') ?>"
                                            data-id="<?= esc((string)$tenant['tenant_id'], 'attr') ?>"
                                            data-company="<?= esc($company_name, 'attr') ?>"
                                            data-code="<?= esc($tenant_code, 'attr') ?>"
                                            data-type="<?= esc(saas_business_type_label($tenant['business_type'] ?? ''), 'attr') ?>"
                                            data-address="<?= esc($sa_display_text((string)($tenant['address'] ?? $tenant['address_1'] ?? '')), 'attr') ?>"
                                            data-city="<?= esc($sa_display_text((string)($tenant['city'] ?? '')), 'attr') ?>"
                                            data-country="<?= esc($sa_display_text((string)($tenant['country'] ?? '')), 'attr') ?>"
                                            data-tax="<?= esc((string)($tenant['tax_id'] ?? ''), 'attr') ?>"
                                            data-owner="<?= esc($owner_name, 'attr') ?>"
                                            data-email="<?= esc((string)($tenant['email'] ?? $tenant['owner_email'] ?? ''), 'attr') ?>"
                                            data-phone="<?= esc((string)($tenant['phone_number'] ?? $tenant['owner_phone'] ?? ''), 'attr') ?>"
                                            data-username="<?= esc($username !== '' ? $username : (string)($tenant['owner_username'] ?? ''), 'attr') ?>"
                                            data-plan="<?= esc((string)($tenant['plan_name'] ?? ''), 'attr') ?>"
                                            data-payment="<?= esc((string)($tenant['payment_reference'] ?? ''), 'attr') ?>"
                                            data-expires="<?= esc(saas_format_period_end($period_end), 'attr') ?>"
                                            data-status="<?= esc((string)$tenant['status'], 'attr') ?>"
                                            data-created="<?= esc($format_request_date((string)($tenant['registered_at'] ?? $tenant['created_at'] ?? '')), 'attr') ?>">
                                        View
                                    </button>
                                    <?= form_open('super-admin/toggle-status/' . (int)$tenant['tenant_id'], ['class' => 'js-tenant-status-form sa-row-actions__form']) ?>
                                    <select class="sa-select--sm" name="status" aria-label="Status">
                                        <option value="active" <?= $tenant['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="awaiting_payment" <?= $tenant['status'] === 'awaiting_payment' ? 'selected' : '' ?>>Approved · waiting $20</option>
                                        <option value="suspended" <?= $tenant['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                        <option value="cancelled" <?= $tenant['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="tenant_code" value="<?= esc($tenant_code) ?>">
                                    <button class="sa-btn sa-btn--primary" type="submit">Save</button>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'admins'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Platform Admins</h2>
                <p class="sa-panel__subtitle">Accounts allowed to operate this platform dashboard.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($platform_admins)): ?>
                        <tr><td colspan="5" class="sa-empty">No platform admins.</td></tr>
                    <?php else: ?>
                    <?php foreach ($platform_admins as $admin): ?>
                        <?php
                            $admin_status = strtolower((string)($admin['status'] ?? ''));
                            $is_owner_row = (string)($admin['username'] ?? '') === 'superadmin';
                            $self_id = (int)($logged_in_admin->admin_id ?? 0);
                            $row_id = (int)($admin['admin_id'] ?? 0);
                        ?>
                        <tr class="js-searchable-row"
                            data-group="admin"
                            data-search="<?= esc(strtolower(trim(($admin['username'] ?? '') . ' ' . ($admin['full_name'] ?? '') . ' ' . ($admin['email'] ?? '')))) ?>">
                            <td><?= esc($admin['username']) ?><?= $is_owner_row ? ' <span class="sa-status sa-status--owner">Owner</span>' : '' ?></td>
                            <td><?= esc($admin['full_name']) ?></td>
                            <td><?= esc($admin['email'] ?? '') ?></td>
                            <td><span class="sa-status sa-status--<?= esc($admin_status === 'disabled' ? 'cancelled' : $admin_status) ?>"><?= esc($admin['status']) ?></span></td>
                            <td>
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-title="<?= esc($admin['username'], 'attr') ?>"
                                            data-kind="admin"
                                            data-id="<?= esc((string)$admin['admin_id'], 'attr') ?>"
                                            data-username="<?= esc($admin['username'], 'attr') ?>"
                                            data-name="<?= esc($admin['full_name'], 'attr') ?>"
                                            data-email="<?= esc($admin['email'] ?? '', 'attr') ?>"
                                            data-status="<?= esc($admin['status'], 'attr') ?>">
                                        View
                                    </button>
                                    <?php if (!empty($is_owner) && !$is_owner_row && $row_id !== $self_id): ?>
                                        <?= form_open('super-admin/toggle-admin/' . $row_id, ['class' => 'sa-row-actions__form']) ?>
                                        <?php if ($admin_status === 'active'): ?>
                                            <input type="hidden" name="status" value="disabled">
                                            <button class="sa-btn sa-btn--danger" type="submit">Disable</button>
                                        <?php else: ?>
                                            <input type="hidden" name="status" value="active">
                                            <button class="sa-btn sa-btn--success" type="submit">Enable</button>
                                        <?php endif; ?>
                                        <?= form_close() ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'email'): ?>
        <?php $sending_gmail = trim((string)($mail_delivery['user'] ?? '')); ?>
        <div class="sa-mail">
            <section class="sa-mail-hero <?= $mail_ready ? 'is-on' : 'is-off' ?>">
                <div class="sa-mail-hero__icon" aria-hidden="true">
                    <?php if ($mail_ready): ?>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php else: ?>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4V6z" stroke="currentColor" stroke-width="2"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php endif; ?>
                </div>
                <div class="sa-mail-hero__copy">
                    <span class="sa-mail-hero__badge"><?= $mail_ready ? 'Automatic sending on' : 'Setup required' ?></span>
                    <h2><?= $mail_ready ? 'Owners get verify email on register' : 'Connect Gmail so owners get verify email' ?></h2>
                    <p>
                        <?php if ($mail_ready && $sending_gmail !== ''): ?>
                            Sending from <strong><?= esc($sending_gmail) ?></strong>. Super Admin does not send each verify message.
                        <?php else: ?>
                            This is one-time wiring. Super Admin does not click Verify for the owner.
                        <?php endif; ?>
                    </p>
                </div>
            </section>

            <ol class="sa-mail-steps">
                <li>
                    <span>1</span>
                    <strong>Owner registers</strong>
                    <em>Public website signup</em>
                </li>
                <li>
                    <span>2</span>
                    <strong>Email goes out</strong>
                    <em>Automatically, from Gmail above</em>
                </li>
                <li>
                    <span>3</span>
                    <strong>Owner clicks Verify</strong>
                    <em>Then Super Admin can Approve &amp; send KHQR</em>
                </li>
            </ol>

            <div class="sa-mail-grid">
                <section class="sa-mail-card">
                    <div class="sa-mail-card__head">
                        <h3>Sending account</h3>
                        <p>Use a Google App Password, not the normal Gmail password.</p>
                    </div>
                    <?= form_open('super-admin/save-gmail', ['class' => 'sa-mail-form']) ?>
                    <label class="sa-mail-label" for="gmail_user">Sending Gmail</label>
                    <input class="sa-input" id="gmail_user" name="gmail_user" type="email" value="<?= esc($sending_gmail, 'attr') ?>" placeholder="you@gmail.com" required>
                    <label class="sa-mail-label" for="gmail_app_password">App Password</label>
                    <input class="sa-input" id="gmail_app_password" name="gmail_app_password" type="password" autocomplete="new-password" placeholder="<?= $mail_ready ? 'Saved — paste a new one only to replace' : 'xxxx xxxx xxxx xxxx' ?>" required>
                    <a class="sa-mail-help" href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">Create an App Password</a>
                    <button class="sa-btn sa-btn--success" type="submit"><?= $mail_ready ? 'Update Gmail' : 'Save Gmail' ?></button>
                    <?= form_close() ?>
                </section>

                <section class="sa-mail-card sa-mail-card--soft">
                    <div class="sa-mail-card__head">
                        <h3>Send a test</h3>
                        <p>Optional check. Owners still get mail from the public register form, not from this button.</p>
                    </div>
                    <?= form_open('super-admin/test-gmail', ['class' => 'sa-mail-form']) ?>
                    <label class="sa-mail-label" for="test_email">Send a test to</label>
                    <input class="sa-input" id="test_email" name="test_email" type="email" value="<?= esc($sending_gmail, 'attr') ?>" placeholder="owner@gmail.com" required>
                    <button class="sa-btn sa-btn--ghost" type="submit">Send test email</button>
                    <?= form_close() ?>
                </section>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($active_page === 'requests'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Waiting for your approve</h2>
                <p class="sa-panel__subtitle">Approve &amp; send KHQR. They cannot log in until they pay.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>Signup ID</th>
                        <th>Code</th>
                        <th>Company</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($subscription_requests)): ?>
                        <tr><td colspan="7" class="sa-empty">No pending requests.</td></tr>
                    <?php else: ?>
                    <?php foreach ($subscription_requests as $request): ?>
                        <tr class="js-searchable-row"
                            data-group="request"
                            data-search="<?= esc(strtolower(trim(($request['company_name'] ?? '') . ' ' . ($request['tenant_code'] ?? '') . ' ' . ($request['owner_first_name'] ?? '') . ' ' . ($request['owner_last_name'] ?? '') . ' ' . ($request['owner_email'] ?? '') . ' ' . ($request['plan_name'] ?? '') . ' ' . ($request['payment_reference'] ?? '') . ' ' . ($request['city'] ?? '') . ' ' . ($request['tax_id'] ?? '')))) ?>">
                            <td><?= esc($request['request_id']) ?></td>
                            <td><?= esc($request['tenant_code']) ?></td>
                            <td><?= esc($request['company_name']) ?></td>
                            <td><?= esc(format_person_name($request['owner_first_name'], $request['owner_last_name'])) ?></td>
                            <td><?= esc($request['owner_email']) ?></td>
                            <td><?= esc($request['plan_name'] ?? '') ?></td>
                            <td>
                                <div class="sa-row-actions">
                                    <button type="button"
                                            class="sa-btn sa-btn--ghost js-sa-view-detail"
                                            data-kind="registration"
                                            data-title="<?= esc($request['company_name'], 'attr') ?>"
                                            data-id="<?= esc((string)$request['request_id'], 'attr') ?>"
                                            data-company="<?= esc($request['company_name'], 'attr') ?>"
                                            data-code="<?= esc($request['tenant_code'], 'attr') ?>"
                                            data-type="<?= esc(saas_business_type_label($request['business_type'] ?? ''), 'attr') ?>"
                                            data-address="<?= esc($request['address'] ?? '', 'attr') ?>"
                                            data-city="<?= esc($request['city'] ?? '', 'attr') ?>"
                                            data-country="<?= esc($request['country'] ?? '', 'attr') ?>"
                                            data-tax="<?= esc($request['tax_id'] ?? '', 'attr') ?>"
                                            data-owner="<?= esc(format_person_name($request['owner_first_name'], $request['owner_last_name']), 'attr') ?>"
                                            data-email="<?= esc($request['owner_email'], 'attr') ?>"
                                            data-phone="<?= esc($request['owner_phone'] ?? '', 'attr') ?>"
                                            data-username="<?= esc($request['owner_username'] ?? '', 'attr') ?>"
                                            data-plan="<?= esc($request['plan_name'] ?? '', 'attr') ?>"
                                            data-created="<?= esc($format_request_date($request['created_at'] ?? ''), 'attr') ?>">
                                        View
                                    </button>
                                    <?= form_open('super-admin/approve-request/' . (int)$request['request_id'], [
                                        'class' => 'js-confirm-action-form sa-row-actions__form',
                                        'data-action' => 'approve',
                                        'data-context' => 'registration',
                                    ]) ?>
                                    <button class="sa-btn sa-btn--success" type="submit">Approve &amp; send KHQR</button>
                                    <?= form_close() ?>
                                    <?= form_open('super-admin/reject-request/' . (int)$request['request_id'], [
                                        'class' => 'js-confirm-action-form sa-row-actions__form',
                                        'data-action' => 'reject',
                                        'data-context' => 'registration',
                                    ]) ?>
                                    <input type="hidden" name="reject_comment" class="js-reject-comment-field" value="">
                                    <button class="sa-btn sa-btn--danger" type="submit">Reject</button>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'history'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Registration History</h2>
                <p class="sa-panel__subtitle">Signup ID is from the registration table. It will not match Shop ID.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                    <tr>
                        <th>Signup ID</th>
                        <th>Code</th>
                        <th>Company</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Payment Ref</th>
                        <th>Status</th>
                        <th>Reviewed</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($subscription_request_history)): ?>
                        <tr><td colspan="10" class="sa-empty">No registration history yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($subscription_request_history as $request): ?>
                        <?php
                            $history_status = strtolower((string)($request['status'] ?? ''));
                            $history_paid = trim((string)($request['payment_reference'] ?? '')) !== '';
                            $history_label = $history_status === 'approved'
                                ? ($history_paid ? 'Paid' : 'Approved · waiting $20')
                                : $request['status'];
                            $history_badge = $history_status === 'rejected'
                                ? 'cancelled'
                                : ($history_paid ? 'active' : 'pending');
                            $rejection_reason = saas_rejection_reason($request['notes'] ?? '');
                        ?>
                        <tr class="js-searchable-row"
                            data-group="history"
                            data-status="<?= esc($history_status) ?>"
                            data-search="<?= esc(strtolower(trim(($request['company_name'] ?? '') . ' ' . ($request['tenant_code'] ?? '') . ' ' . ($request['owner_first_name'] ?? '') . ' ' . ($request['owner_last_name'] ?? '') . ' ' . ($request['owner_email'] ?? '') . ' ' . ($request['plan_name'] ?? '') . ' ' . ($request['payment_reference'] ?? '') . ' ' . ($request['status'] ?? '') . ' ' . ($request['city'] ?? '') . ' ' . ($request['tax_id'] ?? '') . ' ' . $rejection_reason))) ?>">
                            <td><?= esc($request['request_id']) ?></td>
                            <td><?= esc($request['tenant_code']) ?></td>
                            <td><?= esc($request['company_name']) ?></td>
                            <td><?= esc(format_person_name($request['owner_first_name'] ?? '', $request['owner_last_name'] ?? '')) ?></td>
                            <td><?= esc($request['owner_email']) ?></td>
                            <td><?= esc($request['plan_name'] ?? '') ?></td>
                            <td><?= $history_paid ? esc($request['payment_reference']) : '—' ?></td>
                            <td>
                                <span class="sa-status sa-status--<?= esc($history_badge) ?>">
                                    <?= esc($history_label) ?>
                                </span>
                            </td>
                            <td><?= esc($format_request_date($request['reviewed_at'] ?? '')) ?></td>
                            <td>
                                <button type="button"
                                        class="sa-btn sa-btn--ghost js-sa-view-detail"
                                        data-kind="registration"
                                        data-title="<?= esc($request['company_name'] ?? '', 'attr') ?>"
                                        data-id="<?= esc((string)$request['request_id'], 'attr') ?>"
                                        data-company="<?= esc($request['company_name'] ?? '', 'attr') ?>"
                                        data-code="<?= esc($request['tenant_code'] ?? '', 'attr') ?>"
                                        data-type="<?= esc(saas_business_type_label($request['business_type'] ?? ''), 'attr') ?>"
                                        data-address="<?= esc($request['address'] ?? '', 'attr') ?>"
                                        data-city="<?= esc($request['city'] ?? '', 'attr') ?>"
                                        data-country="<?= esc($request['country'] ?? '', 'attr') ?>"
                                        data-tax="<?= esc($request['tax_id'] ?? '', 'attr') ?>"
                                        data-owner="<?= esc(format_person_name($request['owner_first_name'] ?? '', $request['owner_last_name'] ?? ''), 'attr') ?>"
                                        data-email="<?= esc($request['owner_email'] ?? '', 'attr') ?>"
                                        data-phone="<?= esc($request['owner_phone'] ?? '', 'attr') ?>"
                                        data-username="<?= esc($request['owner_username'] ?? '', 'attr') ?>"
                                        data-plan="<?= esc($request['plan_name'] ?? '', 'attr') ?>"
                                        data-payment="<?= esc($request['payment_reference'] ?? '', 'attr') ?>"
                                        data-status="<?= esc($request['status'] ?? '', 'attr') ?>"
                                        data-reason="<?= esc($rejection_reason, 'attr') ?>"
                                        data-created="<?= esc($format_request_date($request['created_at'] ?? ''), 'attr') ?>">
                                    View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'features'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Master feature catalog</h2>
                <p class="sa-panel__subtitle">Global on/off for the shared POS template. Use Plans &amp; Sync to decide which paid tier receives each module. Shop sales data stays private.</p>
            </div>
            <div class="sa-panel__body">
                <div class="sa-feature-grid">
                    <?php foreach ($system_features as $feature): ?>
                        <a class="sa-feature-card" href="<?= site_url('super-admin/features/' . $feature['id']) ?>">
                            <img class="sa-feature-card__icon" src="<?= base_url($feature['icon']) ?>" alt="">
                            <div class="sa-feature-card__copy">
                                <strong><?= esc($feature['label']) ?></strong>
                                <span><?= esc($feature['description']) ?></span>
                            </div>
                            <span class="sa-status sa-status--<?= $feature['enabled'] ? 'active' : 'cancelled' ?>">
                                <?= $feature['enabled'] ? 'On for all shops' : 'Off for all shops' ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'plans'): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Push template update</h2>
                <p class="sa-panel__subtitle">Deploys interface/catalog changes to every isolated shop database. Private sales, stock, and customers are never copied or deleted. PHP code is already shared — cashiers see new screens after refresh.</p>
            </div>
            <div class="sa-panel__body">
                <p class="sa-feature-detail__note">Template version <strong>v<?= esc($template_meta['template_version'] ?? '1') ?></strong><?= !empty($template_meta['last_sync_at']) ? ' · last sync ' . esc($template_meta['last_sync_at']) : '' ?>.</p>
                <?= form_open('super-admin/sync-template', ['class' => 'sa-template-actions']) ?>
                    <button class="sa-btn sa-btn--primary" type="submit">Sync / Deploy Update</button>
                    <a class="sa-btn sa-btn--ghost" href="<?= site_url('sales') ?>">Test on Admin POS</a>
                <?= form_close() ?>
                <?php if (is_array($template_sync) && !empty($template_sync['tenants'])): ?>
                    <ul class="sa-sync-report">
                        <?php foreach ($template_sync['tenants'] as $row): ?>
                            <li><?= esc($row['tenant_code'] ?? '') ?> — <?= esc($row['message'] ?? '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title">Feature toggling by plan</h2>
                <p class="sa-panel__subtitle">The $20 WBPOS plan includes every POS module.</p>
            </div>
            <div class="sa-table-wrap">
                <table class="sa-table sa-table--plans">
                    <thead>
                    <tr>
                        <th>Feature</th>
                        <?php foreach ($subscription_plans as $plan): ?>
                            <th><?= esc($plan['plan_name']) ?><br><small>$<?= number_format((float)$plan['price_monthly'], 0) ?>/mo</small></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($system_features as $feature): ?>
                        <tr>
                            <td>
                                <strong><?= esc($feature['label']) ?></strong>
                                <div class="sa-muted"><?= esc($feature['description']) ?></div>
                            </td>
                            <?php foreach ($subscription_plans as $plan): ?>
                                <?php
                                    $plan_id = (int)$plan['plan_id'];
                                    $on = !empty($plan_feature_matrix[$plan_id][$feature['id']]);
                                ?>
                                <td>
                                    <?= form_open('super-admin/plans/' . $plan_id . '/feature') ?>
                                        <input type="hidden" name="feature_id" value="<?= esc($feature['id']) ?>">
                                        <input type="hidden" name="enabled" value="<?= $on ? '0' : '1' ?>">
                                        <button class="sa-btn <?= $on ? 'sa-btn--success' : 'sa-btn--ghost' ?>" type="submit">
                                            <?= $on ? 'On' : 'Off' ?>
                                        </button>
                                    <?= form_close() ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($active_page === 'feature' && $current_feature): ?>
        <section class="sa-panel">
            <div class="sa-panel__head">
                <h2 class="sa-panel__title"><?= esc($current_feature['label']) ?></h2>
                <p class="sa-panel__subtitle"><?= esc($current_feature['description']) ?></p>
            </div>
            <div class="sa-panel__body sa-feature-detail">
                <div class="sa-feature-detail__meta">
                    <img class="sa-feature-card__icon" src="<?= base_url($current_feature['icon']) ?>" alt="">
                    <span class="sa-status sa-status--<?= $current_feature['enabled'] ? 'active' : 'cancelled' ?>">
                        <?= $current_feature['enabled'] ? 'On for all shops' : 'Off for all shops' ?>
                    </span>
                </div>
                <p class="sa-feature-detail__note">Applies to <?= (int) $active_tenants ?> active subscription<?= $active_tenants === 1 ? '' : 's' ?>. This screen is the system feature, not a shop’s private data.</p>
                <?= form_open('super-admin/features/' . $current_feature['id'] . '/toggle', ['class' => 'sa-feature-detail__form']) ?>
                    <input type="hidden" name="enabled" value="<?= $current_feature['enabled'] ? '0' : '1' ?>">
                    <button class="sa-btn <?= $current_feature['enabled'] ? 'sa-btn--danger' : 'sa-btn--success' ?>" type="submit">
                        <?= $current_feature['enabled'] ? 'Turn off for all subscriptions' : 'Turn on for all subscriptions' ?>
                    </button>
                    <a class="sa-btn sa-btn--ghost" href="<?= site_url('super-admin/features') ?>">Back to all features</a>
                <?= form_close() ?>
            </div>
        </section>
        <?php endif; ?>
        </div>
    </main>
</div>

<div id="sa-row-detail" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal sa-modal--detail" role="dialog" aria-modal="true" aria-labelledby="sa-row-detail-title">
        <div class="sa-modal__head sa-modal__head--detail">
            <div class="sa-modal__head-copy">
                <p class="sa-modal__eyebrow" id="sa-row-detail-eyebrow">Request details</p>
                <h3 class="sa-modal__title" id="sa-row-detail-title">Details</h3>
            </div>
            <button type="button" class="sa-modal__close" id="sa-row-detail-close-icon" aria-label="Close">&times;</button>
        </div>
        <div class="sa-modal__body" id="sa-row-detail-body"></div>
        <div class="sa-modal__actions sa-modal__actions--detail">
            <button type="button" class="sa-btn sa-btn--primary" id="sa-row-detail-close">Close</button>
        </div>
    </div>
</div>

<div id="request-action-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal sa-modal--action" role="dialog" aria-modal="true" aria-labelledby="request-action-confirm-title">
        <div class="sa-modal__head sa-modal__head--action">
            <div class="sa-modal__head-row">
                <span class="sa-modal__icon sa-modal__icon--danger" id="request-action-confirm-icon" hidden aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </span>
                <h3 class="sa-modal__title" id="request-action-confirm-title">Confirm action</h3>
            </div>
        </div>
        <div class="sa-modal__body sa-modal__body--action">
            <p class="sa-modal__message" id="request-action-confirm-message">Are you sure?</p>
            <div id="request-action-reject-comment-wrap" class="sa-reject-comment" hidden>
                <label class="sa-reject-comment__label" for="request-action-reject-comment">Rejection reason</label>
                <textarea
                    id="request-action-reject-comment"
                    class="sa-reject-comment__input"
                    rows="4"
                    maxlength="500"
                    placeholder="Tell the owner why this signup was rejected."
                ></textarea>
                <p class="sa-reject-comment__helper">Required. Saved in Registration History when you click Reject.</p>
                <p class="sa-reject-comment__hint" id="request-action-reject-comment-error" hidden>Enter at least 3 characters.</p>
            </div>
        </div>
        <div class="sa-modal__actions sa-modal__actions--action">
            <button type="button" class="sa-btn sa-btn--ghost" id="request-action-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--primary" id="request-action-confirm-continue">Confirm</button>
        </div>
    </div>
</div>

<div id="tenant-status-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="tenant-confirm-title">
        <div class="sa-modal__head" id="tenant-confirm-title">Confirm status update</div>
        <div class="sa-modal__body" id="tenant-confirm-message">Are you sure you want to save this change?</div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--ghost" id="tenant-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--primary" id="tenant-confirm-save">Save</button>
        </div>
    </div>
</div>

<div id="super-admin-logout-confirm" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="logout-confirm-title">
        <div class="sa-modal__head" id="logout-confirm-title">Confirm logout</div>
        <div class="sa-modal__body">Are you sure you want to logout from Super Admin?</div>
        <div class="sa-modal__actions">
            <button type="button" class="sa-btn sa-btn--ghost" id="logout-confirm-cancel">Cancel</button>
            <button type="button" class="sa-btn sa-btn--danger-solid" id="logout-confirm-continue">Logout</button>
        </div>
    </div>
</div>

<div id="sa-change-password" class="sa-modal-overlay" aria-hidden="true">
    <div class="sa-modal sa-modal--pos-password" role="dialog" aria-modal="true" aria-labelledby="sa-change-password-title">
        <div class="sa-pos-password__header">
            <h3 class="sa-pos-password__title" id="sa-change-password-title">Change Password</h3>
            <button type="button" class="sa-pos-password__close" id="sa-change-password-close" aria-label="Close">&times;</button>
        </div>
        <div class="sa-pos-password__body">
            <p class="sa-pos-password__required"><?= lang('Common.fields_required_message') ?></p>
            <p class="sa-change-password__error" id="sa-change-password-error" hidden></p>
            <?= form_open('super-admin/changepassword', ['id' => 'sa_change_password_form', 'class' => 'sa-pos-password-form form-horizontal']) ?>
                <div class="sa-pos-password__row">
                    <label class="sa-pos-password__label required" for="sa_cp_username"><?= lang('Employees.username') ?></label>
                    <div class="sa-pos-password__field">
                        <div class="sa-pos-input-group">
                            <span class="sa-pos-input-group__addon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input class="sa-pos-input-group__input" id="sa_cp_username" name="username" type="text" value="<?= esc($admin_username, 'attr') ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="sa-pos-password__row">
                    <label class="sa-pos-password__label" for="sa_cp_current"><?= lang('Employees.current_password') ?></label>
                    <div class="sa-pos-password__field">
                        <div class="sa-pos-input-group input-group">
                            <span class="sa-pos-input-group__addon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input class="sa-pos-input-group__input form-control" id="sa_cp_current" name="current_password" type="password" autocomplete="current-password" required>
                        </div>
                    </div>
                </div>

                <div class="sa-pos-password__row">
                    <label class="sa-pos-password__label" for="sa_cp_password"><?= lang('Employees.password') ?></label>
                    <div class="sa-pos-password__field">
                        <div class="sa-pos-input-group input-group">
                            <span class="sa-pos-input-group__addon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input class="sa-pos-input-group__input form-control" id="sa_cp_password" name="password" type="password" autocomplete="new-password" required>
                        </div>
                        <p class="sa-pos-password__help"><?= esc(lang('Common.password_strong_hint')) ?></p>
                    </div>
                </div>

                <div class="sa-pos-password__row">
                    <label class="sa-pos-password__label" for="sa_cp_repeat"><?= lang('Employees.repeat_password') ?></label>
                    <div class="sa-pos-password__field">
                        <div class="sa-pos-input-group input-group">
                            <span class="sa-pos-input-group__addon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input class="sa-pos-input-group__input form-control" id="sa_cp_repeat" name="repeat_password" type="password" autocomplete="new-password" required>
                        </div>
                    </div>
                </div>
            <?= form_close() ?>
        </div>
        <div class="sa-pos-password__footer">
            <button type="submit" form="sa_change_password_form" class="sa-pos-password__submit" id="sa-change-password-submit">Submit</button>
        </div>
    </div>
</div>

<div id="sa_notify_backdrop" class="sa-notify-backdrop" hidden aria-hidden="true"></div>
<aside id="sa_notify_panel" class="sa-notify-panel" aria-hidden="true" aria-labelledby="sa_notify_panel_title">
    <div class="sa-notify-panel__head">
        <div class="sa-notify-panel__title-row">
            <div class="sa-notify-panel__title-wrap">
                <h2 id="sa_notify_panel_title">Notifications</h2>
                <span class="sa-notify-panel__count<?= $notify_count > 0 ? '' : ' sa-notify-panel__count--hidden' ?>" id="sa_notify_panel_count"><?= $notify_count ?></span>
            </div>
            <button type="button" class="sa-notify-panel__close" id="sa_notify_close" aria-label="Close notifications">&times;</button>
        </div>
        <div class="sa-notify-panel__actions">
            <button type="button" class="sa-notify-panel__action sa-notify-panel__action--read" id="sa_notify_mark_read">Mark all as read</button>
            <button type="button" class="sa-notify-panel__action sa-notify-panel__action--clear" id="sa_notify_clear">Clear</button>
        </div>
    </div>
    <div class="sa-notify-panel__body">
        <ul class="sa-notify-panel__list" id="sa_notify_list">
            <?php foreach ($notification_items as $item): ?>
                <li class="sa-notify-card" data-notify-key="<?= esc($item['key'], 'attr') ?>">
                    <button type="button" class="sa-notify-card__dismiss" aria-label="Dismiss notification">&times;</button>
                    <div class="sa-notify-card__row">
                        <span class="sa-notify-card__icon sa-notify-card__icon--<?= esc($item['type']) ?>" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path></svg>
                        </span>
                        <div class="sa-notify-card__head">
                            <strong><?= esc($item['title']) ?></strong>
                            <?php if ($item['relative_time'] !== ''): ?>
                                <time datetime="<?= esc($item['created_at'], 'attr') ?>"><?= esc($item['relative_time']) ?></time>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="sa-notify-card__subtitle"><?= esc($item['subtitle']) ?></p>
                    <p class="sa-notify-card__body"><?= esc($item['body']) ?></p>
                    <?php if ($item['meta'] !== ''): ?>
                        <p class="sa-notify-card__meta"><?= esc($item['meta']) ?></p>
                    <?php endif; ?>
                    <a class="sa-notify-card__link" href="<?= esc($item['review_url'], 'attr') ?>">View details</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="sa-notify-panel__empty<?= empty($notification_items) ? '' : ' sa-notify-panel__empty--hidden' ?>" id="sa_notify_empty">No alerts right now.</p>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarKey = 'ospos_sidebar_collapsed';
        const sidebarToggle = document.getElementById('sa_sidebar_toggle');
        const mobileToggle = document.getElementById('sa_mobile_sidebar_toggle');
        const mobileBackdrop = document.getElementById('sa_sidebar_backdrop');

        const syncForViewport = function() {
            if (window.innerWidth <= 992) {
                document.documentElement.classList.remove('sidebar-collapsed');
            } else if (localStorage.getItem(sidebarKey) === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        };

        const setExpandedState = function() {
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', (!document.documentElement.classList.contains('sidebar-collapsed')).toString());
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    return;
                }

                document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem(sidebarKey, document.documentElement.classList.contains('sidebar-collapsed') ? '1' : '0');
                setExpandedState();
            });
        }

        const closeMobileSidebar = function() {
            document.documentElement.classList.remove('mobile-sidebar-open');
            if (mobileToggle) {
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        };

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                if (window.innerWidth > 992) {
                    return;
                }

                const isOpen = document.documentElement.classList.toggle('mobile-sidebar-open');
                mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeMobileSidebar);
        }

        document.querySelectorAll('.neo-global-menu-item, .neo-sidebar-logout').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', function() {
            syncForViewport();
            setExpandedState();
            if (window.innerWidth > 992) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && window.innerWidth <= 992 && document.documentElement.classList.contains('mobile-sidebar-open')) {
                closeMobileSidebar();
            }
        });

        syncForViewport();
        setExpandedState();

        const overlay = document.getElementById('tenant-status-confirm');
        const messageEl = document.getElementById('tenant-confirm-message');
        const cancelBtn = document.getElementById('tenant-confirm-cancel');
        const saveBtn = document.getElementById('tenant-confirm-save');
        const logoutOverlay = document.getElementById('super-admin-logout-confirm');
        const logoutCancelBtn = document.getElementById('logout-confirm-cancel');
        const logoutContinueBtn = document.getElementById('logout-confirm-continue');
        const actionOverlay = document.getElementById('request-action-confirm');
        const actionTitleEl = document.getElementById('request-action-confirm-title');
        const actionMessageEl = document.getElementById('request-action-confirm-message');
        const actionCancelBtn = document.getElementById('request-action-confirm-cancel');
        const actionContinueBtn = document.getElementById('request-action-confirm-continue');
        const actionConfirmIcon = document.getElementById('request-action-confirm-icon');
        const rejectCommentWrap = document.getElementById('request-action-reject-comment-wrap');
        const rejectCommentInput = document.getElementById('request-action-reject-comment');
        const rejectCommentError = document.getElementById('request-action-reject-comment-error');
        const searchInput = document.getElementById('sa_navbar_search');
        const statusFilter = document.getElementById('super_admin_status_filter');
        const notifyBtn = document.getElementById('sa_notify_btn');
        const notifyPanel = document.getElementById('sa_notify_panel');
        const notifyBackdrop = document.getElementById('sa_notify_backdrop');
        const notifyCloseBtn = document.getElementById('sa_notify_close');
        const notifyMarkReadBtn = document.getElementById('sa_notify_mark_read');
        const notifyClearBtn = document.getElementById('sa_notify_clear');
        const notifyList = document.getElementById('sa_notify_list');
        const notifyEmpty = document.getElementById('sa_notify_empty');
        const profileBtn = document.getElementById('sa_profile_btn');
        const profileDropdown = document.getElementById('sa_profile_dropdown');
        const dismissedStorageKey = 'sa_dismissed_notifications';
        let pendingForm = null;
        let pendingLogoutHref = null;
        let pendingActionForm = null;
        let openDropdown = null;

        const getDismissedKeys = function() {
            try {
                const parsed = JSON.parse(sessionStorage.getItem(dismissedStorageKey) || '[]');
                return Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                return [];
            }
        };

        const setDismissedKeys = function(keys) {
            sessionStorage.setItem(dismissedStorageKey, JSON.stringify(keys));
        };

        const getVisibleNotifyCards = function() {
            if (!notifyList) {
                return [];
            }

            return Array.from(notifyList.querySelectorAll('.sa-notify-card')).filter(function(card) {
                return card.style.display !== 'none';
            });
        };

        const syncNotifyEmptyState = function() {
            if (!notifyEmpty || !notifyList) {
                return;
            }

            const visibleCount = getVisibleNotifyCards().length;
            notifyEmpty.classList.toggle('sa-notify-panel__empty--hidden', visibleCount > 0);
            notifyList.hidden = visibleCount === 0;
        };

        const syncNotifyCountsFromVisible = function() {
            if (sessionStorage.getItem('sa_notifications_marked_read') === '1') {
                updateNotifyCounts(0);
                return;
            }

            updateNotifyCounts(getVisibleNotifyCards().length);
        };

        const updatePendingStatCount = function(total) {
            const statValue = document.getElementById('sa_pending_stat_value');
            const count = Math.max(0, parseInt(total, 10) || 0);

            if (statValue) {
                statValue.textContent = String(count);
            }
        };

        const updateNotifyCounts = function(total) {
            const count = Math.max(0, parseInt(total, 10) || 0);
            const navbarBadge = document.getElementById('sa_navbar_badge');
            const panelCount = document.getElementById('sa_notify_panel_count');

            if (navbarBadge) {
                navbarBadge.textContent = String(count);
                navbarBadge.classList.toggle('sa-notify-badge--hidden', count <= 0);
            }

            if (panelCount) {
                panelCount.textContent = String(count);
                panelCount.classList.toggle('sa-notify-panel__count--hidden', count <= 0);
            }
        };

        const pruneDismissedNotifications = function() {
            if (!notifyList) {
                return;
            }

            const validKeys = Array.from(notifyList.querySelectorAll('.sa-notify-card'))
                .map(function(card) {
                    return card.getAttribute('data-notify-key') || '';
                })
                .filter(function(key) {
                    return key !== '';
                });

            const dismissed = getDismissedKeys().filter(function(key) {
                return validKeys.indexOf(key) !== -1;
            });

            setDismissedKeys(dismissed);
        };

        const applyDismissedNotifications = function() {
            if (!notifyList) {
                return;
            }

            pruneDismissedNotifications();

            const dismissed = getDismissedKeys();
            notifyList.querySelectorAll('.sa-notify-card').forEach(function(card) {
                const key = card.getAttribute('data-notify-key') || '';
                if (dismissed.indexOf(key) !== -1) {
                    card.style.display = 'none';
                }
            });

            syncNotifyEmptyState();
            syncNotifyCountsFromVisible();
        };

        const closeNotifyPanel = function() {
            if (!notifyPanel || !notifyBackdrop) {
                return;
            }

            notifyPanel.classList.remove('is-open');
            notifyPanel.setAttribute('aria-hidden', 'true');
            notifyBackdrop.hidden = true;
            notifyBackdrop.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('sa-notify-open');

            if (notifyBtn) {
                notifyBtn.setAttribute('aria-expanded', 'false');
            }
        };

        const openNotifyPanel = function() {
            if (!notifyPanel || !notifyBackdrop) {
                return;
            }

            closeDropdowns();
            applyDismissedNotifications();
            notifyPanel.classList.add('is-open');
            notifyPanel.setAttribute('aria-hidden', 'false');
            notifyBackdrop.hidden = false;
            notifyBackdrop.setAttribute('aria-hidden', 'false');
            document.body.classList.add('sa-notify-open');

            if (notifyBtn) {
                notifyBtn.setAttribute('aria-expanded', 'true');
            }
        };

        const closeDropdowns = function() {
            if (profileDropdown) {
                profileDropdown.hidden = true;
            }

            if (profileBtn) {
                profileBtn.setAttribute('aria-expanded', 'false');
            }

            openDropdown = null;
        };

        const toggleDropdown = function(button, dropdown) {
            if (!button || !dropdown) {
                return;
            }

            const willOpen = dropdown.hidden;
            closeDropdowns();

            if (willOpen) {
                dropdown.hidden = false;
                button.setAttribute('aria-expanded', 'true');
                openDropdown = dropdown;
            }
        };

        if (notifyBtn && notifyPanel) {
            notifyBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                if (notifyPanel.classList.contains('is-open')) {
                    closeNotifyPanel();
                } else {
                    openNotifyPanel();
                }
            });
        }

        if (notifyCloseBtn) {
            notifyCloseBtn.addEventListener('click', closeNotifyPanel);
        }

        if (notifyBackdrop) {
            notifyBackdrop.addEventListener('click', closeNotifyPanel);
        }

        if (notifyMarkReadBtn) {
            notifyMarkReadBtn.addEventListener('click', function() {
                if (notifyList) {
                    notifyList.querySelectorAll('.sa-notify-card').forEach(function(card) {
                        if (card.style.display !== 'none') {
                            card.classList.add('is-read');
                        }
                    });
                }

                sessionStorage.setItem('sa_notifications_marked_read', '1');
                updateNotifyCounts(0);
            });
        }

        if (notifyClearBtn && notifyList) {
            notifyClearBtn.addEventListener('click', function() {
                const dismissed = getDismissedKeys();

                notifyList.querySelectorAll('.sa-notify-card').forEach(function(card) {
                    const key = card.getAttribute('data-notify-key') || '';
                    card.style.display = 'none';
                    if (key !== '' && dismissed.indexOf(key) === -1) {
                        dismissed.push(key);
                    }
                });

                setDismissedKeys(dismissed);
                sessionStorage.removeItem('sa_notifications_marked_read');
                syncNotifyCountsFromVisible();
                syncNotifyEmptyState();
            });
        }

        if (notifyList) {
            notifyList.addEventListener('click', function(event) {
                const dismissBtn = event.target.closest('.sa-notify-card__dismiss');
                if (!dismissBtn) {
                    return;
                }

                const card = dismissBtn.closest('.sa-notify-card');
                if (!card) {
                    return;
                }

                const key = card.getAttribute('data-notify-key') || '';
                const dismissed = getDismissedKeys();
                if (key !== '' && dismissed.indexOf(key) === -1) {
                    dismissed.push(key);
                    setDismissedKeys(dismissed);
                }

                card.style.display = 'none';
                sessionStorage.removeItem('sa_notifications_marked_read');
                syncNotifyCountsFromVisible();
                syncNotifyEmptyState();
            });
        }

        applyDismissedNotifications();

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                closeNotifyPanel();
                toggleDropdown(profileBtn, profileDropdown);
            });
        }

        const changePasswordOverlay = document.getElementById('sa-change-password');
        const changePasswordBtn = document.getElementById('sa_change_password_btn');
        const changePasswordForm = document.getElementById('sa_change_password_form');
        const changePasswordError = document.getElementById('sa-change-password-error');
        const changePasswordClose = document.getElementById('sa-change-password-close');
        const changePasswordSubmit = document.getElementById('sa-change-password-submit');

        const openChangePasswordModal = function() {
            if (!changePasswordOverlay) {
                return;
            }
            closeDropdowns();
            if (changePasswordError) {
                changePasswordError.hidden = true;
                changePasswordError.textContent = '';
            }
            if (changePasswordForm) {
                changePasswordForm.reset();
                const usernameField = document.getElementById('sa_cp_username');
                if (usernameField) {
                    usernameField.value = <?= json_encode($admin_username) ?>;
                }
            }
            changePasswordOverlay.classList.add('is-open');
            changePasswordOverlay.setAttribute('aria-hidden', 'false');
            const currentField = document.getElementById('sa_cp_current');
            if (currentField) {
                currentField.focus();
            }
        };

        const closeChangePasswordModal = function() {
            if (!changePasswordOverlay) {
                return;
            }
            changePasswordOverlay.classList.remove('is-open');
            changePasswordOverlay.setAttribute('aria-hidden', 'true');
        };

        if (changePasswordBtn) {
            changePasswordBtn.addEventListener('click', function(event) {
                event.preventDefault();
                openChangePasswordModal();
            });
        }

        if (changePasswordClose) {
            changePasswordClose.addEventListener('click', closeChangePasswordModal);
        }
        if (changePasswordOverlay) {
            changePasswordOverlay.addEventListener('click', function(event) {
                if (event.target === changePasswordOverlay) {
                    closeChangePasswordModal();
                }
            });
        }

        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(event) {
                event.preventDefault();
                if (changePasswordError) {
                    changePasswordError.hidden = true;
                    changePasswordError.textContent = '';
                }

                const currentPassword = (document.getElementById('sa_cp_current') || {}).value || '';
                const password = (document.getElementById('sa_cp_password') || {}).value || '';
                const repeatPassword = (document.getElementById('sa_cp_repeat') || {}).value || '';

                const showError = function(message) {
                    if (!changePasswordError) {
                        return;
                    }
                    changePasswordError.textContent = message;
                    changePasswordError.hidden = false;
                };

                if (currentPassword.length < 8) {
                    showError('Current password is required (at least 8 characters).');
                    return;
                }
                if (password.length < 8) {
                    showError(<?= json_encode(lang('Employees.password_minlength')) ?>);
                    return;
                }
                if (password !== repeatPassword) {
                    showError(<?= json_encode(lang('Employees.password_must_match')) ?>);
                    return;
                }
                if (password === currentPassword) {
                    showError(<?= json_encode(lang('Employees.password_not_must_match')) ?>);
                    return;
                }

                if (changePasswordSubmit) {
                    changePasswordSubmit.disabled = true;
                }

                fetch(changePasswordForm.action, {
                    method: 'POST',
                    body: new FormData(changePasswordForm),
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data && data.success) {
                            closeChangePasswordModal();
                            window.location.href = <?= json_encode(site_url('super-admin/overview') . '?password_changed=1') ?>;
                            return;
                        }
                        showError((data && data.message) ? data.message : 'Password change failed.');
                    })
                    .catch(function() {
                        showError('Password change failed. Please try again.');
                    })
                    .finally(function() {
                        if (changePasswordSubmit) {
                            changePasswordSubmit.disabled = false;
                        }
                    });
            });
        }

        document.addEventListener('click', function(event) {
            if (!openDropdown) {
                return;
            }

            if (event.target.closest('.sa-dropdown-wrap')) {
                return;
            }

            closeDropdowns();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && changePasswordOverlay && changePasswordOverlay.classList.contains('is-open')) {
                closeChangePasswordModal();
                return;
            }

            if (event.key === 'Escape' && notifyPanel && notifyPanel.classList.contains('is-open')) {
                closeNotifyPanel();
                return;
            }

            if (event.key === 'Escape' && openDropdown) {
                closeDropdowns();
            }
        });

        const closeModal = function() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            pendingForm = null;
        };

        const openModal = function(form) {
            const statusInput = form.querySelector('select[name="status"]');
            const tenantCodeInput = form.querySelector('input[name="tenant_code"]');
            const nextStatus = statusInput ? statusInput.value : '';
            const tenantCode = tenantCodeInput ? tenantCodeInput.value : '';
            messageEl.textContent = tenantCode
                ? 'Save status "' + nextStatus + '" for tenant "' + tenantCode + '"?'
                : 'Save this status change?';
            pendingForm = form;
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
        };

        const closeLogoutModal = function() {
            logoutOverlay.classList.remove('is-open');
            logoutOverlay.setAttribute('aria-hidden', 'true');
            pendingLogoutHref = null;
        };

        const openLogoutModal = function(href) {
            pendingLogoutHref = href;
            logoutOverlay.classList.add('is-open');
            logoutOverlay.setAttribute('aria-hidden', 'false');
        };

        const closeActionModal = function() {
            if (!actionOverlay) {
                return;
            }

            actionOverlay.classList.remove('is-open');
            actionOverlay.setAttribute('aria-hidden', 'true');
            pendingActionForm = null;
            if (actionOverlay) {
                actionOverlay.classList.remove('is-reject');
            }
            if (actionConfirmIcon) {
                actionConfirmIcon.hidden = true;
            }
            if (rejectCommentWrap) {
                rejectCommentWrap.hidden = true;
            }
            if (rejectCommentInput) {
                rejectCommentInput.value = '';
            }
            if (rejectCommentError) {
                rejectCommentError.hidden = true;
            }
        };

        const openActionModal = function(form) {
            if (!actionOverlay || !actionMessageEl || !actionTitleEl || !actionContinueBtn) {
                return;
            }

            const action = form.dataset.action || 'approve';
            const context = form.dataset.context || 'registration';
            const isApprove = action === 'approve';
            const isResendVerify = action === 'resend-verify';

            const isReject = action === 'reject';

            if (actionOverlay) {
                actionOverlay.classList.toggle('is-reject', isReject);
            }
            if (actionConfirmIcon) {
                actionConfirmIcon.hidden = !isReject;
            }
            if (rejectCommentWrap) {
                rejectCommentWrap.hidden = !isReject;
            }
            if (rejectCommentInput) {
                rejectCommentInput.value = '';
            }
            if (rejectCommentError) {
                rejectCommentError.hidden = true;
            }

            if (isResendVerify) {
                actionTitleEl.textContent = 'Send verify email';
                actionMessageEl.textContent = 'Send the verification link to the owner’s Gmail. Super Admin does not verify for them — they must click the link.';
                actionContinueBtn.textContent = 'Send verify email';
                actionContinueBtn.className = 'sa-btn sa-btn--success';
            } else {
                actionTitleEl.textContent = isApprove ? 'Confirm approve' : 'Confirm rejection';
                actionMessageEl.textContent = isApprove
                    ? 'Approve this shop and send the KHQR to the owner’s email? They cannot log in until they pay.'
                    : 'Add a short reason below. It is saved in registration history.';
                actionContinueBtn.textContent = isApprove ? 'Approve & send KHQR' : 'Reject';
                actionContinueBtn.className = isApprove
                    ? 'sa-btn sa-btn--success'
                    : 'sa-btn sa-btn--danger-solid';
            }

            pendingActionForm = form;
            actionOverlay.classList.add('is-open');
            actionOverlay.setAttribute('aria-hidden', 'false');
            if (isReject && rejectCommentInput) {
                rejectCommentInput.focus();
            }
        };

        document.querySelectorAll('.js-confirm-action-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openActionModal(form);
            });
        });

        document.querySelectorAll('.js-tenant-status-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                openModal(form);
            });
        });

        const applyRowFilters = function() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const status = statusFilter ? statusFilter.value.toLowerCase() : '';

            document.querySelectorAll('.js-searchable-row').forEach(function(row) {
                const haystack = (row.dataset.search || '').toLowerCase();
                const rowGroup = row.dataset.group || '';
                const rowStatus = (row.dataset.status || '').toLowerCase();
                const rowBilling = (row.dataset.billing || '').toLowerCase();

                const queryMatch = query === '' || haystack.indexOf(query) !== -1;
                const usesStatusFilter = rowGroup === 'tenant' || rowGroup === 'history';
                let statusMatch = true;
                if (usesStatusFilter && status !== '') {
                    if (status === 'expired') {
                        statusMatch = rowBilling === 'expired';
                    } else if (status === 'expiring_soon') {
                        statusMatch = rowBilling === 'warning';
                    } else {
                        statusMatch = rowStatus === status;
                    }
                }
                row.style.display = queryMatch && statusMatch ? '' : 'none';
            });
        };

        if (searchInput) {
            searchInput.addEventListener('input', applyRowFilters);
        }
        if (statusFilter) {
            statusFilter.addEventListener('change', applyRowFilters);
        }

        const applyInitialFiltersFromUrl = function() {
            if (!statusFilter || statusFilter.hidden) {
                return;
            }

            const params = new URLSearchParams(window.location.search);
            const status = (params.get('status') || '').toLowerCase();
            const allowed = ['active', 'suspended', 'cancelled', 'awaiting_payment', 'approved', 'rejected', 'expired', 'expiring_soon'];

            if (allowed.indexOf(status) !== -1) {
                statusFilter.value = status;
            }
        };

        applyInitialFiltersFromUrl();
        applyRowFilters();

        document.querySelectorAll('.js-super-admin-logout').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                openLogoutModal(link.getAttribute('href'));
            });
        });

        if (logoutContinueBtn) {
            logoutContinueBtn.addEventListener('click', function() {
                if (!pendingLogoutHref) {
                    closeLogoutModal();
                    return;
                }
                window.location.href = pendingLogoutHref;
            });
        }

        if (logoutCancelBtn) {
            logoutCancelBtn.addEventListener('click', closeLogoutModal);
        }

        if (logoutOverlay) {
            logoutOverlay.addEventListener('click', function(event) {
                if (event.target === logoutOverlay) {
                    closeLogoutModal();
                }
            });
        }

        if (actionContinueBtn) {
            actionContinueBtn.addEventListener('click', function() {
                if (!pendingActionForm) {
                    closeActionModal();
                    return;
                }

                const isReject = pendingActionForm.dataset.action === 'reject';
                if (isReject && rejectCommentInput) {
                    const comment = rejectCommentInput.value.trim();
                    if (comment.length < 3) {
                        if (rejectCommentError) {
                            rejectCommentError.hidden = false;
                        }
                        rejectCommentInput.focus();
                        return;
                    }

                    const hiddenField = pendingActionForm.querySelector('.js-reject-comment-field');
                    if (hiddenField) {
                        hiddenField.value = comment;
                    }
                }

                const formToSubmit = pendingActionForm;
                closeActionModal();
                formToSubmit.submit();
            });
        }

        if (actionCancelBtn) {
            actionCancelBtn.addEventListener('click', closeActionModal);
        }

        if (actionOverlay) {
            actionOverlay.addEventListener('click', function(event) {
                if (event.target === actionOverlay) {
                    closeActionModal();
                }
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (!pendingForm) {
                    closeModal();
                    return;
                }

                const formToSubmit = pendingForm;
                closeModal();
                formToSubmit.submit();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        if (overlay) {
            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    closeModal();
                }
            });
        }

        const detailOverlay = document.getElementById('sa-row-detail');
        const detailTitleEl = document.getElementById('sa-row-detail-title');
        const detailEyebrowEl = document.getElementById('sa-row-detail-eyebrow');
        const detailBodyEl = document.getElementById('sa-row-detail-body');
        const detailCloseBtn = document.getElementById('sa-row-detail-close');
        const detailCloseIconBtn = document.getElementById('sa-row-detail-close-icon');
        const detailFieldOrder = ['id', 'company', 'code', 'type', 'address', 'city', 'country', 'tax', 'owner', 'name', 'email', 'phone', 'username', 'plan', 'payment', 'expires', 'tenant', 'status', 'reason', 'created'];

        const escapeDetailHtml = function(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        };

        const closeDetailModal = function() {
            if (!detailOverlay) {
                return;
            }

            detailOverlay.classList.remove('is-open');
            detailOverlay.setAttribute('aria-hidden', 'true');
        };

        const openDetailModal = function(button) {
            if (!detailOverlay || !detailTitleEl || !detailBodyEl) {
                return;
            }

            const lines = [];
            const kind = button.dataset.kind || '';
            const idLabels = {
                business: 'Shop ID',
                registration: 'Signup ID',
                admin: 'Admin ID'
            };
            const map = {
                id: idLabels[kind] || 'ID',
                company: 'Company',
                code: 'Company code',
                type: 'Business type',
                address: 'Store address',
                city: 'City / Province',
                country: 'Country',
                tax: 'Tax ID / VAT TIN',
                owner: 'Owner',
                name: 'Full name',
                email: 'Email',
                phone: 'Phone',
                username: 'Username',
                plan: 'Plan',
                payment: 'Payment reference',
                expires: 'Expires',
                tenant: 'Shop ID',
                status: 'Status',
                reason: 'Rejection reason',
                created: 'Requested'
            };

            const rowStatus = (button.dataset.status || '').toLowerCase();

            detailFieldOrder.forEach(function(key) {
                let value = button.dataset[key];
                if (key === 'created' && value) {
                    value = value.split(/[ T]/)[0];
                }
                if (key === 'tenant') {
                    return;
                }
                if (key === 'reason' && rowStatus !== 'rejected') {
                    return;
                }
                if (value && map[key]) {
                    if (key === 'reason') {
                        lines.push(
                            '<div class="sa-detail-row sa-detail-row--reason">' +
                                '<span class="sa-detail-row__label">' + map[key] + '</span>' +
                                '<div class="sa-detail-row__value sa-reject-note">' + escapeDetailHtml(value) + '</div>' +
                            '</div>'
                        );
                        return;
                    }
                    lines.push(
                        '<div class="sa-detail-row">' +
                            '<span class="sa-detail-row__label">' + map[key] + '</span>' +
                            '<span class="sa-detail-row__value">' + value + '</span>' +
                        '</div>'
                    );
                }
            });

            const title = button.dataset.title || 'Details';
            detailTitleEl.textContent = title;
            if (detailEyebrowEl) {
                if (kind === 'business') {
                    detailEyebrowEl.textContent = 'Business profile';
                } else if (kind === 'password' || kind === 'admin') {
                    detailEyebrowEl.textContent = kind === 'admin' ? 'Platform admin' : 'Password reset';
                } else {
                    detailEyebrowEl.textContent = 'Registration request';
                }
            }
            detailBodyEl.innerHTML = '<div class="sa-detail-sheet">' + lines.join('') + '</div>';
            detailOverlay.classList.add('is-open');
            detailOverlay.setAttribute('aria-hidden', 'false');
        };

        document.querySelectorAll('.js-sa-view-detail').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                openDetailModal(button);
            });
        });

        if (detailCloseBtn) {
            detailCloseBtn.addEventListener('click', closeDetailModal);
        }

        if (detailCloseIconBtn) {
            detailCloseIconBtn.addEventListener('click', closeDetailModal);
        }

        if (detailOverlay) {
            detailOverlay.addEventListener('click', function(event) {
                if (event.target === detailOverlay) {
                    closeDetailModal();
                }
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
                closeModal();
            }
            if (logoutOverlay && event.key === 'Escape' && logoutOverlay.classList.contains('is-open')) {
                closeLogoutModal();
            }
            if (actionOverlay && event.key === 'Escape' && actionOverlay.classList.contains('is-open')) {
                closeActionModal();
            }
            if (detailOverlay && event.key === 'Escape' && detailOverlay.classList.contains('is-open')) {
                closeDetailModal();
            }
        });

        document.querySelectorAll('.js-copy-pay-url').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = button.getAttribute('data-target');
                const value = targetId && document.getElementById(targetId)
                    ? document.getElementById(targetId).value
                    : (button.getAttribute('data-url') || '');
                if (!value) {
                    return;
                }
                const done = function() {
                    const original = button.textContent;
                    button.textContent = 'Copied';
                    window.setTimeout(function() {
                        button.textContent = original;
                    }, 1600);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(value).then(done).catch(function() {
                        window.prompt('Copy this payment link', value);
                    });
                    return;
                }
                window.prompt('Copy this payment link', value);
            });
        });

        (function initRegistrationAlerts() {
            const pollUrl = <?= json_encode(site_url('super-admin/notifications/poll')) ?>;
            const requestsUrl = <?= json_encode(site_url('super-admin/requests')) ?>;
            const businessesUrl = <?= json_encode(site_url('super-admin/businesses')) ?>;
            const latestRegistrationId = <?= (int)($latest_registration_request_id ?? 0) ?>;
            const latestAlertIdBoot = <?= (int)(!empty($platform_alerts[0]['alert_id']) ? $platform_alerts[0]['alert_id'] : 0) ?>;
            const activePage = <?= json_encode($active_page) ?>;
            const storageKey = 'sa_last_registration_request_id';
            const alertStorageKey = 'sa_last_platform_alert_id';
            const toastStack = document.getElementById('sa_toast_stack');
            const pendingBadge = document.getElementById('sa_navbar_badge');
            const pendingStatValue = document.getElementById('sa_pending_stat_value');
            const pollIntervalMs = 30000;

            let lastSeenRegistrationId = parseInt(localStorage.getItem(storageKey) || '0', 10);
            let lastSeenAlertId = parseInt(localStorage.getItem(alertStorageKey) || '0', 10);

            if (activePage === 'requests' || lastSeenRegistrationId === 0) {
                lastSeenRegistrationId = latestRegistrationId;
                localStorage.setItem(storageKey, String(lastSeenRegistrationId));
            }
            if (lastSeenAlertId === 0 && latestAlertIdBoot > 0) {
                lastSeenAlertId = latestAlertIdBoot;
                localStorage.setItem(alertStorageKey, String(lastSeenAlertId));
            }

            const escapeHtml = function(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            };

            const updatePendingBadge = function(total) {
                updatePendingStatCount(total);
                syncNotifyCountsFromVisible();
            };

            const showToast = function(options) {
                if (!toastStack) {
                    return;
                }

                const toast = document.createElement('div');
                toast.className = 'sa-toast ' + (options.toastClass || 'sa-toast--registration');
                toast.innerHTML =
                    '<div class="sa-toast__body">' +
                        '<strong>' + escapeHtml(options.title || 'Alert') + '</strong>' +
                        '<p>' + escapeHtml(options.line1 || '') + '</p>' +
                        (options.line2 ? '<p class="sa-toast__meta">' + escapeHtml(options.line2) + '</p>' : '') +
                    '</div>' +
                    '<a class="sa-toast__action" href="' + escapeHtml(options.href || businessesUrl) + '">' + escapeHtml(options.actionLabel || 'View') + '</a>' +
                    '<button type="button" class="sa-toast__close" aria-label="Dismiss">&times;</button>';

                toast.querySelector('.sa-toast__close').addEventListener('click', function() {
                    toast.remove();
                });

                toastStack.appendChild(toast);

                window.setTimeout(function() {
                    toast.classList.add('is-leaving');
                    window.setTimeout(function() {
                        toast.remove();
                    }, 300);
                }, 12000);
            };

            const showRegistrationToast = function(registration) {
                showToast({
                    toastClass: 'sa-toast--registration',
                    title: 'New company registration',
                    line1: (registration.company_name || '') + ' (' + (registration.tenant_code || '') + ')',
                    line2: (registration.owner_username || '') + ' · ' + (registration.owner_email || ''),
                    href: registration.review_url || requestsUrl,
                    actionLabel: 'Review'
                });
            };

            const showPaymentToast = function(payment) {
                showToast({
                    toastClass: 'sa-toast--payment',
                    title: payment.title || 'Shop paid',
                    line1: (payment.company_name || '') + (payment.tenant_code ? ' (' + payment.tenant_code + ')' : ''),
                    line2: payment.body || '',
                    href: payment.review_url || businessesUrl,
                    actionLabel: 'View'
                });
            };

            const notifyBrowser = function(registration) {
                if (!('Notification' in window) || Notification.permission !== 'granted') {
                    return;
                }

                const notification = new Notification('New company registration', {
                    body: registration.company_name + ' (' + registration.tenant_code + ')',
                    tag: 'wbpos-registration-' + registration.request_id
                });

                notification.onclick = function() {
                    window.focus();
                    window.location.href = registration.review_url || requestsUrl;
                };
            };

            const pollNotifications = function() {
                fetch(
                    pollUrl
                        + '?since=' + encodeURIComponent(String(lastSeenRegistrationId))
                        + '&since_alert=' + encodeURIComponent(String(lastSeenAlertId)),
                    {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    }
                )
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('poll failed');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        if (!data || typeof data !== 'object') {
                            return;
                        }

                        updatePendingBadge(parseInt(data.pending_total, 10) || 0);

                        const newRegistrations = Array.isArray(data.new_registrations) ? data.new_registrations : [];
                        const newPayments = Array.isArray(data.new_payments) ? data.new_payments : [];
                        if (newRegistrations.length > 0 || newPayments.length > 0) {
                            sessionStorage.removeItem('sa_notifications_marked_read');
                        }

                        newRegistrations.forEach(function(registration) {
                            showRegistrationToast(registration);
                            notifyBrowser(registration);
                        });

                        newPayments.forEach(function(payment) {
                            showPaymentToast(payment);
                        });

                        if (newRegistrations.length > 0) {
                            const latestId = parseInt(data.latest_registration_request_id, 10) || lastSeenRegistrationId;
                            lastSeenRegistrationId = latestId;
                            localStorage.setItem(storageKey, String(lastSeenRegistrationId));
                        }

                        const latestAlert = parseInt(data.latest_alert_id, 10) || 0;
                        if (latestAlert > lastSeenAlertId) {
                            lastSeenAlertId = latestAlert;
                            localStorage.setItem(alertStorageKey, String(lastSeenAlertId));
                            // Refresh page list so paid cards appear without full reload of other state.
                            if (newPayments.length > 0 && notifyList) {
                                window.setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            }
                        }
                    })
                    .catch(function() {
                        /* ignore transient network errors */
                    });
            };

            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().catch(function() {});
            }

            window.setInterval(pollNotifications, pollIntervalMs);
            window.setTimeout(pollNotifications, 5000);
        })();
    });
</script>
<script src="<?= base_url('js/password_toggle.js?v=2') ?>"></script>
</body>
</html>
