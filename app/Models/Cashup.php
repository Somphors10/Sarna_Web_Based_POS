<?php

namespace App\Models;

use App\Models\Concerns\TenantAware;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;
use Config\OSPOS;
use stdClass;

/**
 * Cashup class
 * Cashups are used to report actual cash on hand, expenses and transactions at the end of a period.
 */
class Cashup extends Model
{
    use TenantAware;

    protected $table = 'cash_up';
    protected $primaryKey = 'cashup_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'open_date',
        'close_date',
        'open_cash_amount',
        'transfer_cash_amount',
        'note',
        'closed_amount_cash',
        'closed_amount_card',
        'closed_amount_check',
        'closed_amount_total',
        'description',
        'open_employee_id',
        'close_employee_id',
        'deleted',
        'closed_amount_due',
        'tenant_id'
    ];

    private function hasCashupTenantColumn(): bool
    {
        return $this->db->tableExists('cash_up') && $this->db->fieldExists('tenant_id', 'cash_up');
    }

    private function hasPeopleTenantColumn(): bool
    {
        return $this->db->tableExists('people') && $this->db->fieldExists('tenant_id', 'people');
    }

    /**
     * Scope by tenant and include legacy cashups with tenant_id=0
     * mapped to employees of the current tenant.
     */
    private function scopeCashupByTenant(BaseBuilder $builder, string $tenantColumn = 'tenant_id', string $openEmployeeColumn = 'open_employee_id'): void
    {
        if (!$this->hasCashupTenantColumn()) {
            return;
        }

        $tenant_id = $this->getTenantId();

        if (!$this->hasPeopleTenantColumn()) {
            $builder->where($tenantColumn, $tenant_id);
            return;
        }

        $builder->groupStart();
        $builder->where($tenantColumn, $tenant_id);
        $builder->orGroupStart();
        $builder->where($tenantColumn, 0);
        $builder->whereIn($openEmployeeColumn, function (BaseBuilder $subquery) use ($tenant_id) {
            $subquery->select('person_id')
                ->from('people')
                ->where('tenant_id', $tenant_id);
        });
        $builder->groupEnd();
        $builder->groupEnd();
    }

    private function tenantCashupSequenceSelect(): string
    {
        $tenant_id = (int)$this->getTenantId();
        $cashup_table = $this->db->prefixTable('cash_up');
        $people_table = $this->db->prefixTable('people');

        return '(
            SELECT COUNT(*)
            FROM ' . $cashup_table . ' AS c2
            WHERE (
                    c2.tenant_id = ' . $tenant_id . '
                    OR (
                        c2.tenant_id = 0
                        AND c2.open_employee_id IN (
                            SELECT p2.person_id
                            FROM ' . $people_table . ' AS p2
                            WHERE p2.tenant_id = ' . $tenant_id . '
                        )
                    )
                )
                AND c2.deleted = 0
                AND c2.cashup_id <= cash_up.cashup_id
        ) AS tenant_cashup_seq';
    }

    /**
     * Determines if a given Cashup_id is a Cashup
     */
    public function exists(int $cashup_id): bool
    {
        $builder = $this->db->table('cash_up');
        $builder->where('cashup_id', $cashup_id);
        $this->scopeCashupByTenant($builder, 'tenant_id', 'open_employee_id');

        return ($builder->get()->getNumRows() == 1);    // TODO: ===
    }

    /**
     * Gets employee info
     */
    public function get_employee(int $cashup_id): object    // TODO: This function is never called and if it were called, would not yield proper results.  There is no employee_id field in the cash_up table.
    {
        $builder = $this->db->table('cash_up');
        $builder->where('cashup_id', $cashup_id);
        $this->scopeCashupByTenant($builder, 'tenant_id', 'open_employee_id');

        $employee = model(Employee::class);

        return $employee->get_info($builder->get()->getRow()->employee_id);
    }

    /**
     * @param string $cashup_ids
     * @return ResultInterface
     */
    public function get_multiple_info(string $cashup_ids): ResultInterface
    {
        $builder = $this->db->table('cash_up');
        $builder->whereIn('cashup_id', $cashup_ids);
        $this->scopeCashupByTenant($builder, 'tenant_id', 'open_employee_id');
        $builder->orderBy('cashup_id', 'asc');

        return $builder->get();
    }

    /**
     * Gets rows
     */
    public function get_found_rows(string $search, array $filters): int
    {
        return $this->search($search, $filters, 0, 0, 'cashup_id', 'asc', true);
    }

    /**
     * Searches cashups
     */
    public function search(string $search, array $filters, ?int $rows = 0, ?int $limit_from = 0, ?string $sort = 'cashup_id', ?string $order = 'asc', ?bool $count_only = false)
    {
        // Set default values
        if ($rows == null) $rows = 0;
        if ($limit_from == null) $limit_from = 0;
        if ($sort == null) $sort = 'cashup_id';
        if ($order == null) $order = 'asc';
        if ($count_only == null) $count_only = false;

        $config = config(OSPOS::class)->settings;
        $builder = $this->db->table('cash_up AS cash_up');
        $this->scopeCashupByTenant($builder, 'cash_up.tenant_id', 'cash_up.open_employee_id');

        // get_found_rows case
        if ($count_only) {
            $builder->select('COUNT(cash_up.cashup_id) as count');
        } else {
            $builder->select('
            cash_up.cashup_id,
            MAX(cash_up.open_date) AS open_date,
            MAX(cash_up.close_date) AS close_date,
            MAX(cash_up.open_amount_cash) AS open_amount_cash,
            MAX(cash_up.transfer_amount_cash) AS transfer_amount_cash,
            MAX(cash_up.closed_amount_cash) AS closed_amount_cash,
            MAX(cash_up.closed_amount_due) AS closed_amount_due,
            MAX(cash_up.closed_amount_card) AS closed_amount_card,
            MAX(cash_up.closed_amount_check) AS closed_amount_check,
            MAX(cash_up.closed_amount_total) AS closed_amount_total,
            MAX(cash_up.description) AS description,
            MAX(cash_up.note) AS note,
            MAX(cash_up.open_employee_id) AS open_employee_id,
            MAX(cash_up.close_employee_id) AS close_employee_id,
            MAX(open_employees.first_name) AS open_first_name,
            MAX(open_employees.last_name) AS open_last_name,
            MAX(close_employees.first_name) AS close_first_name,
            MAX(close_employees.last_name) AS close_last_name
        ');
            if ($this->hasCashupTenantColumn()) {
                $builder->select($this->tenantCashupSequenceSelect(), false);
            }
        }

        $builder->join('people AS open_employees', 'open_employees.person_id = cash_up.open_employee_id', 'LEFT');
        $builder->join('people AS close_employees', 'close_employees.person_id = cash_up.close_employee_id', 'LEFT');

        $builder->groupStart();
        $builder->like('cash_up.open_date', $search);
        $builder->orLike('open_employees.first_name', $search);
        $builder->orLike('open_employees.last_name', $search);
        $builder->orLike('close_employees.first_name', $search);
        $builder->orLike('close_employees.last_name', $search);
        $builder->orLike('cash_up.closed_amount_total', $search);
        $builder->orLike('CONCAT(open_employees.first_name, " ", open_employees.last_name)', $search);
        $builder->orLike('CONCAT(close_employees.first_name, " ", close_employees.last_name)', $search);
        $builder->groupEnd();

        $builder->where('cash_up.deleted', $filters['is_deleted']);

        if (empty($config['date_or_time_format'])) {    // TODO: convert this to ternary notation.
            $builder->where('DATE_FORMAT(cash_up.open_date, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
        } else {
            $builder->where('cash_up.open_date BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
        }

        // get_found_rows case
        if ($count_only) {
            return $builder->get()->getRow()->count;
        } else {
            $builder->groupBy('cashup_id');
        }

        $builder->orderBy($sort, $order);

        if ($rows > 0) {
            $builder->limit($rows, $limit_from);
        }

        return $builder->get();
    }

    /**
     * Gets information about a particular cashup
     */
    public function get_info(int $cashup_id): object
    {
        $builder = $this->db->table('cash_up AS cash_up');
        $builder->select('
            cash_up.cashup_id AS cashup_id,
            cash_up.open_date AS open_date,
            cash_up.close_date AS close_date,
            cash_up.open_amount_cash AS open_amount_cash,
            cash_up.transfer_amount_cash AS transfer_amount_cash,
            cash_up.closed_amount_cash AS closed_amount_cash,
            cash_up.closed_amount_due AS closed_amount_due,
            cash_up.closed_amount_card AS closed_amount_card,
            cash_up.closed_amount_check AS closed_amount_check,
            cash_up.closed_amount_total AS closed_amount_total,
            cash_up.description AS description,
            cash_up.note AS note,
            cash_up.open_employee_id AS open_employee_id,
            cash_up.close_employee_id AS close_employee_id,
            cash_up.deleted AS deleted,
            open_employees.first_name AS open_first_name,
            open_employees.last_name AS open_last_name,
            close_employees.first_name AS close_first_name,
            close_employees.last_name AS close_last_name
        ');
        if ($this->hasCashupTenantColumn()) {
            $builder->select($this->tenantCashupSequenceSelect(), false);
        }
        $builder->join('people AS open_employees', 'open_employees.person_id = cash_up.open_employee_id', 'LEFT');
        $builder->join('people AS close_employees', 'close_employees.person_id = cash_up.close_employee_id', 'LEFT');
        $builder->where('cashup_id', $cashup_id);
        $this->scopeCashupByTenant($builder, 'cash_up.tenant_id', 'cash_up.open_employee_id');

        $query = $builder->get();
        if ($query->getNumRows() == 1) {    // TODO: ===
            return $query->getRow();
        } else {
            return $this->getEmptyObject('cash_up');
        }
    }

    /**
     * Initializes an empty object based on database definitions
     * @param string $table_name
     * @return object
     */
    private function getEmptyObject(string $table_name): object
    {
        // Return an empty base parent object, as $item_id is NOT an item
        $empty_obj = new stdClass();

        // Iterate through field definitions to determine how the fields should be initialized
        foreach ($this->db->getFieldData($table_name) as $field) {
            $field_name = $field->name;

            if (in_array($field->type, ['int', 'tinyint', 'decimal'])) {
                $empty_obj->$field_name = ($field->primary_key == 1) ? NEW_ENTRY : 0;
            } else {
                $empty_obj->$field_name = null;
            }
        }

        return $empty_obj;
    }


    /**
     * Inserts or updates a cashup
     */
    public function save_value(array &$cash_up_data, $cashup_id = NEW_ENTRY): bool
    {
        $cash_up_data['tenant_id'] = $this->getTenantId();

        if (!$cashup_id == NEW_ENTRY || !$this->exists($cashup_id)) {
            $builder = $this->db->table('cash_up');
            if ($builder->insert($cash_up_data)) {
                $cash_up_data['cashup_id'] = $this->db->insertID();

                return true;
            }

            return false;
        }

        $builder = $this->db->table('cash_up');
        $builder->where('cashup_id', $cashup_id);
        $this->scopeCashupByTenant($builder, 'tenant_id', 'open_employee_id');

        return $builder->update($cash_up_data);
    }

    /**
     * Deletes a list of cashups
     */
    public function delete_list(array $cashup_ids): bool
    {
        // Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->transStart();
        $builder = $this->db->table('cash_up');
        $builder->whereIn('cashup_id', $cashup_ids);
        $this->scopeCashupByTenant($builder, 'tenant_id', 'open_employee_id');
        $success = $builder->update(['deleted' => 1]);
        $this->db->transComplete();

        return $success;
    }
}
