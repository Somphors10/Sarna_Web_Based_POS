<?php

namespace App\Commands;

use App\Libraries\TenantSeeder;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeedTenants extends BaseCommand
{
    protected $group       = 'OSPOS';
    protected $name        = 'tenants:seed';
    protected $description = 'Backfill per-tenant config and reference data from the default tenant.';
    protected $usage       = 'tenants:seed [tenant_id]';
    protected $arguments   = [
        'tenant_id' => 'Optional tenant ID. Seeds all non-default tenants when omitted.',
    ];

    public function run(array $params): void
    {
        $seeder = new TenantSeeder();
        $tenant_id = $params[0] ?? null;

        if ($tenant_id !== null && $tenant_id !== '') {
            $report = $seeder->backfillForTenant((int)$tenant_id);
            $this->printReport((int)$tenant_id, $report);
            return;
        }

        CLI::write('Backfilling all tenants from tenant 1...', 'yellow');

        foreach ($seeder->backfillAllTenants() as $tid => $report) {
            $this->printReport($tid, $report, $report['tenant_code'] ?? '', $report['company_name'] ?? '');
        }

        CLI::write('Done. Clear writable/cache/settings and log in again to load fresh settings.', 'green');
    }

    private function printReport(int $tenant_id, array $report, string $code = '', string $company = ''): void
    {
        $label = 'Tenant ' . $tenant_id;
        if ($code !== '') {
            $label .= ' (' . $code . ')';
        }
        if ($company !== '') {
            $label .= ' - ' . $company;
        }

        if (!empty($report['error'])) {
            CLI::write($label . ': ' . $report['error'], 'red');
            return;
        }

        if (!empty($report['skipped'])) {
            CLI::write($label . ': skipped', 'light_gray');
            return;
        }

        $counts = $report['counts'] ?? [];
        $parts = [];
        foreach ($counts as $key => $value) {
            if ((int)$value > 0) {
                $parts[] = $key . '=' . $value;
            }
        }

        if (empty($parts)) {
            CLI::write($label . ': already complete', 'light_gray');
            return;
        }

        CLI::write($label . ': ' . implode(', ', $parts), 'green');
    }
}
