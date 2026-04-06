<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create a new user with roles and branch assignments
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Assign roles
            if (!empty($data['roles'])) {
                $user->roles()->attach($data['roles']);
            }

            // Assign branches
            if (!empty($data['branches'])) {
                $branchData = [];
                foreach ($data['branches'] as $branchId) {
                    $branchData[$branchId] = ['is_primary' => ($branchId == ($data['primary_branch'] ?? null))];
                }
                $user->branches()->attach($branchData);
            }

            return $user->load(['roles', 'branches']);
        });
    }

    /**
     * Update an existing user
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Update basic info
            $updateData = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'is_active' => $data['is_active'] ?? $user->is_active,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Sync roles
            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            // Sync branches
            if (isset($data['branches'])) {
                $branchData = [];
                foreach ($data['branches'] as $branchId) {
                    $branchData[$branchId] = ['is_primary' => ($branchId == ($data['primary_branch'] ?? null))];
                }
                $user->branches()->sync($branchData);
            }

            return $user->fresh(['roles', 'branches']);
        });
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user, string $newPassword = null): string
    {
        $password = $newPassword ?? Str::random(10);

        $user->update([
            'password' => Hash::make($password),
        ]);

        return $password;
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): bool
    {
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);
        return $newStatus;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleName)
    {
        return User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->get();
    }

    /**
     * Get users for a specific branch
     */
    public function getBranchUsers(int $branchId)
    {
        return User::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with(['roles', 'branches'])->get();
    }

    /**
     * Search users
     */
    public function searchUsers(string $query)
    {
        return User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->with(['roles', 'branches'])
            ->paginate(15);
    }
}
