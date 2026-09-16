<?php

namespace App\Libraries;

use Throwable;

/**
 * Pushes master template structure/catalog to isolated shop databases.
 * Never copies sales, items, customers, or other private shop data.
 */
class TemplateSync
{
    /**
     * @var list<string>
     */
    private const CATALOG_TABLES = [
        'modules',
        'permissions',
        'app_config',
    ];

    /**
     * @return array{success:bool, version:string, tenants:array<int, array<string, mixed>>}
     */
    public function deploy(): array
    {
        $arch = new PlatformArchitecture();
        $version = $arch->bumpTemplateVersion();
        $source = (string)config('Database')->platform['database'];
        $prefix = trim((string)config('Database')->platform['DBPrefix']);
        if ($prefix === '') {
            $prefix = 'ospos_';
        }

        $platform = db_connect('platform');
        $tenants = $platform->table('tenants')
            ->select('tenant_id, tenant_code, company_name, db_name')
            ->where('tenant_code !=', 'platform')
            ->get()
            ->getResultArray();

        $report = [];
        $provisioner = new TenantDatabaseProvisioner();

        foreach ($tenants as $tenant) {
            $tenant_id = (int)$tenant['tenant_id'];
            $db_name = trim((string)($tenant['db_name'] ?? ''));
            if ($db_name === '' || strcasecmp($db_name, $source) === 0) {
                $report[$tenant_id] = [
                    'tenant_code' => $tenant['tenant_code'],
                    'success' => true,
                    'shared' => true,
                    'message' => 'Shared codebase already applies. Isolate this shop to give it a private database.',
                ];
                continue;
            }

            try {
                $this->syncTenantDatabase($source, $db_name, $prefix);
                if ($platform->fieldExists('template_version', 'tenants')) {
                    $platform->table('tenants')
                        ->where('tenant_id', $tenant_id)
                        ->update(['template_version' => $version]);
                }
                $report[$tenant_id] = [
                    'tenant_code' => $tenant['tenant_code'],
                    'success' => true,
                    'db_name' => $db_name,
                    'message' => 'Template catalog synced. Shop sales and stock were not changed.',
                ];
            } catch (Throwable $e) {
                $report[$tenant_id] = [
                    'tenant_code' => $tenant['tenant_code'],
                    'success' => false,
                    'db_name' => $db_name,
                    'message' => $e->getMessage(),
                ];
            }
        }

        $arch->setTemplateMeta('last_sync_at', date('Y-m-d H:i:s'));
        $arch->setTemplateMeta('last_sync_report', json_encode($report));

        unset($provisioner);

        return [
            'success' => true,
            'version' => $version,
            'tenants' => $report,
        ];
    }

    private function syncTenantDatabase(string $source, string $dest, string $prefix): void
    {
        if ($source === '' || $dest === '' || strcasecmp($source, $dest) === 0) {
            return;
        }

        $config = config('Database')->platform;
        $mysqli = @new \mysqli(
            (string)$config['hostname'],
            (string)$config['username'],
            (string)$config['password'],
            (string)$config['database'],
            (int)$config['port']
        );
        if ($mysqli->connect_errno) {
            throw new \RuntimeException('Could not connect to MySQL for template sync.');
        }

        $source_tables = $this->showTables($mysqli, $source);
        $dest_tables = $this->showTables($mysqli, $dest);
        $control = [
            $prefix . 'sessions',
            $prefix . 'platform_admins',
            $prefix . 'subscription_requests',
            $prefix . 'password_reset_requests',
            $prefix . 'invoices',
            $prefix . 'invoice_payments',
            $prefix . 'tenant_logins',
            $prefix . 'plan_features',
            $prefix . 'plans',
            $prefix . 'subscriptions',
            $prefix . 'tenant_domains',
            $prefix . 'platform_template_meta',
        ];

        foreach ($source_tables as $table) {
            if (!str_starts_with($table, $prefix) || in_array($table, $control, true)) {
                continue;
            }
            if (!in_array($table, $dest_tables, true)) {
                $mysqli->query("CREATE TABLE `{$dest}`.`{$table}` LIKE `{$source}`.`{$table}`");
            }
        }

        foreach (self::CATALOG_TABLES as $logical) {
            $table = $prefix . $logical;
            if (!in_array($table, $source_tables, true) || !in_array($table, $this->showTables($mysqli, $dest), true)) {
                continue;
            }

            if ($logical === 'app_config') {
                continue;
            }

            $mysqli->query("DELETE FROM `{$dest}`.`{$table}`");
            $mysqli->query("INSERT INTO `{$dest}`.`{$table}` SELECT * FROM `{$source}`.`{$table}`");
        }

        $mysqli->close();
    }

    /**
     * @return list<string>
     */
    private function showTables(\mysqli $mysqli, string $database): array
    {
        $tables = [];
        $safe = $mysqli->real_escape_string($database);
        $result = $mysqli->query("SHOW TABLES FROM `{$safe}`");
        if (!$result) {
            return $tables;
        }
        while ($row = $result->fetch_row()) {
            $tables[] = (string)$row[0];
        }
        $result->free();

        return $tables;
    }
}
