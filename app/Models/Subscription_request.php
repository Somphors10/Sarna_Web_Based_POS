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
        'owner_first_name',
        'owner_last_name',
        'owner_email',
        'owner_phone',
        'owner_username',
        'owner_password_hash',
        'plan_id',
        'payment_reference',
        'status',
        'notes',
        'reviewed_by_admin_id',
        'reviewed_at'
    ];

    public function get_pending_with_plan(): array
    {
        return $this->db->table('subscription_requests')
            ->select('subscription_requests.*, plans.plan_name, plans.plan_code, plans.price_monthly')
            ->join('plans', 'plans.plan_id = subscription_requests.plan_id', 'left')
            ->where('subscription_requests.status', 'pending')
            ->orderBy('subscription_requests.request_id', 'desc')
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
}
