<?php

namespace App\Models;

use CodeIgniter\Model;

class Tenant extends Model
{
    protected $DBGroup = 'platform';
    protected $table = 'tenants';
    protected $primaryKey = 'tenant_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'tenant_code',
        'company_name',
        'status',
        'timezone',
        'currency_code',
        'db_hostname',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
        'db_prefix'
    ];

    public function get_default(): ?object
    {
        return $this->db->table('tenants')
            ->where('tenant_code', 'default')
            ->get(1)
            ->getRow();
    }

    public function code_exists(string $tenant_code): bool
    {
        return $this->db->table('tenants')
            ->where('tenant_code', $tenant_code)
            ->countAllResults() > 0;
    }

    public function get_with_owner_summary(): array
    {
        $builder = $this->db->table('tenants');
        $builder->select('tenants.tenant_id, tenants.tenant_code, tenants.company_name, tenants.status, tenants.created_at');
        $builder->select('people.first_name, people.last_name, employees.username');
        $builder->join('tenant_users', 'tenant_users.tenant_id = tenants.tenant_id AND tenant_users.tenant_role = "owner"', 'left');
        $builder->join('people', 'people.person_id = tenant_users.person_id', 'left');
        $builder->join('employees', 'employees.person_id = tenant_users.person_id', 'left');
        $builder->orderBy('tenants.tenant_id', 'desc');

        return $builder->get()->getResultArray();
    }

    public function set_status(int $tenant_id, string $status): bool
    {
        return $this->db->table('tenants')
            ->where('tenant_id', $tenant_id)
            ->update(['status' => $status]);
    }

    public function get_database_meta(int $tenant_id): ?array
    {
        if (
            !$this->db->tableExists('tenants')
            || !$this->db->fieldExists('db_name', 'tenants')
        ) {
            return null;
        }

        $row = $this->db->table('tenants')
            ->select('db_hostname, db_port, db_name, db_username, db_password, db_prefix')
            ->where('tenant_id', $tenant_id)
            ->get(1)
            ->getRowArray();

        return empty($row) ? null : $row;
    }
}
