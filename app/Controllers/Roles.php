<?php

namespace App\Controllers;

class Roles extends Secure_Controller
{
    public function __construct()
    {
        parent::__construct('roles');
        helper('rbac');
        rbac_ensure();
    }

    public function getIndex(): void
    {
        $edit_permission = $this->editPermission();
        $edit_role = $this->editRole();
        $tab = strtolower((string)$this->request->getGet('tab'));
        if ($edit_role !== null) {
            $tab = 'roles';
        } elseif ($edit_permission !== null) {
            $tab = 'permissions';
        } elseif (!in_array($tab, ['permissions', 'roles'], true)) {
            $tab = 'roles';
        }

        $new = strtolower((string)$this->request->getGet('new'));

        echo view('roles/manage', [
            'controller_name'      => 'roles',
            'permissions'          => rbac_permissions(),
            'roles'                => rbac_roles(),
            'role_perm_map'        => $this->rolePermissionMap(),
            'edit_permission'      => $edit_permission,
            'edit_role'            => $edit_role,
            'active_tab'           => $tab,
            'show_permission_form' => $edit_permission !== null || $new === 'permission',
            'show_role_form'       => $edit_role !== null || $new === 'role',
            'message'              => (string)session()->getFlashdata('rbac_message'),
            'error'                => (string)session()->getFlashdata('rbac_error'),
        ]);
    }

    public function postSavePermission(): \CodeIgniter\HTTP\RedirectResponse
    {
        $result = rbac_save_permission(
            (string)$this->request->getPost('permission_code'),
            (string)$this->request->getPost('permission_name'),
            (string)$this->request->getPost('description'),
            (int)$this->request->getPost('permission_id')
        );

        return redirect()->to('roles?tab=permissions')->with($result['ok'] ? 'rbac_message' : 'rbac_error', $result['message']);
    }

    public function postDeletePermission(): \CodeIgniter\HTTP\RedirectResponse
    {
        $result = rbac_delete_permission((int)$this->request->getPost('permission_id'));

        return redirect()->to('roles?tab=permissions')->with($result['ok'] ? 'rbac_message' : 'rbac_error', $result['message']);
    }

    public function postSaveRole(): \CodeIgniter\HTTP\RedirectResponse
    {
        $result = rbac_save_role(
            (string)$this->request->getPost('role_name'),
            (string)$this->request->getPost('description'),
            (array)$this->request->getPost('permission_ids'),
            (int)$this->request->getPost('role_id')
        );

        return redirect()->to('roles?tab=roles')->with($result['ok'] ? 'rbac_message' : 'rbac_error', $result['message']);
    }

    public function postDeleteRole(): \CodeIgniter\HTTP\RedirectResponse
    {
        $result = rbac_delete_role((int)$this->request->getPost('role_id'));

        return redirect()->to('roles?tab=roles')->with($result['ok'] ? 'rbac_message' : 'rbac_error', $result['message']);
    }

    private function rolePermissionMap(): array
    {
        $map = [];
        foreach (rbac_roles() as $role) {
            $map[(int)$role['role_id']] = rbac_role_permission_ids((int)$role['role_id']);
        }

        return $map;
    }

    private function editPermission(): ?array
    {
        $id = (int)$this->request->getGet('edit_permission');
        if ($id <= 0) {
            return null;
        }
        foreach (rbac_permissions() as $row) {
            if ((int)$row['permission_id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    private function editRole(): ?array
    {
        $id = (int)$this->request->getGet('edit_role');
        if ($id <= 0) {
            return null;
        }
        foreach (rbac_roles() as $row) {
            if ((int)$row['role_id'] === $id) {
                $row['permission_ids'] = rbac_role_permission_ids($id);

                return $row;
            }
        }

        return null;
    }
}
