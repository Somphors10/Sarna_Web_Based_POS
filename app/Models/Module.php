<?php

namespace App\Models;

use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;

/**
 * Module class
 */
class Module extends Model
{
    protected $table = 'modules';
    protected $primaryKey = 'module_id';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name_lang_key',
        'desc_lang_key',
        'sort'
    ];

    /**
     * @param string $module_id
     * @return string
     */
    public function get_module_name(string $module_id): string
    {
        $builder = $this->db->table('modules');
        $query = $builder->getWhere(['module_id' => $module_id], 1);

        if ($query->getNumRows() == 1) {    // TODO: ===
            $row = $query->getRow();

            return lang($row->name_lang_key);
        }

        return lang('Errors.unknown');
    }

    /**
     * @param string $module_id
     * @return string
     */
    public function get_module_desc(string $module_id): string    // TODO: This method doesn't seem to be called in the code.  Is it needed?  Also, probably should change the name to get_module_description()
    {
        $builder = $this->db->table('modules');
        $query = $builder->getWhere(['module_id' => $module_id], 1);

        if ($query->getNumRows() == 1) {    // TODO: ===
            $row = $query->getRow();

            return lang($row->desc_lang_key);
        }

        return lang('Errors.unknown');
    }

    /**
     * @return ResultInterface
     */
    public function get_all_permissions(): ResultInterface
    {
        $builder = $this->db->table('permissions');

        return $builder->get();
    }

    /**
     * @return ResultInterface
     */
    public function get_all_subpermissions(): ResultInterface
    {
        $builder = $this->db->table('permissions');
        $builder->join('modules AS modules', 'modules.module_id = permissions.module_id');    // TODO: can the table parameter just be modules instead of modules AS modules?

        // Can't quote the parameters correctly when using different operators
        $builder->where('modules.module_id != ', 'permission_id', false);

        return $builder->get();
    }

    /**
     * @return ResultInterface
     */
    public function get_all_modules(): ResultInterface
    {
        $builder = $this->db->table('modules');
        $builder->orderBy('sort', 'asc');

        return $builder->get();
    }

    /**
     * @param int $person_id
     * @return ResultInterface
     */
    public function get_allowed_home_modules(int $person_id): ResultInterface
    {
        $this->ensure_module_catalog();

        $menus = ['home', 'both'];
        $builder = $this->db->table('modules');    // TODO: this is duplicated with the code below... probably refactor a method and just pass through whether home/office modules are needed.
        $builder->join('permissions', 'permissions.permission_id = modules.module_id');
        $builder->join('grants', 'permissions.permission_id = grants.permission_id');
        $builder->where('person_id', $person_id);
        $builder->whereIn('menu_group', $menus);
        $builder->where('sort !=', 0);
        $builder->orderBy('sort', 'asc');

        return $builder->get();
    }

    /**
     * @param int $person_id
     * @return ResultInterface
     */
    public function get_allowed_office_modules(int $person_id): ResultInterface
    {
        $this->ensure_module_catalog();

        $menus = ['office', 'both'];
        $builder = $this->db->table('modules');    // TODO: Duplicated code
        $builder->join('permissions', 'permissions.permission_id = modules.module_id');
        $builder->join('grants', 'permissions.permission_id = grants.permission_id');
        $builder->where('person_id', $person_id);
        $builder->whereIn('menu_group', $menus);
        $builder->where('sort !=', 0);
        $builder->orderBy('sort', 'asc');

        return $builder->get();
    }

    /**
     * This method is used to set the show the office navigation icon on the home page
     * which happens when the sort value is greater than zero
     */
    public function set_show_office_group(bool $show_office_group): void
    {
        $sort = $show_office_group ? 999 : 0;
        $this->ensure_office_module($sort);

        $builder = $this->db->table('modules');
        $builder->where('module_id', 'office');
        $builder->update(['sort' => $sort]);
    }

    /**
     * This method is used to show the office navigation icon on the home page
     * which happens when the sort value is greater than zero
     */
    public function get_show_office_group(): int
    {
        $this->ensure_office_module();

        $builder = $this->db->table('modules');
        $builder->select('sort');
        $builder->where('module_id', 'office');
        $row = $builder->get()->getRow();

        return $row ? (int)$row->sort : 0;
    }

    private function ensure_office_module(int $sort = 999): void
    {
        $this->ensure_module_catalog();

        if (!$this->db->tableExists('modules')) {
            return;
        }

        $exists = $this->db->table('modules')->where('module_id', 'office')->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db->table('modules')->insert([
            'name_lang_key' => 'module_office',
            'desc_lang_key' => 'module_office_desc',
            'sort' => $sort,
            'module_id' => 'office',
        ]);
    }

    /**
     * Rebuild the POS module/permission catalog if Sync/Deploy wiped it
     * (happens when a shop still points at the platform database).
     */
    public function ensure_module_catalog(): void
    {
        if (!$this->db->tableExists('modules') || !$this->db->tableExists('permissions')) {
            return;
        }

        if ($this->db->table('modules')->countAllResults() > 0
            && $this->db->table('permissions')->countAllResults() > 0
        ) {
            return;
        }

        $modules = [
            ['name_lang_key' => 'module_home', 'desc_lang_key' => 'module_home_desc', 'sort' => 1, 'module_id' => 'home'],
            ['name_lang_key' => 'module_customers', 'desc_lang_key' => 'module_customers_desc', 'sort' => 10, 'module_id' => 'customers'],
            ['name_lang_key' => 'module_items', 'desc_lang_key' => 'module_items_desc', 'sort' => 20, 'module_id' => 'items'],
            ['name_lang_key' => 'module_item_kits', 'desc_lang_key' => 'module_item_kits_desc', 'sort' => 30, 'module_id' => 'item_kits'],
            ['name_lang_key' => 'module_suppliers', 'desc_lang_key' => 'module_suppliers_desc', 'sort' => 40, 'module_id' => 'suppliers'],
            ['name_lang_key' => 'module_reports', 'desc_lang_key' => 'module_reports_desc', 'sort' => 50, 'module_id' => 'reports'],
            ['name_lang_key' => 'module_receivings', 'desc_lang_key' => 'module_receivings_desc', 'sort' => 60, 'module_id' => 'receivings'],
            ['name_lang_key' => 'module_sales', 'desc_lang_key' => 'module_sales_desc', 'sort' => 70, 'module_id' => 'sales'],
            ['name_lang_key' => 'module_employees', 'desc_lang_key' => 'module_employees_desc', 'sort' => 80, 'module_id' => 'employees'],
            ['name_lang_key' => 'module_giftcards', 'desc_lang_key' => 'module_giftcards_desc', 'sort' => 90, 'module_id' => 'giftcards'],
            ['name_lang_key' => 'module_messages', 'desc_lang_key' => 'module_messages_desc', 'sort' => 98, 'module_id' => 'messages'],
            ['name_lang_key' => 'module_taxes', 'desc_lang_key' => 'module_taxes_desc', 'sort' => 105, 'module_id' => 'taxes'],
            ['name_lang_key' => 'module_attributes', 'desc_lang_key' => 'module_attributes_desc', 'sort' => 107, 'module_id' => 'attributes'],
            ['name_lang_key' => 'module_expenses', 'desc_lang_key' => 'module_expenses_desc', 'sort' => 108, 'module_id' => 'expenses'],
            ['name_lang_key' => 'module_expenses_categories', 'desc_lang_key' => 'module_expenses_categories_desc', 'sort' => 109, 'module_id' => 'expenses_categories'],
            ['name_lang_key' => 'module_cashups', 'desc_lang_key' => 'module_cashups_desc', 'sort' => 110, 'module_id' => 'cashups'],
            ['name_lang_key' => 'module_config', 'desc_lang_key' => 'module_config_desc', 'sort' => 900, 'module_id' => 'config'],
            ['name_lang_key' => 'module_office', 'desc_lang_key' => 'module_office_desc', 'sort' => 999, 'module_id' => 'office'],
        ];

        if ($this->db->table('modules')->countAllResults() === 0) {
            $this->db->table('modules')->insertBatch($modules);
        }

        $permissions = [
            ['permission_id' => 'attributes', 'module_id' => 'attributes', 'location_id' => null],
            ['permission_id' => 'cashups', 'module_id' => 'cashups', 'location_id' => null],
            ['permission_id' => 'config', 'module_id' => 'config', 'location_id' => null],
            ['permission_id' => 'customers', 'module_id' => 'customers', 'location_id' => null],
            ['permission_id' => 'employees', 'module_id' => 'employees', 'location_id' => null],
            ['permission_id' => 'expenses', 'module_id' => 'expenses', 'location_id' => null],
            ['permission_id' => 'expenses_categories', 'module_id' => 'expenses_categories', 'location_id' => null],
            ['permission_id' => 'giftcards', 'module_id' => 'giftcards', 'location_id' => null],
            ['permission_id' => 'home', 'module_id' => 'home', 'location_id' => null],
            ['permission_id' => 'items', 'module_id' => 'items', 'location_id' => null],
            ['permission_id' => 'items_stock', 'module_id' => 'items', 'location_id' => 1],
            ['permission_id' => 'item_kits', 'module_id' => 'item_kits', 'location_id' => null],
            ['permission_id' => 'messages', 'module_id' => 'messages', 'location_id' => null],
            ['permission_id' => 'office', 'module_id' => 'office', 'location_id' => null],
            ['permission_id' => 'receivings', 'module_id' => 'receivings', 'location_id' => null],
            ['permission_id' => 'receivings_stock', 'module_id' => 'receivings', 'location_id' => 1],
            ['permission_id' => 'reports', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_categories', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_customers', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_discounts', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_employees', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_expenses_categories', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_inventory', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_items', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_payments', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_receivings', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_sales', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_sales_taxes', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_suppliers', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'reports_taxes', 'module_id' => 'reports', 'location_id' => null],
            ['permission_id' => 'sales', 'module_id' => 'sales', 'location_id' => null],
            ['permission_id' => 'sales_change_price', 'module_id' => 'sales', 'location_id' => null],
            ['permission_id' => 'sales_delete', 'module_id' => 'sales', 'location_id' => null],
            ['permission_id' => 'sales_stock', 'module_id' => 'sales', 'location_id' => 1],
            ['permission_id' => 'suppliers', 'module_id' => 'suppliers', 'location_id' => null],
            ['permission_id' => 'taxes', 'module_id' => 'taxes', 'location_id' => null],
        ];

        if ($this->db->table('permissions')->countAllResults() === 0) {
            $this->db->table('permissions')->insertBatch($permissions);
        }
    }
}
