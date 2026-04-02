<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Branch;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display roles list
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'branch'])->orderBy('name')->paginate(15);
        $permissions = Permission::all()->groupBy('group'); // Add this line

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show create role form
     */
    public function create()
    {
        $permissions = $this->roleService->getPermissionsGrouped();
        $branches = Branch::where('is_active', true)->get();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'permissions' => $permissions,
                'branches' => $branches
            ]);
        }

        return view('admin.roles.create', compact('permissions', 'branches'));
    }

    /**
     * Store new role
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->createRole($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role
            ]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Show role details
     */
    public function show(Role $role)
    {
        return redirect()->route('admin.roles.index');
    }

    /**
     * Show edit role form
     */
    public function edit(Role $role)
    {
        $permissions = $this->roleService->getPermissionsGrouped();
        $branches = Branch::where('is_active', true)->get();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'permissions' => $role->permissions->pluck('id'),
                'branch_id' => $role->branch_id
            ]);
        }

        return view('admin.roles.edit', compact('role', 'permissions', 'branches'));
    }

    /**
     * Update role
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleService->updateRole($role, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role
            ]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove role
     */
    public function destroy(Role $role)
    {
        if ($role->id <= 5) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'System roles cannot be deleted'
                ], 403);
            }
            return redirect()->back()->with('error', 'System roles cannot be deleted');
        }

        $role->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }

    /**
     * Clone role to branch
     */
    public function cloneToBranch(Request $request, Role $role)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $newRole = $this->roleService->cloneRoleToBranch($role, $request->branch_id);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role cloned successfully');
    }

    /**
     * Get role statistics with filters
     */
    public function stats(Request $request)
    {
        $query = Role::query();
        $query = $this->applyFiltersToQuery($query, $request);

        $stats = [
            'total' => (clone $query)->count(),
            'with_permissions' => (clone $query)->has('permissions')->count(),
            'without_permissions' => (clone $query)->doesntHave('permissions')->count(),
            'system_roles' => (clone $query)->where('id', '<=', 5)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated role data for AJAX table
     */
    public function data(Request $request)
    {
        $query = Role::with(['permissions', 'branch']);
        $query = $this->applyFiltersToQuery($query, $request);

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $roles = $query->paginate($perPage);

        return response()->json($roles);
    }

    /**
     * Apply common filters to role query
     */
    protected function applyFiltersToQuery($query, Request $request)
    {
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('display_name', 'LIKE', "%{$search}%");
            });
        }

        // Security Isolation: Non-super admins are restricted to their assigned branch + global roles
        if (!auth()->user()->isSuperAdmin()) {
            $userBranchId = auth()->user()->primary_branch_id;
            if ($userBranchId) {
                $query->where(function($q) use ($userBranchId) {
                    $q->where('branch_id', $userBranchId)
                      ->orWhereNull('branch_id'); // Global roles always visible
                });
            }
        }

        // Optional Branch Filter (Explicitly selected by the user)
        if ($request->filled('branch_id')) {
            $filterBranchId = $request->branch_id;
            $query->where(function($q) use ($filterBranchId) {
                if ($filterBranchId === 'null' || $filterBranchId === 'system') {
                    $q->whereNull('branch_id');
                } else {
                    $q->where('branch_id', (int) $filterBranchId)
                      ->orWhereNull('branch_id'); // System roles stay visible even when filtering branches
                }
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        return $query;
    }

    /**
     * Toggle role status (active/inactive).
     */
    public function toggleStatus(Role $role)
    {
        if ($role->id <= 5) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be deactivated.'
            ], 403);
        }

        $role->update([
            'is_active' => !$role->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role status updated successfully.',
            'is_active' => $role->is_active
        ]);
    }
}
