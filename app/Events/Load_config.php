<?php

namespace App\Events;

use App\Libraries\MY_Migration;
use App\Models\Appconfig;
use App\Models\Employee;
use CodeIgniter\Session\Session;
use Config\OSPOS;
use Config\Services;

/**
 * @property my_migration migration;
 * @property session session;
 * @property appconfig appconfig;
 * @property mixed $migration_config
 * @property mixed $config
 */
class Load_config
{
    public Session $session;

    /**
     * Loads configuration from database into App CI config and then applies those settings
     */
    public function load_config(): void
    {
        // Migrations
        $migration_config = config('Migrations');
        $migration = new MY_Migration($migration_config);

        $this->session = session();

        // Database Configuration
        $config = config(OSPOS::class);

        if (!$migration->is_latest()) {
            $this->session->destroy();
        }

        // Language — employee prefs may be set while tenant_config is still partial (pre-backfill).
        $language_code = $config->settings['language_code'] ?? null;
        $language_name = $config->settings['language'] ?? null;
        $language_exists = !empty($language_code) && file_exists('../app/Language/' . $language_code);

        if (empty($language_code) || empty($language_name) || !$language_exists) {
            $config->settings['language'] = DEFAULT_LANGUAGE;
            $config->settings['language_code'] = DEFAULT_LANGUAGE_CODE;
            $language_code = DEFAULT_LANGUAGE_CODE;
        }

        $person_id = (int)($this->session->get('person_id') ?? 0);
        if ($person_id > 0) {
            $employee = model(Employee::class);
            if ($employee->is_logged_in()) {
                $employee_info = $employee->get_logged_in_employee_info();
                if (
                    !empty($employee_info->language_code)
                    && file_exists('../app/Language/' . $employee_info->language_code)
                ) {
                    $language_code = $employee_info->language_code;
                }
            }
        }

        $language = Services::language();
        $language->setLocale($language_code);

        // Time Zone
        date_default_timezone_set($config->settings['timezone'] ?? ini_get('date.timezone'));

        bcscale(max(2, totals_decimals() + tax_decimals()));
    }
}
