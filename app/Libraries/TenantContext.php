<?php

namespace App\Libraries;

use Throwable;

class TenantContext
{
    private const SESSION_KEYS = [
        'tenant_db_hostname',
        'tenant_db_port',
        'tenant_db_name',
        'tenant_db_username',
        'tenant_db_password',
        'tenant_db_prefix',
    ];

    /**
     * Store tenant DB connection metadata in session.
     * If metadata is not configured, clear session overrides (shared DB fallback).
     */
    public function bootstrapSessionTenantDatabase(int $tenant_id): void
    {
        if ($tenant_id <= 0) {
            $this->clearTenantDatabaseSession();
            return;
        }

        try {
            $meta = $this->getTenantDatabaseMeta($tenant_id);
        } catch (Throwable $e) {
            $meta = null;
        }

        if ($meta === null) {
            $this->clearTenantDatabaseSession();
            return;
        }

        session()->set([
            'tenant_db_hostname' => $meta['db_hostname'],
            'tenant_db_port' => $meta['db_port'],
            'tenant_db_name' => $meta['db_name'],
            'tenant_db_username' => $meta['db_username'],
            'tenant_db_password' => $meta['db_password'],
            'tenant_db_prefix' => $meta['db_prefix'],
        ]);
    }

    public function clearTenantDatabaseSession(): void
    {
        try {
            session()->remove(self::SESSION_KEYS);
        } catch (Throwable $e) {
            // Session service can be unavailable in early boot.
        }
    }

    /**
     * Read tenant DB metadata from platform tenants table.
     */
    public function getTenantDatabaseMeta(int $tenant_id): ?array
    {
        try {
            $platform_db = db_connect('platform');
        } catch (Throwable $e) {
            return null;
        }

        if (
            !$platform_db->tableExists('tenants')
            || !$platform_db->fieldExists('db_name', 'tenants')
        ) {
            return null;
        }

        $row = $platform_db->table('tenants')
            ->select('db_hostname, db_port, db_name, db_username, db_password, db_prefix')
            ->where('tenant_id', $tenant_id)
            ->get(1)
            ->getRowArray();

        if (empty($row) || empty($row['db_name'])) {
            return null;
        }

        return [
            'db_hostname' => (string)($row['db_hostname'] ?? 'localhost'),
            'db_port' => (int)($row['db_port'] ?? 3306),
            'db_name' => (string)$row['db_name'],
            'db_username' => (string)($row['db_username'] ?? ''),
            'db_password' => (string)($row['db_password'] ?? ''),
            'db_prefix' => trim((string)($row['db_prefix'] ?? '')) !== '' ? (string)$row['db_prefix'] : 'ospos_',
        ];
    }
}

