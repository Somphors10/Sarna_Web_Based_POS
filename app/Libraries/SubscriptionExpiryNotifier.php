<?php

namespace App\Libraries;

use Throwable;

/**
 * Sends Gmail expiry reminders (7 / 3 / 1 days + expired) without spamming.
 */
class SubscriptionExpiryNotifier
{
    /** @var array<string, int> */
    private const STAGE_RANK = [
        '7' => 1,
        '3' => 2,
        '1' => 3,
        'expired' => 4,
    ];

    /**
     * @return array{sent:int, skipped:int, errors:int}
     */
    public function notifyAll(): array
    {
        $stats = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        try {
            (new PlatformArchitecture())->ensure();
            $db = db_connect('platform');
            if (!$db->tableExists('tenants') || !$db->tableExists('subscriptions')) {
                return $stats;
            }

            $tenants = $db->table('tenants')
                ->select('tenant_id')
                ->where('tenant_code !=', 'platform')
                ->whereIn('status', ['active', 'awaiting_payment'])
                ->get()
                ->getResultArray();

            foreach ($tenants as $row) {
                $result = $this->notifyTenant((int)$row['tenant_id']);
                $stats[$result]++;
            }
        } catch (Throwable $e) {
            log_message('error', 'Expiry notifyAll failed: ' . $e->getMessage());
            $stats['errors']++;
        }

        return $stats;
    }

    /**
     * @return 'sent'|'skipped'|'errors'
     */
    public function notifyTenant(int $tenant_id): string
    {
        if ($tenant_id <= 0 || !function_exists('saas_tenant_subscription_info')) {
            return 'skipped';
        }

        try {
            (new PlatformArchitecture())->ensure();
            $db = db_connect('platform');
            if (!$db->tableExists('subscriptions') || !$db->fieldExists('expiry_mail_stage', 'subscriptions')) {
                return 'skipped';
            }

            $info = saas_tenant_subscription_info($tenant_id);
            $stage = $this->stageForInfo($info);
            if ($stage === null) {
                return 'skipped';
            }

            $sub = $db->table('subscriptions')
                ->select('subscription_id, period_end, expiry_mail_stage, expiry_mail_period_end')
                ->where('tenant_id', $tenant_id)
                ->orderBy('subscription_id', 'DESC')
                ->get(1)
                ->getRow();

            if ($sub === null) {
                return 'skipped';
            }

            $period_end = (string)($info['period_end'] ?? $sub->period_end ?? '');
            $last_period = trim((string)($sub->expiry_mail_period_end ?? ''));
            $last_stage = trim((string)($sub->expiry_mail_stage ?? ''));

            if ($last_period !== '' && $period_end !== '' && $last_period !== $period_end) {
                $last_stage = '';
            }

            $want = self::STAGE_RANK[$stage] ?? 0;
            $had = self::STAGE_RANK[$last_stage] ?? 0;
            if ($want <= $had) {
                return 'skipped';
            }

            $tenant = $db->table('tenants')
                ->select('tenant_id, tenant_code, company_name, status')
                ->where('tenant_id', $tenant_id)
                ->get(1)
                ->getRow();

            if ($tenant === null) {
                return 'skipped';
            }

            $status = strtolower((string)($tenant->status ?? ''));
            if (!in_array($status, ['active', 'awaiting_payment'], true)) {
                return 'skipped';
            }

            $contact = $this->ownerContact((string)$tenant->tenant_code);
            if ($contact['email'] === '') {
                return 'skipped';
            }

            if (!PlatformMail::isReady()) {
                return 'skipped';
            }

            $pay_url = $contact['pay_url'] !== '' ? $contact['pay_url'] : site_url('saas/checkout');
            $mail = (new PlatformMail())->sendExpiryReminder([
                'to' => $contact['email'],
                'company_name' => (string)($tenant->company_name ?? 'Your shop'),
                'owner_name' => $contact['name'],
                'expires_label' => function_exists('saas_format_period_end')
                    ? saas_format_period_end($period_end)
                    : $period_end,
                'stage' => $stage,
                'pay_url' => $pay_url,
                'days_left' => (int)($info['days_left'] ?? 0),
                'tenant_id' => $tenant_id,
            ]);

            if (!$mail['ok']) {
                log_message('error', 'Expiry mail failed for tenant ' . $tenant_id . ': ' . $mail['error']);

                return 'errors';
            }

            $db->table('subscriptions')
                ->where('subscription_id', (int)$sub->subscription_id)
                ->update([
                    'expiry_mail_stage' => $stage,
                    'expiry_mail_period_end' => $period_end,
                ]);

            return 'sent';
        } catch (Throwable $e) {
            log_message('error', 'Expiry notifyTenant failed: ' . $e->getMessage());

            return 'errors';
        }
    }

    /**
     * @param array{period_end:?string,days_left:?int,is_expired:bool,is_warning:bool,has_period:bool} $info
     */
    private function stageForInfo(array $info): ?string
    {
        if (empty($info['has_period'])) {
            return null;
        }
        if (!empty($info['is_expired'])) {
            return 'expired';
        }

        $days = (int)($info['days_left'] ?? 999);
        if ($days <= 1) {
            return '1';
        }
        if ($days <= 3) {
            return '3';
        }
        if ($days <= 7) {
            return '7';
        }

        return null;
    }

    /**
     * @return array{email:string, name:string, pay_url:string}
     */
    private function ownerContact(string $tenant_code): array
    {
        $empty = ['email' => '', 'name' => '', 'pay_url' => ''];
        $tenant_code = strtolower(trim($tenant_code));
        if ($tenant_code === '') {
            return $empty;
        }

        try {
            $db = db_connect('platform');
            if (!$db->tableExists('subscription_requests')) {
                return $empty;
            }

            $row = $db->table('subscription_requests')
                ->select('owner_email, owner_first_name, owner_last_name, payment_token')
                ->where('tenant_code', $tenant_code)
                ->where('status', 'approved')
                ->orderBy('request_id', 'DESC')
                ->get(1)
                ->getRow();

            if ($row === null) {
                $row = $db->table('subscription_requests')
                    ->select('owner_email, owner_first_name, owner_last_name, payment_token')
                    ->where('tenant_code', $tenant_code)
                    ->orderBy('request_id', 'DESC')
                    ->get(1)
                    ->getRow();
            }

            if ($row === null) {
                return $empty;
            }

            $email = strtolower(trim((string)($row->owner_email ?? '')));
            $name = trim((string)($row->owner_first_name ?? '') . ' ' . (string)($row->owner_last_name ?? ''));
            $token = trim((string)($row->payment_token ?? ''));
            $pay_url = $token !== '' ? site_url('saas/pay/' . $token) : site_url('saas/checkout');

            return ['email' => $email, 'name' => $name, 'pay_url' => $pay_url];
        } catch (Throwable $e) {
            return $empty;
        }
    }
}
