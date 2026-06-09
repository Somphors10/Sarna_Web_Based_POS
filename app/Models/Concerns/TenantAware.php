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

    protected function scopeModelTenant(BaseBuilder $builder, ?string $table = null): void
    {
        $table = $table ?? ($this->table ?? '');
        if ($table === '' || !$this->db->tableExists($table) || !$this->db->fieldExists('tenant_id', $table)) {
            return;
        }

        $this->scopeTenant($builder, $table . '.tenant_id');
    }

    protected function tenantIdForInsert(?string $table = null): ?int
    {
        $table = $table ?? ($this->table ?? '');
        if ($table === '' || !$this->isTenantScopingEnabled() || !$this->db->fieldExists('tenant_id', $table)) {
            return null;
        }

        return $this->getTenantId();
    }

    protected function tenantSqlAnd(string $column): string
    {
        if (!$this->isTenantScopingEnabled()) {
            return '';
        }

        return ' AND ' . $column . ' = ' . (int)$this->getTenantId();
    }

    /**
     * Per-tenant display sequence (1, 2, 3...) based on primary key order within the company.
     */
    protected function tenantSequenceSql(
        string $table,
        string $pkColumn,
        string $sequenceAlias,
        string $tableAlias = '',
        string $extraWhere = ''
    ): string {
        if (!$this->isTenantScopingEnabled()) {
            return '1 AS ' . $sequenceAlias;
        }

        $prefixedTable = $this->db->prefixTable($table);
        $outerAlias = $tableAlias !== '' ? $tableAlias : $table;

        return '(
            SELECT COUNT(*)
            FROM ' . $prefixedTable . ' AS tenant_seq_inner
            WHERE tenant_seq_inner.tenant_id = ' . $outerAlias . '.tenant_id
              AND tenant_seq_inner.' . $pkColumn . ' <= ' . $outerAlias . '.' . $pkColumn . '
              ' . $extraWhere . '
        ) AS ' . $sequenceAlias;
    }

    protected function resolveTenantPkBySequence(string $table, string $pkColumn, int $sequence): ?int
    {
        if ($sequence <= 0 || !$this->isTenantScopingEnabled()) {
            return $sequence > 0 ? $sequence : null;
        }

        if (!$this->db->tableExists($table) || !$this->db->fieldExists('tenant_id', $table)) {
            return $sequence;
        }

        $builder = $this->db->table($table);
        $builder->select($pkColumn);
        $this->scopeTenant($builder, 'tenant_id');
        $builder->orderBy($pkColumn, 'ASC');
        $builder->limit(1, $sequence - 1);

        $row = $builder->get()->getRow();

        return $row ? (int)$row->{$pkColumn} : null;
    }

    protected function isTenantScopingEnabled(): bool
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
