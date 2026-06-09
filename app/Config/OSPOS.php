<?php

namespace Config;

use App\Models\Appconfig;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseConfig;
use Config\Services;

/**
 * This class holds the configuration options stored from the database so that on launch those settings can be cached
 * once in memory.  The settings are referenced frequently, so there is a significant performance hit to not storing
 * them.
 */
class OSPOS extends BaseConfig
{
    public array $settings;
    public string $commit_sha1 = 'dev';    // TODO: Travis scripts need to be updated to replace this with the commit hash on build
    private CacheInterface $cache;

    public function __construct()
    {
        parent::__construct();
        $this->cache = Services::cache();
        $this->set_settings();
    }

    private function settingsCacheKey(): string
    {
        $tenant_id = 0;
        try {
            $tenant_id = (int)(session()->get('tenant_id') ?? 0);
        } catch (\Throwable $e) {
            $tenant_id = 0;
        }

        return $tenant_id > 0 ? 'settings_tenant_' . $tenant_id : 'settings_global';
    }

    /**
     * @return void
     */
    public function set_settings(): void
    {
        $cache_key = $this->settingsCacheKey();
        $cache = $this->cache->get($cache_key);

        if ($cache) {
            $this->settings = decode_array($cache);
        } else {
            $appconfig = model(Appconfig::class);
            $this->settings = $appconfig->get_all_assoc();
            $this->cache->save($cache_key, encode_array($this->settings));
        }
    }

    /**
     * @return void
     */
    public function update_settings(): void
    {
        $this->cache->delete($this->settingsCacheKey());
        $this->cache->delete('settings');
        $this->set_settings();
    }
}
