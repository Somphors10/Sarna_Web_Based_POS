<?php

namespace App\Models;

use App\Models\Concerns\TenantAware;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;

/**
 * Inventory class
 *
 * @property employee employee
 *
 */
class Inventory extends Model
{
    use TenantAware;

    protected $table = 'inventory';
    protected $primaryKey = 'trans_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'trans_items',
        'trans_user',
        'trans_date',
        'trans_comment',
        'trans_inventory',
        'trans_location',
        'tenant_id'
    ];

    public function insert($data = null, bool $returnID = true): bool|int|string
    {
        if (is_array($data) && !array_key_exists('tenant_id', $data)) {
            $data['tenant_id'] = $this->getTenantId();
        }

        return parent::insert($data, $returnID);
    }

    /**
     * @param $comment
     * @param $inventory_data
     * @return bool
     */
    public function update($comment = null, $inventory_data = null): bool
    {
        $builder = $this->db->table('inventory');
        $this->scopeTenant($builder, 'inventory.tenant_id');
        $builder->where('trans_comment', $comment);

        return $builder->update($inventory_data);
    }

    /**
     * Retrieves inventory data given an item_id.
     *
     * @param int $item_id
     * @param bool $location_id
     * @return ResultInterface
     */
    public function get_inventory_data_for_item(int $item_id, bool $location_id = false): ResultInterface
    {
        $builder = $this->db->table('inventory');
        $this->scopeTenant($builder, 'inventory.tenant_id');
        $builder->where('trans_items', $item_id);

        if ($location_id) {
            $builder->where('trans_location', $location_id);
        }

        $builder->orderBy('trans_date', 'desc');

        return $builder->get();
    }

    /**
     * @param int $item_id ID number for the item to have quantity reset.
     * @return bool|int|string The row id of the inventory table on insert or false on failure
     */
    public function reset_quantity(int $item_id): bool|int|string
    {
        $inventory_sums = $this->get_inventory_sum($item_id);
        foreach ($inventory_sums as $inventory_sum) {
            if ($inventory_sum['sum'] > 0) {
                $employee = model(Employee::class);

                return $this->insert([
                    'trans_inventory' => -1 * $inventory_sum['sum'],
                    'trans_items'     => $item_id,
                    'trans_location'  => $inventory_sum['location_id'],
                    'trans_comment'   => lang('Items.is_deleted'),
                    'trans_user'      => $employee->get_logged_in_employee_info()->person_id,
                    'tenant_id'       => $this->getTenantId()
                ]);
            }
        }

        return true;
    }

    /**
     * Reverse legacy soft-delete stock wipes (when delete zeroed quantity).
     * No-op if stock was preserved on delete or already non-zero.
     */
    public function restore_quantity_after_undelete(int $item_id): bool
    {
        $delete_comments = array_values(array_unique(array_filter([
            lang('Items.is_deleted'),
            'Deleted',
            'បានលុប',
            'Item is deleted',
            'Item dihapus',
        ], static fn($comment) => $comment !== '' && $comment !== 'Items.is_deleted')));

        if ($delete_comments === []) {
            return true;
        }

        $builder = $this->db->table('inventory');
        $this->scopeTenant($builder, 'inventory.tenant_id');
        $builder->select('trans_location, SUM(trans_inventory) AS sum_deleted');
        $builder->where('trans_items', $item_id);
        $builder->whereIn('trans_comment', $delete_comments);
        $builder->groupBy('trans_location');

        $rows = $builder->get()->getResultArray();
        if ($rows === []) {
            return true;
        }

        $item_quantity = model(Item_quantity::class);
        $employee = model(Employee::class);
        $employee_id = $employee->get_logged_in_employee_info()->person_id;
        $restore_comment = lang('Items.successful_restored');

        foreach ($rows as $row) {
            $sum_deleted = (float) ($row['sum_deleted'] ?? 0);
            // Delete adjustments are negative; reverse with a positive inventory row.
            if ($sum_deleted >= 0) {
                continue;
            }

            $location_id = (int) $row['trans_location'];
            $restore_qty = -1 * $sum_deleted;
            $current = $item_quantity->get_item_quantity($item_id, $location_id);

            // Stock already present (new soft-delete keeps qty) — leave as-is.
            if ((float) $current->quantity !== 0.0) {
                continue;
            }

            $inserted = $this->insert([
                'trans_inventory' => $restore_qty,
                'trans_items'     => $item_id,
                'trans_location'  => $location_id,
                'trans_comment'   => $restore_comment,
                'trans_user'      => $employee_id,
                'tenant_id'       => $this->getTenantId(),
            ]);

            if ($inserted === false) {
                return false;
            }

            if (!$item_quantity->save_value([
                'item_id'     => $item_id,
                'location_id' => $location_id,
                'quantity'    => $restore_qty,
            ], $item_id, $location_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $item_id
     * @return array
     */
    public function get_inventory_sum(int $item_id): array
    {
        $builder = $this->db->table('inventory');
        $this->scopeTenant($builder, 'inventory.tenant_id');
        $builder->select('SUM(trans_inventory) AS sum, MAX(trans_location) AS location_id');
        $builder->where('trans_items', $item_id);
        $builder->groupBy('trans_location');

        return $builder->get()->getResultArray();
    }
}
