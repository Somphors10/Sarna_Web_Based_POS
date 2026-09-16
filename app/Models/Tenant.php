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
        if ($this->db->fieldExists('db_name', 'tenants')) {
            $builder->select('tenants.db_name');
        }
        if ($this->db->fieldExists('isolated_at', 'tenants')) {
            $builder->select('tenants.isolated_at');
        }
        if ($this->db->fieldExists('template_version', 'tenants')) {
            $builder->select('tenants.template_version');
        }
        $builder->select('people.first_name, people.last_name, people.email, people.phone_number, people.address_1, people.city, people.country, employees.username');
        $builder->join('tenant_users', 'tenant_users.tenant_id = tenants.tenant_id AND tenant_users.tenant_role = "owner"', 'left');
        $builder->join('people', 'people.person_id = tenant_users.person_id', 'left');
        $builder->join('employees', 'employees.person_id = tenant_users.person_id', 'left');
        $builder->where('tenants.tenant_code !=', 'platform');
        $builder->orderBy('tenants.tenant_id', 'desc');

        $rows = $builder->get()->getResultArray();

        if (!$this->db->tableExists('tenant_logins')) {
            return $rows;
        }

        $logins = $this->db->table('tenant_logins')
            ->where('is_owner', 1)
            ->get()
            ->getResultArray();
        $by_tenant = [];
        foreach ($logins as $login) {
            $by_tenant[(int)$login['tenant_id']] = $login;
        }

        foreach ($rows as &$row) {
            $login = $by_tenant[(int)$row['tenant_id']] ?? null;
            if ($login === null) {
                continue;
            }
            if (trim((string)($row['username'] ?? '')) === '') {
                $row['username'] = $login['username'];
            }
            if (trim((string)($row['first_name'] ?? '') . (string)($row['last_name'] ?? '')) === '' && !empty($login['display_name'])) {
                $parts = preg_split('/\s+/', (string)$login['display_name'], 2) ?: [];
                $row['first_name'] = $parts[0] ?? '';
                $row['last_name'] = $parts[1] ?? '';
            }
        }
        unset($row);

        return $rows;
    }

    public function set_status(int $tenant_id, string $status): bool
    {
        $tenant = $this->db->table('tenants')->where('tenant_id', $tenant_id)->get(1)->getRowArray();
        if (($tenant['tenant_code'] ?? '') === 'platform') {
            return false;
        }

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
