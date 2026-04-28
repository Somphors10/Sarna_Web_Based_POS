<?php

namespace App\Models\Concerns;

use CodeIgniter\Database\BaseBuilder;

trait TenantAware
{
    protected function getTenantId(): int
    {
        $session = session();
        $tenant_id = (int)($session->get('tenant_id') ?? 0);

        if ($tenant_id > 0) {
            return $tenant_id;
        }

        $person_id = (int)($session->get('person_id') ?? 0);
        if ($person_id > 0 && $this->db->tableExists('employees') && $this->db->fieldExists('tenant_id', 'employees')) {
            $employee = $this->db->table('employees')
                ->select('tenant_id')
                ->where('person_id', $person_id)
                ->get(1)
                ->getRow();
            $tenant_id = (int)($employee->tenant_id ?? 0);
        }

        if ($tenant_id <= 0) {
            $platform_db = db_connect('platform');
            $row = $platform_db->table('tenants')
                ->select('tenant_id')
                ->where('tenant_code', 'default')
                ->get(1)
                ->getRow();
            $tenant_id = $row ? (int)$row->tenant_id : 1;
        }

        $session->set('tenant_id', $tenant_id);

        return $tenant_id;
    }

    protected function scopeTenant(BaseBuilder $builder, string $column = 'tenant_id'): void
    {
        $builder->where($column, $this->getTenantId());
    }
}
