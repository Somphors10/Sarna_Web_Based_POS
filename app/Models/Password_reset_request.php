<?php

namespace App\Models;

use App\Libraries\TenantContext;
use CodeIgniter\Model;
use Throwable;

class Password_reset_request extends Model
{
    protected $DBGroup = 'platform';
    protected $table = 'password_reset_requests';
    protected $primaryKey = 'request_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'tenant_code',
        'username',
        'person_id',
        'tenant_id',
        'reset_token',
        'email',
        'expires_at',
        'used_at',
        'new_password_hash',
        'status',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    public function table_ready(): bool
    {
        return $this->db->tableExists('password_reset_requests')
            && $this->db->fieldExists('reset_token', 'password_reset_requests');
    }

    /**
     * @return object|null Row with tenant_code, username, person_id, tenant_id, email, reset_token, expires_at
     */
    public function find_valid_token(string $token): ?object
    {
        if ($token === '' || !$this->table_ready()) {
            return null;
        }

        $row = $this->db->table('password_reset_requests')
            ->where('reset_token', $token)
            ->where('status', 'pending')
            ->where('used_at', null)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get(1)
            ->getRow();

        return $row ?: null;
    }

    public function invalidate_pending(int $tenant_id, string $username): void
    {
        if (!$this->table_ready()) {
            return;
        }

        $this->db->table('password_reset_requests')
            ->where('tenant_id', $tenant_id)
            ->where('username', $username)
            ->where('status', 'pending')
            ->update([
                'status' => 'expired',
                'used_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Create a one-hour reset token and return the raw token string.
     */
    public function create_token(object $employee, string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $tenant_id = (int)($employee->tenant_id ?? 0);
        $username = (string)($employee->username ?? '');

        $this->invalidate_pending($tenant_id, $username);

        $this->insert([
            'tenant_code' => (string)($employee->tenant_code ?? ''),
            'username' => $username,
            'person_id' => (int)($employee->person_id ?? 0),
            'tenant_id' => $tenant_id,
            'reset_token' => $token,
            'email' => strtolower(trim($email)),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            'status' => 'pending',
            'new_password_hash' => null,
        ]);

        return $token;
    }

    public function mark_used(int $request_id): void
    {
        $this->db->table('password_reset_requests')
            ->where('request_id', $request_id)
            ->update([
                'status' => 'used',
                'used_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Resolve tenant + employee by company code and username.
     */
    public function resolve_employee(string $tenant_code, string $username): ?object
    {
        $db = db_connect('platform');

        $tenant = $db->table('tenants')
            ->select('tenant_id, company_name, tenant_code, status, db_name')
            ->where('tenant_code', $tenant_code)
            ->get(1)
            ->getRow();

        if ($tenant === null || (string)($tenant->status ?? '') === 'cancelled') {
            return null;
        }

        if ($db->tableExists('tenant_logins')) {
            $login = $db->table('tenant_logins')
                ->where('tenant_id', (int)$tenant->tenant_id)
                ->where('LOWER(username) = ' . $db->escape(strtolower($username)), null, false)
                ->where('is_active', 1)
                ->get(1);
            $login = $this->result_row($login, $db);
            if ($login) {
                $email = $this->lookup_person_email((int)$tenant->tenant_id, (int)$login->person_id);

                return (object)[
                    'person_id' => (int)$login->person_id,
                    'username' => (string)$login->username,
                    'tenant_id' => (int)$tenant->tenant_id,
                    'first_name' => '',
                    'last_name' => (string)($login->display_name ?? ''),
                    'email' => $email,
                    'tenant_code' => $tenant->tenant_code,
                    'company_name' => $tenant->company_name,
                ];
            }
        }

        $context = new TenantContext();
        $context->applyRuntimeConnection((int)$tenant->tenant_id);
        try {
            $employee = $this->find_employee_in_tenant(
                (int)$tenant->tenant_id,
                $username,
                null
            );
        } finally {
            $context->restoreSharedConnection();
        }

        if ($employee === null) {
            return null;
        }

        $employee->tenant_code = $tenant->tenant_code;
        $employee->company_name = $tenant->company_name;

        return $employee;
    }

    /**
     * Find the first active employee account with this email address.
     */
    public function resolve_employee_by_email(string $email): ?object
    {
        $email = strtolower(trim($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $employee = $this->resolve_from_registration_email($email);
        if ($employee !== null) {
            return $employee;
        }

        if (filter_var(env('tenant.forceShared', false), FILTER_VALIDATE_BOOLEAN)) {
            $employee = $this->resolve_employee_by_email_shared($email);
            if ($employee !== null) {
                return $employee;
            }
        }

        $db = db_connect('platform');
        $tenants = $db->table('tenants')
            ->select('tenant_id, company_name, tenant_code, status')
            ->where('status !=', 'cancelled')
            ->orderBy('tenant_id', 'asc')
            ->get()
            ->getResult();

        $context = new TenantContext();

        foreach ($tenants as $tenant) {
            try {
                $context->applyRuntimeConnection((int)$tenant->tenant_id);
                $employee = $this->find_employee_in_tenant(
                    (int)$tenant->tenant_id,
                    null,
                    $email
                );
            } catch (Throwable $e) {
                log_message('error', 'Password reset email lookup failed for tenant '
                    . (int)$tenant->tenant_id . ': ' . $e->getMessage());
                $employee = null;
            } finally {
                $context->restoreSharedConnection();
            }

            if ($employee !== null) {
                $employee->tenant_code = $tenant->tenant_code;
                $employee->company_name = $tenant->company_name;

                return $employee;
            }
        }

        return null;
    }

    /**
     * Look up an active employee in a tenant database.
     */
    private function find_employee_in_tenant(int $tenant_id, ?string $username, ?string $email): ?object
    {
        if ($tenant_id <= 0) {
            return null;
        }

        $app = db_connect();
        $builder = $app->table('employees')
            ->select('employees.person_id, employees.username, employees.tenant_id, people.first_name, people.last_name, people.email')
            ->join('people', 'people.person_id = employees.person_id')
            ->where('employees.tenant_id', $tenant_id);

        if ($app->fieldExists('tenant_id', 'people')) {
            $builder->where('people.tenant_id', $tenant_id);
        }

        if ($app->fieldExists('deleted', 'employees')) {
            $builder->where('employees.deleted', 0);
        }

        if ($username !== null && $username !== '') {
            $builder->where('employees.username', $username);
        }

        if ($email !== null && $email !== '') {
            $this->where_lower_equals($builder, $app, 'people.email', $email);
        }

        $employee = $this->result_row($builder->get(1), $app);

        return $employee ?: null;
    }

    /**
     * Match the email used at shop registration (owner_email on subscription_requests).
     */
    private function resolve_from_registration_email(string $email): ?object
    {
        $db = db_connect('platform');
        if (!$db->tableExists('subscription_requests') || !$db->fieldExists('owner_email', 'subscription_requests')) {
            return null;
        }

        $request = $db->table('subscription_requests')
            ->select('tenant_code, owner_username, owner_email, company_name')
            ->where('LOWER(owner_email) = ' . $db->escape($email), null, false)
            ->orderBy('request_id', 'DESC')
            ->get(1);
        $request = $this->result_row($request, $db);

        if ($request === null || trim((string)($request->tenant_code ?? '')) === '') {
            return null;
        }

        $tenant = $db->table('tenants')
            ->select('tenant_id, company_name, tenant_code, status')
            ->where('tenant_code', (string)$request->tenant_code)
            ->where('status !=', 'cancelled')
            ->get(1);
        $tenant = $this->result_row($tenant, $db);

        if ($tenant === null) {
            return null;
        }

        $employee = $this->build_employee_from_registration($request, $tenant, $email);
        if ($employee === null) {
            $employee = $this->resolve_employee((string)$request->tenant_code, (string)$request->owner_username);
        }

        if ($employee === null) {
            return null;
        }

        $employee->email = $email;

        $employee->tenant_code = $tenant->tenant_code;
        $employee->company_name = $tenant->company_name ?: (string)$request->company_name;

        return $employee;
    }

    /**
     * Build a reset target from registration data when employee row lookup fails.
     */
    private function build_employee_from_registration(object $request, object $tenant, string $email): ?object
    {
        $db = db_connect('platform');
        $tenant_id = (int)$tenant->tenant_id;
        $username = trim((string)$request->owner_username);
        $person_id = 0;

        if ($username !== '' && $db->tableExists('tenant_logins')) {
            $login = $db->table('tenant_logins')
                ->where('tenant_id', $tenant_id)
                ->where('LOWER(username) = ' . $db->escape(strtolower($username)), null, false)
                ->get(1);
            $login = $this->result_row($login, $db);
            if ($login !== null) {
                $person_id = (int)$login->person_id;
                $username = (string)$login->username;
            }
        }

        $context = new TenantContext();
        try {
            $context->applyRuntimeConnection($tenant_id);

            if ($person_id <= 0 && $username !== '') {
                $app = db_connect();
                $row = $app->table('employees')
                    ->select('person_id, username')
                    ->where('tenant_id', $tenant_id)
                    ->where('LOWER(username) = ' . $app->escape(strtolower($username)), null, false)
                    ->get(1);
                $row = $this->result_row($row, $app);
                if ($row !== null) {
                    $person_id = (int)$row->person_id;
                    $username = (string)$row->username;
                }
            }

            if ($person_id <= 0) {
                $match = $this->find_employee_in_tenant($tenant_id, null, $email);
                if ($match !== null) {
                    $match->tenant_code = (string)$tenant->tenant_code;
                    $match->company_name = (string)($tenant->company_name ?: $request->company_name);
                    $match->email = $email;

                    return $match;
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'Password reset registration fallback failed: ' . $e->getMessage());
        } finally {
            $context->restoreSharedConnection();
        }

        if ($person_id <= 0 || $username === '') {
            return null;
        }

        return (object)[
            'person_id' => $person_id,
            'username' => $username,
            'tenant_id' => $tenant_id,
            'first_name' => '',
            'last_name' => '',
            'email' => $email,
            'tenant_code' => (string)$tenant->tenant_code,
            'company_name' => (string)($tenant->company_name ?: $request->company_name),
        ];
    }

    /**
     * Shared-database hosting: one query across tenants (no per-tenant DB switch).
     */
    private function resolve_employee_by_email_shared(string $email): ?object
    {
        $db = db_connect('platform');
        if (!$db->tableExists('employees') || !$db->tableExists('people') || !$db->tableExists('tenants')) {
            return null;
        }

        $people_join = 'people.person_id = employees.person_id';
        if ($db->fieldExists('tenant_id', 'people')) {
            $people_join .= ' AND people.tenant_id = employees.tenant_id';
        }

        $builder = $db->table('employees')
            ->select('employees.person_id, employees.username, employees.tenant_id, people.first_name, people.last_name, people.email, tenants.tenant_code, tenants.company_name')
            ->join('people', $people_join)
            ->join('tenants', 'tenants.tenant_id = employees.tenant_id')
            ->where('tenants.status !=', 'cancelled');
        $this->where_lower_equals($builder, $db, 'people.email', $email);

        if ($db->fieldExists('deleted', 'employees')) {
            $builder->where('employees.deleted', 0);
        }

        $employee = $this->result_row($builder->get(1), $db);

        return $employee ?: null;
    }

    private function lookup_person_email(int $tenant_id, int $person_id): string
    {
        if ($person_id <= 0) {
            return '';
        }

        $context = new TenantContext();
        $context->applyRuntimeConnection($tenant_id);
        $row = db_connect()->table('people')
            ->select('email')
            ->where('person_id', $person_id)
            ->get(1)
            ->getRow();
        $context->restoreSharedConnection();

        return strtolower(trim((string)($row->email ?? '')));
    }

    private function where_lower_equals($builder, $db, string $column, string $value): void
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return;
        }

        $builder->where('LOWER(' . $column . ') = ' . $db->escape($value), null, false);
    }

    /**
     * @param mixed $result
     * @return object|null
     */
    private function result_row($result, $db): ?object
    {
        if ($result === false) {
            $error = $db->error();
            log_message('error', 'Password reset query failed: ' . json_encode($error));

            return null;
        }

        $row = $result->getRow();

        return $row ?: null;
    }
}
