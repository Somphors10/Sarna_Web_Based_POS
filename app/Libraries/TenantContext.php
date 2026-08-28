<?php

namespace App\Libraries;

use Config\Database;
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

    /**
     * Point default/development/tenant connections at the shop database.
     * Platform/control-plane connection is never switched.
     */
    public function applyRuntimeConnection(int $tenant_id): void
    {
        if (filter_var(env('tenant.forceShared', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->restoreSharedConnection();
            return;
        }

        if ($tenant_id <= 0) {
            $this->restoreSharedConnection();
            return;
        }

        $this->bootstrapSessionTenantDatabase($tenant_id);
        $meta = $this->getTenantDatabaseMeta($tenant_id);
        if (
            $meta === null
            || $meta['db_name'] === ''
            || $meta['db_password'] === ''
            || $meta['db_username'] === ''
        ) {
            $this->restoreSharedConnection();
            return;
        }

        $dbConfig = config('Database');
        $platform_name = (string)$dbConfig->platform['database'];
        if ($meta['db_name'] === $platform_name) {
            return;
        }

        $this->assignDataPlane($dbConfig, $meta);
        $this->selectDatabaseOnOpenConnections($meta['db_name']);
    }

    public function restoreSharedConnection(): void
    {
        $this->clearTenantDatabaseSession();
        $dbConfig = config('Database');
        $shared = [
            'db_hostname' => (string)$dbConfig->platform['hostname'],
            'db_port' => (int)$dbConfig->platform['port'],
            'db_name' => (string)$dbConfig->platform['database'],
            'db_username' => (string)$dbConfig->platform['username'],
            'db_password' => (string)$dbConfig->platform['password'],
            'db_prefix' => (string)$dbConfig->platform['DBPrefix'],
        ];
        $this->assignDataPlane($dbConfig, $shared);
        $this->selectDatabaseOnOpenConnections($shared['db_name']);
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

    /**
     * @param array<string, mixed> $meta
     */
    private function assignDataPlane(Database $dbConfig, array $meta): void
    {
        foreach (['default', 'development', 'tenant'] as $group) {
            $dbConfig->{$group}['hostname'] = $meta['db_hostname'];
            $dbConfig->{$group}['port'] = $meta['db_port'];
            $dbConfig->{$group}['database'] = $meta['db_name'];
            if ($meta['db_username'] !== '') {
                $dbConfig->{$group}['username'] = $meta['db_username'];
            }
            if ($meta['db_password'] !== '') {
                $dbConfig->{$group}['password'] = $meta['db_password'];
            }
            if (!empty($meta['db_prefix'])) {
                $dbConfig->{$group}['DBPrefix'] = $meta['db_prefix'];
            }
        }
    }

    private function selectDatabaseOnOpenConnections(string $database): void
    {
        if ($database === '') {
            return;
        }

        foreach (['default', 'development', 'tenant'] as $group) {
            try {
                $db = Database::connect($group, true);
                if (isset($db->connID) && $db->connID instanceof \mysqli) {
                    if ((string)$db->getDatabase() !== $database) {
                        @$db->connID->select_db($database);
                    }
                }
            } catch (Throwable $e) {
                // Connection may not exist yet for this group.
            }
        }
    }
}
