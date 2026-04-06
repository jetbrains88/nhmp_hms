<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user's assigned branches
     */
    public function branches()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $branches = Branch::where('is_active', true)
                ->select('id', 'name', 'type')
                ->get();
        } else {
            $branches = $user->branches()
                ->where('is_active', true)
                ->select('branches.id', 'branches.name', 'branches.type')
                ->get()
                ->map(function ($branch) use ($user) {
                    $branch->is_primary = $user->primary_branch_id == $branch->id;
                    return $branch;
                });
        }

        return response()->json($branches);
    }

    /**
     * Get user's permissions
     */
    public function permissions()
    {
        $user = auth()->user();

        $permissions = $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values();

        return response()->json($permissions);
    }

    /**
     * Get user's current branch context
     */
    public function context()
    {
        $user = auth()->user();
        $branchId = session('current_branch_id', $user->primary_branch_id);
        $branch = Branch::find($branchId);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'branch' => $branch ? [
                'id' => $branch->id,
                'name' => $branch->name,
                'type' => $branch->type,
            ] : null,
            'roles' => $user->roles->pluck('name'),
        ]);
    }
}
