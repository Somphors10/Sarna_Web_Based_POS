<?php

namespace App\Models;

use CodeIgniter\Model;

class Platform_admin extends Model
{
    protected $DBGroup = 'platform';
    protected $table = 'platform_admins';
    protected $primaryKey = 'admin_id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'username',
        'password_hash',
        'full_name',
        'email',
        'status'
    ];

    public function login(string $username, string $password): bool
    {
        $row = $this->db->table('platform_admins')
            ->where('username', $username)
            ->where('status', 'active')
            ->get(1)
            ->getRow();

        if (!$row || !password_verify($password, $row->password_hash)) {
            return false;
        }

        session()->set('platform_admin_id', (int)$row->admin_id);

        return true;
    }

    public function is_logged_in(): bool
    {
        return (int)session()->get('platform_admin_id') > 0;
    }

    public function get_logged_in_admin(): ?object
    {
        $admin_id = (int)session()->get('platform_admin_id');
        if ($admin_id <= 0) {
            return null;
        }

        return $this->db->table('platform_admins')
            ->where('admin_id', $admin_id)
            ->get(1)
            ->getRow();
    }

    public function is_owner(): bool
    {
        $admin = $this->get_logged_in_admin();
        return $admin !== null && $admin->username === 'superadmin';
    }

    public function username_exists(string $username): bool
    {
        return $this->db->table('platform_admins')
            ->where('username', $username)
            ->countAllResults() > 0;
    }

    public function create_admin(string $username, string $password, string $full_name, ?string $email = null): bool
    {
        return $this->db->table('platform_admins')->insert([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $full_name,
            'email' => $email,
            'status' => 'active'
        ]);
    }

    public function set_status(int $admin_id, string $status): bool
    {
        if ($admin_id <= 0 || !in_array($status, ['active', 'disabled'], true)) {
            return false;
        }

        return $this->db->table('platform_admins')
            ->where('admin_id', $admin_id)
            ->update(['status' => $status]);
    }

    public function get_all_admins(): array
    {
        return $this->db->table('platform_admins')
            ->select('admin_id, username, full_name, email, status, created_at')
            ->orderBy('admin_id', 'asc')
            ->get()
            ->getResultArray();
    }

    public function check_password(string $username, string $password): bool
    {
        $row = $this->db->table('platform_admins')
            ->where('username', $username)
            ->where('status', 'active')
            ->get(1)
            ->getRow();

        return $row !== null && password_verify($password, $row->password_hash);
    }

    public function change_password(int $admin_id, string $plain_password): bool
    {
        if ($admin_id <= 0 || $plain_password === '') {
            return false;
        }

        return $this->db->table('platform_admins')
            ->where('admin_id', $admin_id)
            ->update([
                'password_hash' => password_hash($plain_password, PASSWORD_DEFAULT),
            ]);
    }

    public function logout(): void
    {
        session()->remove('platform_admin_id');
    }
}
