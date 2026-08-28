<?php

namespace App\Models;

use CodeIgniter\Model;

class Subscription_request extends Model
{
    protected $DBGroup = 'platform';
    protected $table = 'subscription_requests';
    protected $primaryKey = 'request_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_name',
        'tenant_code',
        'business_type',
        'address',
        'city',
        'country',
        'tax_id',
        'owner_first_name',
        'owner_last_name',
        'owner_email',
        'owner_phone',
        'owner_username',
        'owner_password_hash',
        'plan_id',
        'payment_reference',
        'payment_token',
        'email_verify_token',
        'email_verified_at',
        'status',
        'notes',
        'reviewed_by_admin_id',
        'reviewed_at'
    ];

    public function get_pending_with_plan(): array
    {
        $builder = $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.status', 'pending');
        $this->whereEmailVerified($builder);

        return $builder
            ->orderBy('subscription_requests.request_id', 'desc')
            ->get()
            ->getResultArray();
    }

    /**
     * Owners who registered but have not clicked the Gmail verify link yet.
     *
     * @return list<array<string, mixed>>
     */
    public function get_unverified_pending_with_plan(): array
    {
        $builder = $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.status', 'pending');
        $this->whereEmailUnverified($builder);

        return $builder
            ->orderBy('subscription_requests.request_id', 'desc')
            ->get()
            ->getResultArray();
    }

    public function count_pending(): int
    {
        $builder = $this->db->table('subscription_requests')
            ->where('status', 'pending');
        $this->whereEmailVerified($builder);

        return (int)$builder->countAllResults();
    }

    /**
     * Approved and rejected registrations kept for Super Admin history.
     *
     * @return list<array<string, mixed>>
     */
    public function get_history_with_plan(): array
    {
        return $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->whereIn('subscription_requests.status', ['approved', 'rejected'])
            ->orderBy('subscription_requests.reviewed_at', 'desc')
            ->orderBy('subscription_requests.request_id', 'desc')
            ->get()
            ->getResultArray();
    }

    public function get_latest_pending_id(): int
    {
        $row = $this->db->table('subscription_requests')
            ->select('request_id')
            ->where('status', 'pending');
        $this->whereEmailVerified($row);
        $row = $row->orderBy('request_id', 'desc')
            ->get(1)
            ->getRowArray();

        return $row ? (int)$row['request_id'] : 0;
    }

    /**
     * Pending registration requests created after the given request id.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_registrations_since_id(int $since_id): array
    {
        if ($since_id <= 0) {
            return [];
        }

        $builder = $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.status', 'pending')
            ->where('subscription_requests.request_id >', $since_id);
        $this->whereEmailVerified($builder);

        return $builder
            ->orderBy('subscription_requests.request_id', 'asc')
            ->get()
            ->getResultArray();
    }

    public function get_info_for_review(int $request_id): ?object
    {
        return $this->db->table('subscription_requests')
            ->where('request_id', $request_id)
            ->get(1)
            ->getRow();
    }

    public function find_by_payment_token(string $token): ?object
    {
        $token = trim($token);
        if ($token === '' || !$this->db->fieldExists('payment_token', 'subscription_requests')) {
            return null;
        }

        return $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.payment_token', $token)
            ->get(1)
            ->getRow();
    }

    public function find_for_checkout(string $tenant_code, string $email): ?object
    {
        $tenant_code = strtolower(trim($tenant_code));
        $email = strtolower(trim($email));
        if ($tenant_code === '' || $email === '') {
            return null;
        }

        $rows = $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.tenant_code', $tenant_code)
            ->orderBy('subscription_requests.request_id', 'desc')
            ->get()
            ->getResult();

        foreach ($rows as $row) {
            if (strtolower(trim((string)$row->owner_email)) === $email) {
                return $row;
            }
        }

        return null;
    }

    public function find_by_verify_token(string $token): ?object
    {
        $token = trim($token);
        if ($token === '' || !$this->db->fieldExists('email_verify_token', 'subscription_requests')) {
            return null;
        }

        return $this->db->table('subscription_requests')
            ->where('email_verify_token', $token)
            ->get(1)
            ->getRow();
    }

    public function find_unverified_by_email(string $email): ?object
    {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }

        $builder = $this->db->table('subscription_requests')
            ->where('status', 'pending')
            ->where('owner_email', $email);
        if ($this->db->fieldExists('email_verified_at', 'subscription_requests')) {
            $builder->where('email_verified_at IS NULL', null, false);
        }

        return $builder
            ->orderBy('request_id', 'desc')
            ->get(1)
            ->getRow();
    }

    public function is_email_verified(object $request): bool
    {
        if (!$this->db->fieldExists('email_verified_at', 'subscription_requests')) {
            return true;
        }

        return trim((string)($request->email_verified_at ?? '')) !== '';
    }

    /**
     * @param object $builder
     */
    private function whereEmailVerified($builder): void
    {
        if (!$this->db->fieldExists('email_verified_at', 'subscription_requests')) {
            return;
        }

        $builder->where('email_verified_at IS NOT NULL', null, false);
    }

    /**
     * @param object $builder
     */
    private function whereEmailUnverified($builder): void
    {
        if (!$this->db->fieldExists('email_verified_at', 'subscription_requests')) {
            $builder->where('1 = 0', null, false);

            return;
        }

        $builder->where('email_verified_at IS NULL', null, false);
    }
}
