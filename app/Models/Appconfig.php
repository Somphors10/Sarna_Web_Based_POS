<?php

namespace App\Models;

use App\Models\Concerns\TenantAware;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;
use Config\OSPOS;
use ReflectionException;

/**
 * Appconfig class — per-tenant settings in tenant_config; app_config is install template only.
 */
class Appconfig extends Model
{
    use TenantAware;

    protected $table = 'app_config';
    protected $primaryKey = 'key';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'key',
        'value'
    ];

    private function usesTenantConfig(): bool
    {
        return $this->isTenantScopingEnabled() && $this->db->tableExists('tenant_config');
    }

    /**
     * Read tenant_config and backfill missing keys from the install template when needed.
     */
    private function loadTenantSettingsAssoc(): array
    {
        $tenant_id = $this->getTenantId();
        $settings = [];

        foreach ($this->db->table('tenant_config')
            ->where('tenant_id', $tenant_id)
            ->get()
            ->getResult() as $row) {
            $settings[$row->config_key] = $row->config_value;
        }

        if ($this->db->tableExists('app_config')) {
            $expected_keys = (int)$this->db->table('app_config')->countAllResults();

            if ($expected_keys > 0 && count($settings) < $expected_keys) {
                $this->ensureCompleteConfig($tenant_id);

                $settings = [];
                foreach ($this->db->table('tenant_config')
                    ->where('tenant_id', $tenant_id)
                    ->get()
                    ->getResult() as $row) {
                    $settings[$row->config_key] = $row->config_value;
                }
            }
        }

        return $settings;
    }

    /**
     * Checks to see if a given configuration exists in the database.
     */
    public function exists(string $key): bool
    {
        if ($this->usesTenantConfig()) {
            return $this->db->table('tenant_config')
                    ->where('tenant_id', $this->getTenantId())
                    ->where('config_key', $key)
                    ->countAllResults() === 1;
        }

        $builder = $this->db->table('app_config');
        $builder->where('key', $key);

        return ($builder->get()->getNumRows() === 1);
    }

    /**
     * Get all configuration values for the active tenant.
     */
    public function get_all(): ResultInterface
    {
        if (!$this->usesTenantConfig()) {
            $builder = $this->db->table('app_config');
            $builder->orderBy('key', 'asc');
            return $builder->get();
        }

        $builder = $this->db->table('tenant_config');
        $builder->select('config_key AS `key`, config_value AS `value`');
        $builder->where('tenant_id', $this->getTenantId());
        $builder->orderBy('config_key', 'asc');

        return $builder->get();
    }

    /**
     * Returns settings for the active tenant only (no shared app_config merge).
     */
    public function get_all_assoc(): array
    {
        if ($this->usesTenantConfig()) {
            return $this->loadTenantSettingsAssoc();
        }

        $settings = [];

        if ($this->db->tableExists('app_config')) {
            foreach ($this->db->table('app_config')->get()->getResult() as $row) {
                $settings[$row->key] = $row->value;
            }
        }

        return $settings;
    }

    /**
     * Copy any missing config keys from the global template into this tenant.
     * app_config remains install defaults only; each shop reads tenant_config.
     */
    public function ensureCompleteConfig(?int $tenant_id = null): int
    {
        if (!$this->usesTenantConfig()) {
            return 0;
        }

        $tenant_id = $tenant_id ?? $this->getTenantId();

        if ($tenant_id <= 0 || !$this->db->tableExists('app_config')) {
            return 0;
        }

        $expected_keys = (int)$this->db->table('app_config')->countAllResults();
        $existing_keys = (int)$this->db->table('tenant_config')
            ->where('tenant_id', $tenant_id)
            ->countAllResults();

        if ($expected_keys > 0 && $existing_keys >= $expected_keys) {
            return 0;
        }

        return (new \App\Libraries\TenantSeeder())->ensureTenantConfig($tenant_id);
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function get_value(string $key, string $default = ''): string
    {
        if ($this->usesTenantConfig()) {
            $row = $this->db->table('tenant_config')
                ->where('tenant_id', $this->getTenantId())
                ->where('config_key', $key)
                ->get(1)
                ->getRow();

            if ($row !== null) {
                return (string)$row->config_value;
            }

            return $default;
        }

        $builder = $this->db->table('app_config');
        $query = $builder->getWhere(['key' => $key], 1);

        if ($query->getNumRows() === 1) {
            return $query->getRow()->value;
        }

        return $default;
    }

    /**
     * Saves config for the active tenant.
     *
     * @param array|object $data
     * @return bool
     * @throws ReflectionException
     */
    public function save($data): bool
    {
        $key = array_keys($data)[0];
        $value = $data[$key];

        if ($this->usesTenantConfig()) {
            $tenant_id = $this->getTenantId();

            $row = $this->db->table('tenant_config')
                ->where('tenant_id', $tenant_id)
                ->where('config_key', $key)
                ->get(1)
                ->getRow();

            if ($row !== null) {
                $success = $this->db->table('tenant_config')
                    ->where('tenant_id', $tenant_id)
                    ->where('config_key', $key)
                    ->update(['config_value' => $value]);
            } else {
                $success = $this->db->table('tenant_config')->insert([
                    'tenant_id' => $tenant_id,
                    'config_key' => $key,
                    'config_value' => $value,
                ]);
            }
        } else {
            $save_data = ['key' => $key, 'value' => $value];
            $success = parent::save($save_data);
        }

        if ($success) {
            config(OSPOS::class)->update_settings();
        }

        return (bool)$success;
    }

    /**
     * @throws ReflectionException
     */
    public function batch_save(array $data): bool
    {
        $success = true;

        $this->db->transStart();

        foreach ($data as $key => $value) {
            $success &= $this->save([$key => $value]);
        }

        $this->db->transComplete();

        $success &= $this->db->transStatus();

        return $success;
    }

    /**
     * Deletes a row from config.
     */
    public function delete($id = null, bool $purge = false): bool
    {
        if ($this->usesTenantConfig()) {
            return $this->db->table('tenant_config')
                ->where('tenant_id', $this->getTenantId())
                ->where('config_key', $id)
                ->delete();
        }

        $builder = $this->db->table('app_config');
        return $builder->delete(['key' => $id]);
    }

    /**
     * @return bool
     */
    public function delete_all(): bool
    {
        if ($this->usesTenantConfig()) {
            return $this->db->table('tenant_config')
                ->where('tenant_id', $this->getTenantId())
                ->delete();
        }

        $builder = $this->db->table('app_config');
        return $builder->emptyTable();
    }

    /**
     * @throws ReflectionException
     */
    public function acquire_next_invoice_sequence(bool $save = true): string
    {
        $last_used = (int)$this->get_value('last_used_invoice_number', '0') + 1;

        if ($save) {
            $this->save(['last_used_invoice_number' => $last_used]);
        }

        return (string)$last_used;
    }

    /**
     * @throws ReflectionException
     */
    public function acquire_next_quote_sequence(bool $save = true): string
    {
        $last_used = (int)$this->get_value('last_used_quote_number', '0') + 1;

        if ($save) {
            $this->save(['last_used_quote_number' => $last_used]);
        }

        return (string)$last_used;
    }

    /**
     * @throws ReflectionException
     */
    public function acquire_next_work_order_sequence(bool $save = true): string
    {
        $last_used = (int)$this->get_value('last_used_work_order_number', '0') + 1;

        if ($save) {
            $this->save(['last_used_work_order_number' => $last_used]);
        }

        return (string)$last_used;
    }

    /**
     * Replace install/demo company email with the shop owner's registration email.
     * Safe for shops that registered before provisioning was fixed.
     *
     * @return string|null Corrected email when a repair ran, otherwise null
     */
    public function repairPlaceholderShopEmail(): ?string
    {
        $placeholders = ['admin@wbpos.demo', 'changeme@example.com', ''];
        $current = strtolower(trim($this->get_value('email', '')));
        if (!in_array($current, $placeholders, true)) {
            return null;
        }

        $owner_email = $this->resolveOwnerRegistrationEmail();
        if ($owner_email === '' || in_array($owner_email, $placeholders, true)) {
            return null;
        }

        // Write directly to avoid recursive OSPOS settings reload from save().
        $tenant_id = $this->getTenantId();
        if ($this->usesTenantConfig() && $tenant_id > 0) {
            $row = $this->db->table('tenant_config')
                ->where('tenant_id', $tenant_id)
                ->where('config_key', 'email')
                ->get(1)
                ->getRow();
            if ($row !== null) {
                $this->db->table('tenant_config')
                    ->where('tenant_id', $tenant_id)
                    ->where('config_key', 'email')
                    ->update(['config_value' => $owner_email]);
            } else {
                $this->db->table('tenant_config')->insert([
                    'tenant_id'    => $tenant_id,
                    'config_key'   => 'email',
                    'config_value' => $owner_email,
                ]);
            }
        }

        if ($this->db->tableExists('app_config')) {
            $exists = $this->db->table('app_config')->where('key', 'email')->countAllResults() > 0;
            if ($exists) {
                $this->db->table('app_config')->where('key', 'email')->update(['value' => $owner_email]);
            } else {
                $this->db->table('app_config')->insert(['key' => 'email', 'value' => $owner_email]);
            }
        }

        return $owner_email;
    }

    private function resolveOwnerRegistrationEmail(): string
    {
        $tenant_id = $this->getTenantId();
        $placeholders = ['admin@wbpos.demo', 'changeme@example.com'];

        // Owner person on this shop DB
        if ($tenant_id > 0 && $this->db->tableExists('people') && $this->db->tableExists('tenant_users')) {
            $row = $this->db->table('people p')
                ->select('p.email')
                ->join('tenant_users tu', 'tu.person_id = p.person_id AND tu.tenant_id = p.tenant_id', 'inner')
                ->where('p.tenant_id', $tenant_id)
                ->where('tu.tenant_role', 'owner')
                ->where('p.email !=', '')
                ->get(1)
                ->getRow();
            $email = strtolower(trim((string)($row->email ?? '')));
            if ($email !== '' && !in_array($email, $placeholders, true)) {
                return $email;
            }
        }

        // Any non-demo employee email
        if ($tenant_id > 0 && $this->db->tableExists('people') && $this->db->tableExists('employees')) {
            $row = $this->db->table('people p')
                ->select('p.email')
                ->join('employees e', 'e.person_id = p.person_id', 'inner')
                ->where('p.tenant_id', $tenant_id)
                ->where('p.email !=', '')
                ->orderBy('p.person_id', 'asc')
                ->get(1)
                ->getRow();
            $email = strtolower(trim((string)($row->email ?? '')));
            if ($email !== '' && !in_array($email, $placeholders, true)) {
                return $email;
            }
        }

        // Platform registration request
        try {
            $platform = db_connect('platform');
            if (!$platform->tableExists('subscription_requests') || !$platform->tableExists('tenants')) {
                return '';
            }

            $tenant = $platform->table('tenants')
                ->select('tenant_code')
                ->where('tenant_id', $tenant_id)
                ->get(1)
                ->getRow();
            $code = (string)($tenant->tenant_code ?? '');
            if ($code === '') {
                return '';
            }

            $row = $platform->table('subscription_requests')
                ->select('owner_email')
                ->where('tenant_code', $code)
                ->where('owner_email !=', '')
                ->orderBy('request_id', 'desc')
                ->get(1)
                ->getRow();

            return strtolower(trim((string)($row->owner_email ?? '')));
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Drop inherited/shared logo paths so each business only shows its own upload.
     * Valid logos live under uploads/tenants/{tenant_id}/company_logo.*
     *
     * @return bool True when company_logo was cleared
     */
    public function repairInheritedCompanyLogo(): bool
    {
        $tenant_id = $this->getTenantId();
        if ($tenant_id <= 0) {
            return false;
        }

        $current = trim($this->get_value('company_logo', ''));
        if ($current === '') {
            return false;
        }

        if ($this->isTenantOwnedLogoPath($current, $tenant_id)) {
            return false;
        }

        $this->writeConfigValue('company_logo', '');

        return true;
    }

    /**
     * Logo path relative to public/uploads/ that belongs only to this tenant.
     */
    public function tenantCompanyLogoPath(?int $tenant_id = null): string
    {
        $tenant_id = $tenant_id ?? $this->getTenantId();
        $logo = trim($this->get_value('company_logo', ''));
        if ($logo === '' || $tenant_id <= 0 || !$this->isTenantOwnedLogoPath($logo, $tenant_id)) {
            return '';
        }

        if (!is_file(FCPATH . 'uploads/' . $logo)) {
            return '';
        }

        return $logo;
    }

    private function isTenantOwnedLogoPath(string $logo, int $tenant_id): bool
    {
        if ($logo === '' || str_contains($logo, '..') || !preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $logo)) {
            return false;
        }

        $prefix = 'tenants/' . $tenant_id . '/company_logo.';
        if (!str_starts_with($logo, $prefix)) {
            return false;
        }

        $ext = strtolower(pathinfo($logo, PATHINFO_EXTENSION));

        return in_array($ext, ['png', 'jpg', 'jpeg', 'gif'], true);
    }

    private function writeConfigValue(string $key, string $value): void
    {
        $tenant_id = $this->getTenantId();
        if ($this->usesTenantConfig() && $tenant_id > 0) {
            $row = $this->db->table('tenant_config')
                ->where('tenant_id', $tenant_id)
                ->where('config_key', $key)
                ->get(1)
                ->getRow();
            if ($row !== null) {
                $this->db->table('tenant_config')
                    ->where('tenant_id', $tenant_id)
                    ->where('config_key', $key)
                    ->update(['config_value' => $value]);
            } else {
                $this->db->table('tenant_config')->insert([
                    'tenant_id'    => $tenant_id,
                    'config_key'   => $key,
                    'config_value' => $value,
                ]);
            }
        }

        if ($this->db->tableExists('app_config')) {
            $exists = $this->db->table('app_config')->where('key', $key)->countAllResults() > 0;
            if ($exists) {
                $this->db->table('app_config')->where('key', $key)->update(['value' => $value]);
            } else {
                $this->db->table('app_config')->insert(['key' => $key, 'value' => $value]);
            }
        }
    }
}
