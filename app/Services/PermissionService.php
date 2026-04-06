<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionService
{
    protected $permissionCache = [];

    public function check(User $user, string $permission): bool
    {
        // Cache permissions for the request
        $cacheKey = $user->id . '_permissions';
        
        if (!isset($this->permissionCache[$cacheKey])) {
            $this->permissionCache[$cacheKey] = $user->role 
                ? $user->role->permissions->pluck('name')->toArray()
                : [];
        }

        return in_array($permission, $this->permissionCache[$cacheKey]);
    }

    public function syncRolePermissions(Role $role, array $permissions): void
    {
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $role->permissions()->sync($permissionIds);
    }

    public function getGroupedPermissions(): array
    {
        return Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group')
            ->toArray();
    }
}