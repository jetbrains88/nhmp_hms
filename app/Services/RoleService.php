<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RoleService
{
    /**
     * Create a new role with permissions
     */
    public function createRole(array $data): Role
    {
        $role = Role::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'branch_id' => $data['branch_id'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->attach($data['permissions']);
        }

        return $role->load('permissions');
    }

    /**
     * Update role permissions
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'] ?? $role->name,
            'display_name' => $data['display_name'] ?? $role->display_name,
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return $role->fresh('permissions');
    }

    /**
     * Clone role with permissions to another branch
     */
    public function cloneRoleToBranch(Role $role, int $branchId): Role
    {
        $newRole = Role::create([
            'uuid' => (string) Str::uuid(),
            'name' => $role->name . '_' . $branchId,
            'display_name' => $role->display_name,
            'branch_id' => $branchId,
        ]);

        $newRole->permissions()->attach($role->permissions->pluck('id'));

        return $newRole->load('permissions');
    }

    /**
     * Get all permissions grouped by module
     */
    public function getPermissionsGrouped()
    {
        return Permission::all()->groupBy('group');
    }
}
