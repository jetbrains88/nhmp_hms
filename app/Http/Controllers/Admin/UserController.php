<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display users list
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'branches']);

        if ($request->has('search')) {
            $users = $this->userService->searchUsers($request->search);
        } else {
            $users = $query->paginate(15);
        }

        // Get all roles and branches for the modal
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.index', compact('users', 'roles', 'branches'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    /**
     * Store new user
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'branches', 'auditLogs' => function ($q) {
            $q->with(['branch', 'user'])->latest()->limit(20);
        }]);

        $roles = \App\Models\Role::all();
        $branches = \App\Models\Branch::all();
        $offices = \App\Models\Office::where('is_active', true)->get();

        return view('admin.users.show', compact('user', 'roles', 'branches', 'offices'));
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        if (request()->wantsJson() || request()->ajax()) {
            $user->load(['roles', 'branches']);
            return response()->json([
                'user' => $user,
                'role_ids' => $user->roles->pluck('id'),
                'branch_ids' => $user->branches->pluck('id'),
                'primary_branch_id' => $user->branches->where('pivot.is_primary', true)->first()?->id,
            ]);
        }

        return redirect()->route('admin.users.index', ['edit_uuid' => $user->uuid]);
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->userService->updateUser($user, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $this->userService->toggleStatus($user);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "User status changed to " . ($newStatus ? 'active' : 'inactive'),
                'is_active' => $newStatus
            ]);
        }

        return redirect()
            ->back()
            ->with('success', "User status changed to " . ($newStatus ? 'active' : 'inactive'));
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $newPassword = $this->userService->resetPassword($user);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'new_password' => $newPassword
            ]);
        }

        return redirect()
            ->back()
            ->with('success', "Password reset successfully. New password: {$newPassword}");
    }

    /**
     * Get user statistics with filters
     */
    public function stats(Request $request)
    {
        $query = User::query();
        $query = $this->applyFiltersToQuery($query, $request);

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'inactive' => (clone $query)->where('is_active', false)->count(),
            'admins' => (clone $query)->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })->count()
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated user data for AJAX table
     */
    public function data(Request $request)
    {
        $query = User::with(['roles', 'branches']);
        $query = $this->applyFiltersToQuery($query, $request);

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        
        // Ensure sort field is valid (add more allowed fields as needed)
        $allowedSortFields = ['name', 'email', 'created_at', 'last_login_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'name';
        }
        
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Apply common filters to user query
     */
    protected function applyFiltersToQuery($query, Request $request)
    {
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('filterStatus')) {
            if ($request->filterStatus === 'active') {
                $query->where('is_active', true);
            } elseif ($request->filterStatus === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Roles Filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        // Date Range Filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Security Isolation: Non-super admins are always restricted to their assigned branch
        if (!auth()->user()->isSuperAdmin()) {
            $userBranchId = auth()->user()->primary_branch_id;
            if ($userBranchId) {
                $query->whereHas('branches', function ($q) use ($userBranchId) {
                    $q->where('branches.id', $userBranchId);
                });
            }
        }

        // Optional Branch Filter (Explicitly selected by the user - mostly for super-admins)
        if ($request->filled('branch_id')) {
            $filterBranchId = $request->branch_id;
            $query->whereHas('branches', function ($q) use ($filterBranchId) {
                $q->where('branches.id', (int) $filterBranchId);
            });
        }

        return $query;
    }

    /**
     * Show user audit log
     */
    public function auditLog(User $user)
    {
        $logs = $user->auditLogs()
            ->with(['branch', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users.audit', compact('user', 'logs'));
    }

    /**
     * Show user permissions
     */
    public function permissions(User $user)
    {
        $user->load(['roles.permissions']);
        $permissions = $user->permissions();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);
        }

        return view('admin.users.permissions', compact('user', 'permissions'));
    }
}
