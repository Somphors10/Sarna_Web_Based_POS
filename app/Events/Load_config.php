<?php

namespace App\Events;

use App\Libraries\MY_Migration;
use App\Models\Appconfig;
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
        }

        $language = Services::language();
        $language->setLocale($config->settings['language_code']);

        // Time Zone
        date_default_timezone_set($config->settings['timezone'] ?? ini_get('date.timezone'));

        bcscale(max(2, totals_decimals() + tax_decimals()));
    }
}
