<?php



namespace App\Libraries;



use Throwable;



/**

 * Seeds per-tenant defaults so each company starts with isolated config and reference data.

 */

class TenantSeeder

{

    /**

     * Seed a newly created tenant (skips source tenant).

     */

    public function seedForTenant(int $tenant_id, int $source_tenant_id = 1): array

    {

        if ($tenant_id <= 0 || $tenant_id === $source_tenant_id) {

            return [];

        }



        return $this->runSeed($tenant_id, $source_tenant_id);

    }



    /**

     * Backfill one existing tenant with any missing config or reference rows.

     */

    public function backfillForTenant(int $tenant_id, int $source_tenant_id = 1): array

    {

        if ($tenant_id <= 0) {

            return ['error' => 'invalid tenant_id'];

        }



        if ($tenant_id === $source_tenant_id) {

            return ['skipped' => 'source tenant'];

        }



        return $this->runSeed($tenant_id, $source_tenant_id, true);

    }



    /**

     * Backfill every tenant except the source tenant.

     *

     * @return array<int, array<string, mixed>>

     */

    public function backfillAllTenants(int $source_tenant_id = 1): array

    {

        $db = db_connect();

        $report = [];



        if (!$db->tableExists('tenants')) {

            return $report;

        }



        foreach ($db->table('tenants')->orderBy('tenant_id', 'asc')->get()->getResultArray() as $tenant) {

            $tenant_id = (int)$tenant['tenant_id'];

            if ($tenant_id <= 0 || $tenant_id === $source_tenant_id) {

                continue;

            }



            $result = $this->backfillForTenant($tenant_id, $source_tenant_id);

            $result['tenant_code'] = $tenant['tenant_code'] ?? '';

            $result['company_name'] = $tenant['company_name'] ?? '';

            $report[$tenant_id] = $result;

        }



        return $report;

    }



    /**

     * @return array<string, mixed>

     */

    private function runSeed(int $tenant_id, int $source_tenant_id, bool $is_backfill = false): array

    {

        $db = db_connect();

        $counts = [

            'config_added' => 0,

            'tax_jurisdictions' => 0,

            'tax_categories' => 0,

            'tax_codes' => 0,

            'tax_rates' => 0,

            'reward_packages' => 0,

            'dinner_tables' => 0,

            'stock_locations' => 0,

        ];



        try {

            $counts['config_added'] = $this->seedTenantConfig($db, $tenant_id, $source_tenant_id);

            $jurisdiction_map = $this->cloneSimpleTable($db, 'tax_jurisdictions', 'jurisdiction_id', $tenant_id, $source_tenant_id);

            $counts['tax_jurisdictions'] = count($jurisdiction_map);

            $category_map = $this->cloneSimpleTable($db, 'tax_categories', 'tax_category_id', $tenant_id, $source_tenant_id);

            $counts['tax_categories'] = count($category_map);

            $code_map = $this->cloneSimpleTable($db, 'tax_codes', 'tax_code_id', $tenant_id, $source_tenant_id);

            $counts['tax_codes'] = count($code_map);

            $counts['tax_rates'] = $this->cloneTaxRates($db, $tenant_id, $source_tenant_id, $jurisdiction_map, $category_map, $code_map);

            $counts['reward_packages'] = count($this->cloneSimpleTable($db, 'customers_packages', 'package_id', $tenant_id, $source_tenant_id));

            $counts['dinner_tables'] = count($this->cloneSimpleTable($db, 'dinner_tables', 'dinner_table_id', $tenant_id, $source_tenant_id));

            $counts['stock_locations'] = count($this->cloneSimpleTable($db, 'stock_locations', 'location_id', $tenant_id, $source_tenant_id));

        } catch (Throwable $e) {

            log_message('error', 'TenantSeeder failed for tenant ' . $tenant_id . ': ' . $e->getMessage());



            return [

                'success' => false,

                'error' => $e->getMessage(),

                'counts' => $counts,

                'backfill' => $is_backfill,

            ];

        }



        return [

            'success' => true,

            'counts' => $counts,

            'backfill' => $is_backfill,

        ];

    }



    /**

     * Merge missing config keys from the source tenant without overwriting tenant-specific values.

     */

    private function seedTenantConfig($db, int $tenant_id, int $source_tenant_id): int

    {

        if (!$db->tableExists('tenant_config')) {

            return 0;

        }



        $source_rows = $db->table('tenant_config')->where('tenant_id', $source_tenant_id)->get()->getResultArray();

        if (empty($source_rows) && $db->tableExists('app_config')) {

            foreach ($db->table('app_config')->get()->getResultArray() as $row) {

                $source_rows[] = [

                    'config_key' => $row['key'],

                    'config_value' => $row['value'],

                ];

            }

        }



        if (empty($source_rows)) {

            return 0;

        }



        $existing_keys = array_column(

            $db->table('tenant_config')->select('config_key')->where('tenant_id', $tenant_id)->get()->getResultArray(),

            'config_key'

        );



        $batch = [];

        foreach ($source_rows as $row) {

            if (in_array($row['config_key'], $existing_keys, true)) {

                continue;

            }



            $batch[] = [

                'tenant_id' => $tenant_id,

                'config_key' => $row['config_key'],

                'config_value' => $row['config_value'],

            ];

        }



        if (!empty($batch)) {

            $db->table('tenant_config')->insertBatch($batch);

        }



        return count($batch);

    }



    /**

     * @return array<int, int> old_id => new_id

     */

    private function cloneSimpleTable($db, string $table, string $pk, int $tenant_id, int $source_tenant_id): array

    {

        $map = [];



        if (!$db->tableExists($table) || !$db->fieldExists('tenant_id', $table)) {

            return $map;

        }



        if ($db->table($table)->where('tenant_id', $tenant_id)->countAllResults() > 0) {

            return $map;

        }



        foreach ($db->table($table)->where('tenant_id', $source_tenant_id)->get()->getResultArray() as $row) {

            $old_pk = (int)$row[$pk];

            unset($row[$pk]);

            $row['tenant_id'] = $tenant_id;

            $db->table($table)->insert($row);

            $map[$old_pk] = (int)$db->insertID();

        }



        return $map;

    }



    private function cloneTaxRates($db, int $tenant_id, int $source_tenant_id, array $jurisdiction_map, array $category_map, array $code_map): int

    {

        if (!$db->tableExists('tax_rates') || !$db->fieldExists('tenant_id', 'tax_rates')) {

            return 0;

        }



        if ($db->table('tax_rates')->where('tenant_id', $tenant_id)->countAllResults() > 0) {

            return 0;

        }



        $inserted = 0;



        foreach ($db->table('tax_rates')->where('tenant_id', $source_tenant_id)->get()->getResultArray() as $row) {

            unset($row['tax_rate_id']);

            $row['tenant_id'] = $tenant_id;



            if (isset($row['rate_jurisdiction_id'], $jurisdiction_map[$row['rate_jurisdiction_id']])) {

                $row['rate_jurisdiction_id'] = $jurisdiction_map[$row['rate_jurisdiction_id']];

            }

            if (isset($row['rate_tax_category_id'], $category_map[$row['rate_tax_category_id']])) {

                $row['rate_tax_category_id'] = $category_map[$row['rate_tax_category_id']];

            }

            if (isset($row['rate_tax_code_id'], $code_map[$row['rate_tax_code_id']])) {

                $row['rate_tax_code_id'] = $code_map[$row['rate_tax_code_id']];

            }



            if ($db->table('tax_rates')->insert($row)) {

                $inserted++;

            }

        }



        return $inserted;

    }

}


