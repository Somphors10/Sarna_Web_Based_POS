<?php

namespace App\Models;

use CodeIgniter\Model;

class Password_reset_request extends Model
{
    protected $DBGroup = 'platform';
    protected $table = 'password_reset_requests';
    protected $primaryKey = 'request_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'tenant_code',
        'username',
        'person_id',
        'tenant_id',
        'new_password_hash',
        'status',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    public function count_pending(): int
    {
        if (!$this->db->tableExists('password_reset_requests')) {
            return 0;
        }

        return (int)$this->db->table('password_reset_requests')
            ->where('status', 'pending')
            ->countAllResults();
    }

    public function get_pending(): array
    {
        return $this->db->table('password_reset_requests')
            ->where('status', 'pending')
            ->orderBy('request_id', 'desc')
            ->get()
            ->getResultArray();
    }

    public function get_info_for_review(int $request_id): ?object
    {
        return $this->db->table('password_reset_requests')
            ->where('request_id', $request_id)
            ->get(1)
            ->getRow();
    }

    public function has_pending(string $tenant_code, string $username): bool
    {
        return $this->db->table('password_reset_requests')
            ->where('tenant_code', $tenant_code)
            ->where('username', $username)
            ->where('status', 'pending')
            ->countAllResults() > 0;
    }

    /**
     * Resolve tenant + employee for a reset request. Returns null when not found.
     */
    public function resolve_employee(string $tenant_code, string $username): ?object
    {
        $db = db_connect();

        $tenant = $db->table('tenants')
            ->select('tenant_id, company_name, tenant_code, status')
            ->where('tenant_code', $tenant_code)
            ->get(1)
            ->getRow();

        if ($tenant === null || (string)($tenant->status ?? '') !== 'active') {
            return null;
        }

        $employee = $db->table('employees')
            ->select('employees.person_id, employees.username, employees.tenant_id, people.first_name, people.last_name, people.email')
            ->join('people', 'people.person_id = employees.person_id')
            ->where('employees.username', $username)
            ->where('employees.tenant_id', (int)$tenant->tenant_id)
            ->where('employees.deleted', 0)
            ->get(1)
            ->getRow();

        if ($employee === null) {
            return null;
        }

        $employee->tenant_code = $tenant->tenant_code;
        $employee->company_name = $tenant->company_name;

        return $employee;
    }
}
