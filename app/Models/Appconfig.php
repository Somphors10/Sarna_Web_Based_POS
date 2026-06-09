<?php

namespace App\Models;

use App\Models\Concerns\TenantAware;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;
use Config\OSPOS;
use ReflectionException;

/**
 * Appconfig class — per-tenant settings via tenant_config, global defaults via app_config.
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
     * Returns merged settings as associative array (used by OSPOS config).
     */
    public function get_all_assoc(): array
    {
        $settings = [];

        if ($this->db->tableExists('app_config')) {
            foreach ($this->db->table('app_config')->get()->getResult() as $row) {
                $settings[$row->key] = $row->value;
            }
        }

        if ($this->usesTenantConfig()) {
            foreach ($this->db->table('tenant_config')
                ->where('tenant_id', $this->getTenantId())
                ->get()
                ->getResult() as $row) {
                $settings[$row->config_key] = $row->config_value;
            }
        }

        return $settings;
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
            $builder = $this->db->table('tenant_config');
            $exists = $builder->where('tenant_id', $tenant_id)->where('config_key', $key)->countAllResults() > 0;

            if ($exists) {
                $success = $builder->where('tenant_id', $tenant_id)
                    ->where('config_key', $key)
                    ->update(['config_value' => $value]);
            } else {
                $success = $builder->insert([
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

}
