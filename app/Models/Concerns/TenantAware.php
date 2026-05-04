<?php

namespace App\Models\Concerns;

use CodeIgniter\Database\BaseBuilder;
use Throwable;

trait TenantAware
{
    private ?bool $tenantScopingEnabled = null;

    protected function getTenantId(): int
    {
        if (!$this->isTenantScopingEnabled()) {
            return 1;
        }

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
            try {
                $platform_db = db_connect('platform');
                if ($platform_db->tableExists('tenants')) {
                    $row = $platform_db->table('tenants')
                        ->select('tenant_id')
                        ->where('tenant_code', 'default')
                        ->get(1)
                        ->getRow();
                    $tenant_id = $row ? (int)$row->tenant_id : 1;
                } else {
                    $tenant_id = 1;
                }
            } catch (Throwable $e) {
                $tenant_id = 1;
            }
        }

        $session->set('tenant_id', $tenant_id);

        return $tenant_id;
    }

    protected function scopeTenant(BaseBuilder $builder, string $column = 'tenant_id'): void
    {
        if (!$this->isTenantScopingEnabled()) {
            return;
        }

        $builder->where($column, $this->getTenantId());
    }

    private function isTenantScopingEnabled(): bool
    {
        if ($this->tenantScopingEnabled !== null) {
            return $this->tenantScopingEnabled;
        }

        try {
            if (!$this->db->tableExists('employees') || !$this->db->fieldExists('tenant_id', 'employees')) {
                $this->tenantScopingEnabled = false;
                return false;
            }

            $platform_db = db_connect('platform');
            $this->tenantScopingEnabled = $platform_db->tableExists('tenants');
            return $this->tenantScopingEnabled;
        } catch (Throwable $e) {
            $this->tenantScopingEnabled = false;
            return false;
        }
    }
}
