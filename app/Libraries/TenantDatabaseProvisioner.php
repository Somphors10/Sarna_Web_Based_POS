<?php

namespace App\Libraries;

use Config\Database;
use Throwable;

/**
 * Creates a private MySQL database per shop and copies only that shop's data.
 * Control-plane tables (tenants, plans, payments) stay in the platform database.
 */
class TenantDatabaseProvisioner
{
    /**
     * Tables that never leave the platform/control-plane database.
     *
     * @var list<string>
     */
    private const CONTROL_PLANE_TABLES = [
        'sessions',
        'platform_admins',
        'subscription_requests',
        'password_reset_requests',
        'invoices',
        'invoice_payments',
        'tenant_logins',
        'plan_features',
        'plans',
        'subscriptions',
        'tenant_domains',
        'platform_template_meta',
    ];

    /**
     * Shared catalog copied into every tenant DB (structure + rows, not shop sales data).
     *
     * @var list<string>
     */
    private const CATALOG_TABLES = [
        'modules',
        'permissions',
        'app_config',
        'migrations',
    ];

    /**
     * Reference rows cloned from the master template tenant.
     *
     * @var list<string>
     */
    private const REFERENCE_TABLES = [
        'tax_jurisdictions',
        'tax_categories',
        'tax_codes',
        'tax_rates',
        'customers_packages',
        'dinner_tables',
        'stock_locations',
        'tenant_config',
    ];

    /**
     * Provision a brand-new shop database from the master template.
     *
     * @param array{first_name?:string,last_name?:string,email?:string,phone?:string,username:string,password_hash:string,company_name?:string} $owner
     * @return array{success:bool, db_name?:string, error?:string}
     */
    public function provisionNew(int $tenant_id, array $owner, int $source_tenant_id = 1): array
    {
        $platform = db_connect('platform');
        $tenant = $platform->table('tenants')->where('tenant_id', $tenant_id)->get(1)->getRowArray();
        if (!$tenant) {
            return ['success' => false, 'error' => 'Tenant not found'];
        }

        $created = $this->createDatabaseForTenant($tenant);
        if (!$created['success']) {
            return $created;
        }

        $db_name = $created['db_name'];
        $source_name = $this->sourceDatabaseName();
        $prefix = $this->tablePrefix();

        try {
            $this->cloneStructures($source_name, $db_name, $prefix);
            $this->copyCatalog($source_name, $db_name, $prefix);
            $this->copyStubTenant($source_name, $db_name, $prefix, $tenant);
            $this->copyReferenceData($source_name, $db_name, $prefix, $source_tenant_id, $tenant_id);
            $person_id = $this->insertOwner($db_name, $prefix, $tenant_id, $owner);
            $this->copyOwnerGrants($source_name, $db_name, $prefix, $person_id);
            $this->saveTenantConnection($tenant_id, $db_name, $prefix);

            $display = trim(($owner['first_name'] ?? '') . ' ' . ($owner['last_name'] ?? ''));
            (new PlatformArchitecture())->upsertTenantLogin(
                $tenant_id,
                $person_id,
                (string)$owner['username'],
                $display,
                true
            );
        } catch (Throwable $e) {
            log_message('error', 'Tenant provision failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage(), 'db_name' => $db_name];
        }

        return ['success' => true, 'db_name' => $db_name];
    }

    /**
     * Move an existing shared-DB shop into its own database without deleting original rows.
     *
     * @return array{success:bool, db_name?:string, error?:string}
     */
    public function isolateExisting(int $tenant_id): array
    {
        $platform = db_connect('platform');
        $tenant = $platform->table('tenants')->where('tenant_id', $tenant_id)->get(1)->getRowArray();
        if (!$tenant) {
            return ['success' => false, 'error' => 'Tenant not found'];
        }

        $source_name = $this->sourceDatabaseName();
        $existing_db = trim((string)($tenant['db_name'] ?? ''));
        if ($existing_db !== '' && strcasecmp($existing_db, $source_name) !== 0) {
            return ['success' => true, 'db_name' => $existing_db];
        }

        $created = $this->createDatabaseForTenant($tenant);
        if (!$created['success']) {
            return $created;
        }

        $db_name = $created['db_name'];
        $prefix = $this->tablePrefix();

        try {
            $this->cloneStructures($source_name, $db_name, $prefix);
            $this->copyCatalog($source_name, $db_name, $prefix);
            $this->copyAllTenantRows($source_name, $db_name, $prefix, $tenant_id);
            $this->saveTenantConnection($tenant_id, $db_name, $prefix);
            $this->registerLoginsFromTenantDb($db_name, $prefix, $tenant_id);
        } catch (Throwable $e) {
            log_message('error', 'Tenant isolate failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage(), 'db_name' => $db_name];
        }

        return ['success' => true, 'db_name' => $db_name];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function isolateAllSharedTenants(): array
    {
        $platform = db_connect('platform');
        $report = [];
        $rows = $platform->table('tenants')
            ->where('tenant_code !=', 'platform')
            ->get()
            ->getResultArray();

        foreach ($rows as $tenant) {
            $tenant_id = (int)$tenant['tenant_id'];
            $existing_db = trim((string)($tenant['db_name'] ?? ''));
            if ($existing_db !== '' && strcasecmp($existing_db, $this->sourceDatabaseName()) !== 0) {
                $report[$tenant_id] = [
                    'success' => true,
                    'skipped' => true,
                    'db_name' => $existing_db,
                    'tenant_code' => $tenant['tenant_code'],
                ];
                continue;
            }

            $result = $this->isolateExisting($tenant_id);
            $result['tenant_code'] = $tenant['tenant_code'];
            $report[$tenant_id] = $result;
        }

        return $report;
    }

    /**
     * @param array<string, mixed> $tenant
     * @return array{success:bool, db_name?:string, error?:string}
     */
    private function createDatabaseForTenant(array $tenant): array
    {
        $db_name = $this->databaseNameForCode((string)$tenant['tenant_code']);
        $config = config('Database')->platform;

        try {
            $mysqli = @new \mysqli(
                (string)$config['hostname'],
                (string)$config['username'],
                (string)$config['password'],
                '',
                (int)$config['port']
            );
            if ($mysqli->connect_errno) {
                return ['success' => false, 'error' => 'Could not connect to MySQL to create the shop database.'];
            }

            $safe = $mysqli->real_escape_string($db_name);
            if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
                $error = $mysqli->error;
                $mysqli->close();
                return ['success' => false, 'error' => $error !== '' ? $error : 'CREATE DATABASE failed'];
            }

            $user = $mysqli->real_escape_string((string)$config['username']);
            @$mysqli->query("GRANT ALL PRIVILEGES ON `{$safe}`.* TO '{$user}'@'%'");
            @$mysqli->query("GRANT ALL PRIVILEGES ON `{$safe}`.* TO '{$user}'@'localhost'");
            $mysqli->close();
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }

        return ['success' => true, 'db_name' => $db_name];
    }

    public function databaseNameForCode(string $tenant_code): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9_]+/i', '_', $tenant_code) ?? 'shop');
        $slug = trim($slug, '_');
        if ($slug === '') {
            $slug = 'shop';
        }

        $name = 'wbpos_t_' . $slug;
        if (strlen($name) > 64) {
            $name = substr($name, 0, 64);
        }

        return $name;
    }

    private function sourceDatabaseName(): string
    {
        return (string)config('Database')->platform['database'];
    }

    private function tablePrefix(): string
    {
        $prefix = trim((string)config('Database')->platform['DBPrefix']);
        return $prefix !== '' ? $prefix : 'ospos_';
    }

    private function cloneStructures(string $source, string $dest, string $prefix): void
    {
        $admin = $this->adminMysqli();
        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=0');
        $tables = $this->listPrefixedTables($admin, $source, $prefix);
        $skip = $this->prefixedNames(self::CONTROL_PLANE_TABLES, $prefix);

        foreach ($tables as $table) {
            if (in_array($table, $skip, true)) {
                continue;
            }

            $this->exec($admin, "CREATE TABLE IF NOT EXISTS `{$dest}`.`{$table}` LIKE `{$source}`.`{$table}`");
        }

        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=1');
        $admin->close();
    }

    private function copyCatalog(string $source, string $dest, string $prefix): void
    {
        $admin = $this->adminMysqli();
        if (strcasecmp($source, $dest) === 0) {
            $admin->close();
            return;
        }
        foreach (self::CATALOG_TABLES as $logical) {
            $table = $prefix . $logical;
            if (!$this->tableExistsIn($admin, $source, $table) || !$this->tableExistsIn($admin, $dest, $table)) {
                continue;
            }
            $this->exec($admin, "INSERT INTO `{$dest}`.`{$table}` SELECT * FROM `{$source}`.`{$table}`");
        }
        $admin->close();
    }

    /**
     * @param array<string, mixed> $tenant
     */
    private function copyStubTenant(string $source, string $dest, string $prefix, array $tenant): void
    {
        $admin = $this->adminMysqli();
        $table = $prefix . 'tenants';
        if ($this->tableExistsIn($admin, $dest, $table) && $this->tableExistsIn($admin, $source, $table)) {
            $id = (int)$tenant['tenant_id'];
            $this->exec($admin, "INSERT INTO `{$dest}`.`{$table}` SELECT * FROM `{$source}`.`{$table}` WHERE tenant_id = {$id}");
        }
        $admin->close();
    }

    private function copyReferenceData(string $source, string $dest, string $prefix, int $source_tenant_id, int $tenant_id): void
    {
        $admin = $this->adminMysqli();
        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=0');
        foreach (self::REFERENCE_TABLES as $logical) {
            $table = $prefix . $logical;
            if (!$this->tableExistsIn($admin, $source, $table) || !$this->tableExistsIn($admin, $dest, $table)) {
                continue;
            }

            if (!$this->columnExists($admin, $source, $table, 'tenant_id')) {
                continue;
            }

            $this->exec(
                $admin,
                "INSERT INTO `{$dest}`.`{$table}` SELECT * FROM `{$source}`.`{$table}` WHERE tenant_id = " . (int)$source_tenant_id
            );

            if ($source_tenant_id !== $tenant_id && $this->columnExists($admin, $dest, $table, 'tenant_id')) {
                $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=0');
                $this->exec(
                    $admin,
                    "UPDATE `{$dest}`.`{$table}` SET tenant_id = " . (int)$tenant_id . " WHERE tenant_id = " . (int)$source_tenant_id
                );
            }
        }

        $company = $admin->real_escape_string((string)($this->tenantCompany($tenant_id) ?? ''));
        $config_table = $prefix . 'tenant_config';
        if ($company !== '' && $this->tableExistsIn($admin, $dest, $config_table)) {
            $this->exec(
                $admin,
                "UPDATE `{$dest}`.`{$config_table}` SET config_value = '{$company}' WHERE config_key = 'company' AND tenant_id = " . (int)$tenant_id
            );
        }

        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=1');
        $admin->close();
    }

    private function tenantCompany(int $tenant_id): ?string
    {
        $row = db_connect('platform')->table('tenants')->select('company_name')->where('tenant_id', $tenant_id)->get(1)->getRow();
        return $row->company_name ?? null;
    }

    /**
     * @param array<string, mixed> $owner
     */
    private function insertOwner(string $db_name, string $prefix, int $tenant_id, array $owner): int
    {
        $db = $this->connectNamed($db_name);
        $db->table('people')->insert([
            'first_name' => (string)($owner['first_name'] ?? 'Owner'),
            'last_name' => (string)($owner['last_name'] ?? ''),
            'gender' => null,
            'phone_number' => (string)($owner['phone'] ?? ''),
            'email' => (string)($owner['email'] ?? ''),
            'address_1' => (string)($owner['address'] ?? ''),
            'address_2' => '',
            'city' => (string)($owner['city'] ?? ''),
            'state' => '',
            'zip' => '',
            'country' => (string)($owner['country'] ?? ''),
            'comments' => '',
            'tenant_id' => $tenant_id,
        ]);
        $person_id = (int)$db->insertID();
        $db->table('employees')->insert([
            'person_id' => $person_id,
            'username' => (string)$owner['username'],
            'password' => (string)$owner['password_hash'],
            'deleted' => 0,
            'hash_version' => 2,
            'tenant_id' => $tenant_id,
        ]);

        if ($db->tableExists('tenant_users')) {
            $db->table('tenant_users')->insert([
                'tenant_id' => $tenant_id,
                'person_id' => $person_id,
                'tenant_role' => 'owner',
                'is_active' => 1,
            ]);
        }

        if ($db->tableExists('tenant_config')) {
            $company = (string)($owner['company_name'] ?? '');
            $db->table('tenant_config')->replace([
                'tenant_id' => $tenant_id,
                'config_key' => 'company',
                'config_value' => $company,
            ]);
        }

        return $person_id;
    }

    private function copyOwnerGrants(string $source, string $dest, string $prefix, int $person_id): void
    {
        if ($person_id <= 0) {
            return;
        }

        $admin = $this->adminMysqli();
        $table = $prefix . 'grants';
        if (!$this->tableExistsIn($admin, $source, $table) || !$this->tableExistsIn($admin, $dest, $table)) {
            $admin->close();
            return;
        }

        $has_menu = $this->columnExists($admin, $source, $table, 'menu_group');
        if ($has_menu) {
            $this->exec(
                $admin,
                "INSERT INTO `{$dest}`.`{$table}` (permission_id, person_id, menu_group)
                 SELECT permission_id, {$person_id}, menu_group FROM `{$source}`.`{$table}` WHERE person_id = 1"
            );
        } else {
            $this->exec(
                $admin,
                "INSERT INTO `{$dest}`.`{$table}` (permission_id, person_id)
                 SELECT permission_id, {$person_id} FROM `{$source}`.`{$table}` WHERE person_id = 1"
            );
        }
        $admin->close();
    }

    private function copyAllTenantRows(string $source, string $dest, string $prefix, int $tenant_id): void
    {
        $admin = $this->adminMysqli();
        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=0');
        $tables = $this->listPrefixedTables($admin, $dest, $prefix);
        $skip = $this->prefixedNames(array_merge(self::CONTROL_PLANE_TABLES, self::CATALOG_TABLES), $prefix);
        $person_ids = [];

        foreach ($tables as $table) {
            if (in_array($table, $skip, true)) {
                continue;
            }

            if ($this->columnExists($admin, $source, $table, 'tenant_id')) {
                $this->exec(
                    $admin,
                    "INSERT INTO `{$dest}`.`{$table}` SELECT * FROM `{$source}`.`{$table}` WHERE tenant_id = " . (int)$tenant_id
                );
            }
        }

        $people = $prefix . 'people';
        if ($this->tableExistsIn($admin, $dest, $people)) {
            $result = $admin->query("SELECT person_id FROM `{$dest}`.`{$people}`");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $person_ids[] = (int)$row['person_id'];
                }
                $result->free();
            }
        }

        $grants = $prefix . 'grants';
        if ($person_ids !== [] && $this->tableExistsIn($admin, $source, $grants) && $this->tableExistsIn($admin, $dest, $grants)) {
            $ids = implode(',', $person_ids);
            $this->exec($admin, "INSERT INTO `{$dest}`.`{$grants}` SELECT * FROM `{$source}`.`{$grants}` WHERE person_id IN ({$ids})");
        }

        $this->exec($admin, 'SET FOREIGN_KEY_CHECKS=1');
        $admin->close();
    }

    private function registerLoginsFromTenantDb(string $db_name, string $prefix, int $tenant_id): void
    {
        $db = $this->connectNamed($db_name);
        if (!$db->tableExists('employees')) {
            return;
        }

        $arch = new PlatformArchitecture();
        $builder = $db->table('employees');
        $builder->select('employees.person_id, employees.username, employees.deleted, people.first_name, people.last_name');
        $builder->join('people', 'people.person_id = employees.person_id', 'left');
        if ($db->fieldExists('tenant_id', 'employees')) {
            $builder->where('employees.tenant_id', $tenant_id);
        }

        $owner_person_id = 0;
        if ($db->tableExists('tenant_users')) {
            $owner = $db->table('tenant_users')
                ->select('person_id')
                ->where('tenant_id', $tenant_id)
                ->where('tenant_role', 'owner')
                ->get(1)
                ->getRow();
            $owner_person_id = $owner ? (int)$owner->person_id : 0;
        }

        foreach ($builder->get()->getResultArray() as $row) {
            $arch->upsertTenantLogin(
                $tenant_id,
                (int)$row['person_id'],
                (string)$row['username'],
                trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                $owner_person_id > 0 && (int)$row['person_id'] === $owner_person_id,
                (int)($row['deleted'] ?? 0) === 0
            );
        }
    }

    private function saveTenantConnection(int $tenant_id, string $db_name, string $prefix): void
    {
        if ($db_name === '' || strcasecmp($db_name, $this->sourceDatabaseName()) === 0) {
            return;
        }

        $config = config('Database')->platform;
        $platform = db_connect('platform');
        $update = [
            'db_hostname' => (string)$config['hostname'],
            'db_port' => (int)$config['port'],
            'db_name' => $db_name,
            'db_username' => (string)$config['username'],
            'db_password' => (string)$config['password'],
            'db_prefix' => $prefix,
            'isolated_at' => date('Y-m-d H:i:s'),
        ];

        if ($platform->tableExists('platform_template_meta')) {
            $version = $platform->table('platform_template_meta')
                ->where('meta_key', 'template_version')
                ->get(1)
                ->getRow();
            if ($version && $platform->fieldExists('template_version', 'tenants')) {
                $update['template_version'] = (string)$version->meta_value;
            }
        }

        $platform->table('tenants')->where('tenant_id', $tenant_id)->update($update);
    }

    private function adminMysqli(): \mysqli
    {
        $config = config('Database')->platform;
        $mysqli = @new \mysqli(
            (string)$config['hostname'],
            (string)$config['username'],
            (string)$config['password'],
            (string)$config['database'],
            (int)$config['port']
        );
        if ($mysqli->connect_errno) {
            throw new \RuntimeException('MySQL admin connection failed: ' . $mysqli->connect_error);
        }
        $mysqli->set_charset('utf8mb4');

        return $mysqli;
    }

    private function connectNamed(string $database)
    {
        $group = config('Database')->default;
        $group['database'] = $database;

        return Database::connect($group, false);
    }

    /**
     * @return list<string>
     */
    private function listPrefixedTables(\mysqli $mysqli, string $database, string $prefix): array
    {
        $tables = [];
        $safe = $mysqli->real_escape_string($database);
        $result = $mysqli->query("SHOW TABLES FROM `{$safe}`");
        if (!$result) {
            return $tables;
        }

        while ($row = $result->fetch_row()) {
            $name = (string)$row[0];
            if ($prefix === '' || str_starts_with($name, $prefix)) {
                $tables[] = $name;
            }
        }
        $result->free();

        return $tables;
    }

    /**
     * @param list<string> $logical
     * @return list<string>
     */
    private function prefixedNames(array $logical, string $prefix): array
    {
        return array_map(static fn(string $name): string => $prefix . $name, $logical);
    }

    private function tableExistsIn(\mysqli $mysqli, string $database, string $table): bool
    {
        $db = $mysqli->real_escape_string($database);
        $tb = $mysqli->real_escape_string($table);
        $result = $mysqli->query("SHOW TABLES FROM `{$db}` LIKE '{$tb}'");
        $exists = $result && $result->num_rows > 0;
        if ($result) {
            $result->free();
        }

        return $exists;
    }

    private function columnExists(\mysqli $mysqli, string $database, string $table, string $column): bool
    {
        $db = $mysqli->real_escape_string($database);
        $tb = $mysqli->real_escape_string($table);
        $col = $mysqli->real_escape_string($column);
        $result = $mysqli->query("SHOW COLUMNS FROM `{$db}`.`{$tb}` LIKE '{$col}'");
        $exists = $result && $result->num_rows > 0;
        if ($result) {
            $result->free();
        }

        return $exists;
    }

    private function exec(\mysqli $mysqli, string $sql): void
    {
        if (!$mysqli->query($sql)) {
            $message = $mysqli->error;
            if (stripos($message, 'Duplicate') !== false) {
                return;
            }
            throw new \RuntimeException($message . ' SQL: ' . $sql);
        }
    }
}
