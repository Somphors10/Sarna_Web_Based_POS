<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\ResultInterface;

/**
 * Supplier class
 */
class Supplier extends Person
{
    protected $table = 'suppliers';
    protected $primaryKey = 'person_id';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_name',
        'account_number',
        'tax_id',
        'deleted',
        'agency_name',
        'category',
        'tenant_id'
    ];

    private function hasSupplierTenantColumn(): bool
    {
        return $this->db->tableExists('suppliers') && $this->db->fieldExists('tenant_id', 'suppliers');
    }

    private function hasPeopleTenantColumn(): bool
    {
        return $this->db->tableExists('people') && $this->db->fieldExists('tenant_id', 'people');
    }

    private function scopeSupplierTenant(BaseBuilder $builder): void
    {
        if ($this->hasPeopleTenantColumn()) {
            $builder->where('people.tenant_id', $this->getTenantId());
            return;
        }

        if ($this->hasSupplierTenantColumn()) {
            $builder->where('tenant_id', $this->getTenantId());
        }
    }

    /**
     * Determines if a given person_id is a customer
     */
    public function exists(int $person_id): bool
    {
        $builder = $this->db->table('suppliers');
        $builder->join('people', 'people.person_id = suppliers.person_id');
        $builder->where('suppliers.person_id', $person_id);
        $this->scopeSupplierTenant($builder);

        return ($builder->get()->getNumRows() == 1);    // TODO: ===
    }

    /**
     * Gets total of rows
     */
    public function get_total_rows(): int
    {
        $builder = $this->db->table('suppliers');
        if ($this->hasPeopleTenantColumn()) {
            $builder->whereIn('person_id', function (BaseBuilder $subquery) {
                $subquery->select('person_id')
                    ->from('people')
                    ->where('tenant_id', $this->getTenantId());
            });
        } elseif ($this->hasSupplierTenantColumn()) {
            $builder->where('tenant_id', $this->getTenantId());
        }
        $builder->where('deleted', 0);

        return $builder->countAllResults();
    }

    /**
     * Returns all the suppliers
     */
    public function get_all(int $limit = 0, int $offset = 0, int $category = GOODS_SUPPLIER): ResultInterface
    {
        $builder = $this->db->table('suppliers');
        $builder->join('people', 'suppliers.person_id = people.person_id');
        $this->scopeSupplierTenant($builder);
        $builder->where('category', $category);
        $builder->where('deleted', 0);
        $builder->orderBy('company_name', 'asc');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get();
    }

    /**
     * Gets information about a particular supplier
     */
    public function get_info(?int $person_id): object
    {
        $builder = $this->db->table('suppliers AS suppliers');
        $builder->join('people', 'people.person_id = suppliers.person_id');
        $builder->select('suppliers.*, people.*');
        if ($this->hasSupplierTenantColumn()) {
            $builder->select('(
                SELECT COUNT(*)
                FROM ' . $this->db->prefixTable('suppliers') . ' AS s2
                WHERE s2.tenant_id = suppliers.tenant_id
                  AND s2.deleted = 0
                  AND s2.person_id <= suppliers.person_id
            ) AS tenant_supplier_seq', false);
        } elseif ($this->hasPeopleTenantColumn()) {
            $builder->select('(
                SELECT COUNT(*)
                FROM ' . $this->db->prefixTable('suppliers') . ' AS s2
                JOIN ' . $this->db->prefixTable('people') . ' AS p2 ON p2.person_id = s2.person_id
                WHERE p2.tenant_id = ' . (int)$this->getTenantId() . '
                  AND s2.deleted = 0
                  AND s2.person_id <= suppliers.person_id
            ) AS tenant_supplier_seq', false);
        }
        $builder->where('suppliers.person_id', $person_id);
        $this->scopeSupplierTenant($builder);
        $query = $builder->get();

        if ($query->getNumRows() == 1) {    // TODO: ===
            return $query->getRow();
        } else {
            // Get empty base parent object, as $supplier_id is NOT a supplier
            $person_obj = parent::get_info(NEW_ENTRY);

            // Get all the fields from supplier table
            // Append those fields to base parent object, we have a complete empty object
            foreach ($this->db->getFieldNames('suppliers') as $field) {
                $person_obj->$field = '';
            }

            return $person_obj;
        }
    }

    /**
     * Gets information about multiple suppliers
     */
    public function get_multiple_info(array $person_ids): ResultInterface
    {
        $builder = $this->db->table('suppliers');
        $builder->join('people', 'people.person_id = suppliers.person_id');
        $builder->whereIn('suppliers.person_id', $person_ids);
        $this->scopeSupplierTenant($builder);
        $builder->orderBy('last_name', 'asc');

        return $builder->get();
    }

    /**
     * Inserts or updates a suppliers
     */
    public function save_supplier(array &$person_data, array &$supplier_data, int $supplier_id = NEW_ENTRY): bool
    {
        $success = false;
        $tenant_id = $this->getTenantId();
        $person_data['tenant_id'] = $tenant_id;
        if ($this->hasSupplierTenantColumn()) {
            $supplier_data['tenant_id'] = $tenant_id;
        }

        // Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->transStart();

        if (parent::save_value($person_data, $supplier_id)) {
            $builder = $this->db->table('suppliers');
            if ($supplier_id == NEW_ENTRY || !$this->exists($supplier_id)) {
                $supplier_data['person_id'] = $person_data['person_id'];
                $success = $builder->insert($supplier_data);
            } else {
                $builder->where('person_id', $supplier_id);
                if ($this->hasSupplierTenantColumn()) {
                    $builder->where('tenant_id', $tenant_id);
                }
                $success = $builder->update($supplier_data);
            }
        }

        $this->db->transComplete();

        $success &= $this->db->transStatus();

        return $success;
    }

    /**
     * Deletes one supplier
     */
    public function delete($supplier_id = null, bool $purge = false): bool
    {
        $builder = $this->db->table('suppliers');
        $builder->where('person_id', $supplier_id);
        if ($this->hasSupplierTenantColumn()) {
            $builder->where('tenant_id', $this->getTenantId());
        } elseif ($this->hasPeopleTenantColumn()) {
            $builder->whereIn('person_id', function (BaseBuilder $subquery) {
                $subquery->select('person_id')
                    ->from('people')
                    ->where('tenant_id', $this->getTenantId());
            });
        }

        $builder->delete();

        return $this->db->affectedRows() > 0;
    }

    /**
     * Deletes a list of suppliers
     */
    public function delete_list(array $person_ids): bool
    {
        $builder = $this->db->table('suppliers');
        $builder->whereIn('person_id', $person_ids);
        if ($this->hasSupplierTenantColumn()) {
            $builder->where('tenant_id', $this->getTenantId());
        } elseif ($this->hasPeopleTenantColumn()) {
            $builder->whereIn('person_id', function (BaseBuilder $subquery) {
                $subquery->select('person_id')
                    ->from('people')
                    ->where('tenant_id', $this->getTenantId());
            });
        }

        $builder->delete();

        return $this->db->affectedRows() > 0;
    }

    /**
     * Get search suggestions to find suppliers
     */
    public function get_search_suggestions(string $search, int $limit = 25, bool $unique = false): array    // TODO: Parent is looking for the 2nd parameter to be an int
    {
        $suggestions = [];

        $builder = $this->db->table('suppliers');
        $builder->join('people', 'suppliers.person_id = people.person_id');
        $this->scopeSupplierTenant($builder);
        $builder->where('suppliers.deleted', 0);
        $builder->like('company_name', $search);
        $builder->orderBy('company_name', 'asc');

        foreach ($builder->get()->getResult() as $row) {
            $suggestions[] = ['value' => $row->person_id, 'label' => $row->company_name];
        }

        $builder = $this->db->table('suppliers');
        $builder->join('people', 'suppliers.person_id = people.person_id');
        $this->scopeSupplierTenant($builder);
        $builder->where('suppliers.deleted', 0);
        $builder->distinct();
        $builder->like('agency_name', $search);
        $builder->where('agency_name IS NOT NULL');
        $builder->orderBy('agency_name', 'asc');

        foreach ($builder->get()->getResult() as $row) {
            $suggestions[] = ['value' => $row->person_id, 'label' => $row->agency_name];
        }

        $builder = $this->db->table('suppliers');
        $builder->join('people', 'suppliers.person_id = people.person_id');
        $this->scopeSupplierTenant($builder);
        $builder->groupStart();
        $builder->like('first_name', $search);
        $builder->orLike('last_name', $search);
        $builder->orLike('CONCAT(first_name, " ", last_name)', $search);
        $builder->groupEnd();
        $builder->where('suppliers.deleted', 0);
        $builder->orderBy('last_name', 'asc');

        foreach ($builder->get()->getResult() as $row) {
            $suggestions[] = ['value' => $row->person_id, 'label' => $row->first_name . ' ' . $row->last_name];
        }

        if (!$unique) {
            $builder = $this->db->table('suppliers');
            $builder->join('people', 'suppliers.person_id = people.person_id');
            $this->scopeSupplierTenant($builder);
            $builder->where('suppliers.deleted', 0);
            $builder->like('email', $search);
            $builder->orderBy('email', 'asc');

            foreach ($builder->get()->getResult() as $row) {
                $suggestions[] = ['value' => $row->person_id, 'label' => $row->email];
            }

            $builder = $this->db->table('suppliers');
            $builder->join('people', 'suppliers.person_id = people.person_id');
            $this->scopeSupplierTenant($builder);
            $builder->where('suppliers.deleted', 0);
            $builder->like('phone_number', $search);
            $builder->orderBy('phone_number', 'asc');

            foreach ($builder->get()->getResult() as $row) {
                $suggestions[] = ['value' => $row->person_id, 'label' => $row->phone_number];
            }

            $builder = $this->db->table('suppliers');
            $builder->join('people', 'suppliers.person_id = people.person_id');
            $this->scopeSupplierTenant($builder);
            $builder->where('suppliers.deleted', 0);
            $builder->like('account_number', $search);
            $builder->orderBy('account_number', 'asc');

            foreach ($builder->get()->getResult() as $row) {
                $suggestions[] = ['value' => $row->person_id, 'label' => $row->account_number];
            }
        }

        // Only return $limit suggestions
        if (count($suggestions) > $limit) {    // TODO: this can be replaced with return count($suggestions) > $limit ? array_slice($suggestions, 0, $limit) : $suggestions
            $suggestions = array_slice($suggestions, 0, $limit);
        }

        return $suggestions;
    }

    /**
     * Gets rows
     */
    public function get_found_rows(string $search): int
    {
        return $this->search($search, 0, 0, 'last_name', 'asc', true);
    }

    /**
     * Perform a search on suppliers
     */
    public function search(string $search, ?int $rows = 25, ?int $limit_from = 0, ?string $sort = 'last_name', ?string $order = 'asc', ?bool $count_only = false)
    {
        // Set default values on null
        $rows = $rows ?? 25;
        $limit_from = $limit_from ?? 0;
        $sort = $sort ?? 'last_name';
        $order = $order ?? 'asc';
        $count_only = $count_only ?? false;

        $builder = $this->db->table('suppliers AS suppliers');
        if ($this->hasSupplierTenantColumn()) {
            $builder->where('suppliers.tenant_id', $this->getTenantId());
        }

        // get_found_rows case
        if ($count_only) {
            $builder->select('COUNT(suppliers.person_id) as count');
        } else {
            $builder->select('suppliers.*, people.*');
            if ($this->hasSupplierTenantColumn()) {
                $builder->select('(
                    SELECT COUNT(*)
                    FROM ' . $this->db->prefixTable('suppliers') . ' AS s2
                    WHERE s2.tenant_id = suppliers.tenant_id
                      AND s2.deleted = 0
                      AND s2.person_id <= suppliers.person_id
                ) AS tenant_supplier_seq', false);
            } elseif ($this->hasPeopleTenantColumn()) {
                $builder->select('(
                    SELECT COUNT(*)
                    FROM ' . $this->db->prefixTable('suppliers') . ' AS s2
                    JOIN ' . $this->db->prefixTable('people') . ' AS p2 ON p2.person_id = s2.person_id
                    WHERE p2.tenant_id = ' . (int)$this->getTenantId() . '
                      AND s2.deleted = 0
                      AND s2.person_id <= suppliers.person_id
                ) AS tenant_supplier_seq', false);
            }
        }

        $builder->join('people', 'suppliers.person_id = people.person_id');
        if (!$this->hasSupplierTenantColumn() && $this->hasPeopleTenantColumn()) {
            $builder->where('people.tenant_id', $this->getTenantId());
        }
        $builder->groupStart();
        $builder->like('first_name', $search);
        $builder->orLike('last_name', $search);
        $builder->orLike('company_name', $search);
        $builder->orLike('agency_name', $search);
        $builder->orLike('email', $search);
        $builder->orLike('phone_number', $search);
        $builder->orLike('account_number', $search);
        $builder->orLike('CONCAT(first_name, " ", last_name)', $search);    // TODO: According to PHPStorm, this line down to the return is repeated in Customer.php and Employee.php... perhaps refactoring a method in a library could be helpful?
        $builder->groupEnd();
        $builder->where('suppliers.deleted', 0);

        if ($count_only) {
            return $builder->get()->getRow()->count;
        }

        $builder->orderBy($sort, $order);

        if ($rows > 0) {
            $builder->limit($rows, $limit_from);
        }

        return $builder->get();
    }

    /**
     * Return supplier categories
     */
    public function get_categories(): array
    {
        return [
            GOODS_SUPPLIER => lang('Suppliers.goods'),
            COST_SUPPLIER => lang('Suppliers.cost')
        ];
    }

    /**
     * Return a category name given its id.
     * @param int $supplier_type Constant representing the type of supplier.
     * @return string Language string for the given supplier type.
     */
    public function get_category_name(int $supplier_type): string
    {
        if ($supplier_type == 0) {
            return lang('Suppliers.goods');
        } else {
            return  lang('Suppliers.cost');
        }
    }
}
