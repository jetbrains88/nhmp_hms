<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\RolePermissionRequest;
use App\Http\Requests\Admin\StoreRolePermissionRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdminRepository;
use App\Services\AdminService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected AdminService $adminService;
    protected AdminRepository $adminRepository;

    public function __construct(AdminService $adminService, AdminRepository $adminRepository)
    {
        $this->adminService = $adminService;
        $this->adminRepository = $adminRepository;

        $this->middleware(['auth', 'role:admin']);
    }

    // Dashboard
    public function dashboard()
    {
        $dashboardData = $this->adminService->getDashboardData();
        $systemHealth = $this->adminService->getSystemHealth();
        // --- NEW: Analytical Data for Charts ---

        // 1. Patient Registration Trend (Last 7 Days)
        $dates = collect();
        foreach (range(6, 0) as $i) {
            $dates->push(Carbon::now()->subDays($i)->format('M d'));
        }

        // Mocking query logic - Replace with actual DB::raw queries in production
        $chartData = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'patients' => [12, 19, 15, 25, 22, 30, $dashboardData['stats']['todayVisits'] ?? 35],
            'visits' => [4500, 5200, 4800, 6100, 5900, 7200, $dashboardData['stats']['todayRevenue'] ?? 7500]
        ];

        // 2. Department Load (Pie Chart)
        $departmentData = [
            'labels' => ['General', 'Cardiology', 'Neurology', 'Orthopedic'],
            'series' => [44, 55, 13, 33] // Fetch from DB group by department
        ];

        return view('admin.dashboard', array_merge(
            $dashboardData,
            [
                'stats' => $dashboardData['stats'],
                'systemHealth' => $systemHealth,
                'chartData' => $chartData,
                'departmentData' => $departmentData
            ]
        ));
    }

    // NEW: Get patient flow data for charts

    public function getChartData(Request $request)
    {
        try {
            $period = $request->input('period', 'weekly');
            $data = $this->getPatientFlowData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Error in getChartData: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data',
                'data' => []
            ], 500);
        }
    }

    // NEW: AJAX endpoint for chart data

    public function getPatientFlowData($period = 'weekly')
    {
        try {
            $data = [];

            if ($period === 'weekly') {
                // Last 7 days
                $dates = collect();
                $patientsData = [];
                $visitsData = [];

                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dates->push($date->format('D'));

                    // Replace these with actual database queries
                    // Example: Patient::whereDate('created_at', $date->format('Y-m-d'))->count()
                    $patientsData[] = rand(15, 40);
                    $visitsData[] = rand(4500, 8000);
                }

                $data = [
                    'labels' => $dates->toArray(),
                    'patients' => $patientsData,
                    'visits' => $visitsData,
                    'period' => 'weekly'
                ];
            } elseif ($period === 'monthly') {
                // Last 12 months
                $labels = [];
                $patientsData = [];
                $visitsData = [];

                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M');

                    // Replace these with actual database queries
                    // Example: Patient::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count()
                    $patientsData[] = rand(100, 500);
                    $visitsData[] = rand(25000, 100000);
                }

                $data = [
                    'labels' => $labels,
                    'patients' => $patientsData,
                    'visits' => $visitsData,
                    'period' => 'monthly'
                ];
            }

            return $data;
        } catch (Exception $e) {
            Log::error('Error fetching patient flow data: ' . $e->getMessage());

            // Return default data on error
            return $period === 'weekly' ? [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'patients' => [12, 19, 15, 25, 22, 30, 35],
                'visits' => [4500, 5200, 4800, 6100, 5900, 7200, 7500],
                'period' => 'weekly'
            ] : [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'patients' => [120, 190, 150, 250, 220, 300, 350, 280, 310, 290, 330, 380],
                'visits' => [45000, 52000, 48000, 61000, 59000, 72000, 75000, 68000, 71000, 69000, 73000, 78000],
                'period' => 'monthly'
            ];
        }
    }

    public function users()
    {
        // Get paginated users
        $users = $this->adminRepository->getAllUsers();
        $roles = $this->adminRepository->getAllRoles();

        // FIXED: Don't log the entire model objects, just log counts or specific data
        Log::debug('Users data loaded', [
            'users_count' => $users->count(),
            'users_total' => $users->total(),
            'roles_count' => $roles->count(),
            'auth_user_id' => auth()->id(),
            'auth_user_email' => auth()->user()?->email,
        ]);

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'method' => 'required|in:auto,manual',
                'password' => 'required_if:method,manual|string|min:8|confirmed',
                'send_email' => 'boolean'
            ]);

            // Generate or use provided password
            $password = $request->method === 'auto'
                ? Str::random(10) // Generate random 10 character password
                : $request->password;

            // Update user password
            $user->password = Hash::make($password);
            $user->save();

            // Send email if requested
            if ($request->send_email) {
                // You can implement email sending here
                // Mail::to($user->email)->send(new PasswordResetMail($user, $password));

                Log::info('Password reset email would be sent to: ' . $user->email);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully' . ($request->method === 'auto' ? '. New password: ' . $password : ''),
                'password' => $request->method === 'auto' ? $password : null // Only return if auto-generated
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error resetting password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions($id)
    {
        try {
            $user = User::with('roles.permissions')->findOrFail($id);

            // Get all permissions from user's roles
            $permissions = collect();

            foreach ($user->roles as $role) {
                $permissions = $permissions->merge($role->permissions);
            }

            // Remove duplicates
            $permissions = $permissions->unique('id')->values();

            // Format permissions for frontend
            $formattedPermissions = $permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name ?? $permission->name,
                    'module' => explode('_', $permission->name)[1] ?? 'general'
                ];
            });

            return response()->json([
                'success' => true,
                'permissions' => $formattedPermissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user permissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleUserStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Don't allow deactivating yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own status'
                ], 403); // This returns 403 Forbidden
            }

            $user->is_active = !$user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User ' . ($user->is_active ? 'activated' : 'deactivated') . ' successfully',
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating user status: ' . $e->getMessage()
            ], 500);
        }
    }

    // User Management - Show Page

    public function getUsersData(Request $request)
    {
        try {
            // optimized query efficiently
            $query = User::with('roles:id,name,display_name')
                ->select('id', 'name', 'email', 'is_active', 'last_login_at', 'created_at');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas(
                            'roles',
                            function ($r) use ($search) {
                                $r->where('display_name', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%");
                            }
                        );
                });
            }

            // Status filters
            if ($request->filled('active')) {
                if ($request->active == '1') {
                    $query->where('is_active', true);
                }
            }

            if ($request->filled('inactive')) {
                if ($request->inactive == '1') {
                    $query->where('is_active', false);
                }
            }

            // Role filter
            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('id', $request->role);
                });
            }

            // Sorting
            $sortField = $request->get('sort', 'name');
            $sortDirection = $request->get('direction', 'asc');
            $allowedSortFields = ['name', 'email', 'created_at', 'last_login_at', 'is_active'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('name', 'asc');
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            // Transform data for frontend
            $transformedUsers = $users->getCollection()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(
                        function ($role) {
                            return [
                                'role_id' => $role->id,
                                'role_name' => $role->name,
                                'role_display_name' => $role->display_name,
                            ];
                        }
                    ),
                    'is_active' => (bool)$user->is_active,
                    'last_login_at' => $user->last_login_at ? Carbon::parse($user->last_login_at)->format('M d, Y H:i') : 'Never',
                    'created_at' => $user->created_at->format('M d, Y'),
                ];
            });

            return response()->json([
                'current_page' => $users->currentPage(),
                'data' => $transformedUsers,
                'first_page_url' => $users->url(1),
                'from' => $users->firstItem(),
                'last_page' => $users->lastPage(),
                'last_page_url' => $users->url($users->lastPage()),
                'next_page_url' => $users->nextPageUrl(),
                'path' => $users->path(),
                'per_page' => $users->perPage(),
                'prev_page_url' => $users->previousPageUrl(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ]);
        } catch (Exception $e) {
            Log::error('Error in getUsersData:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to load users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserStats()
    {
        try {
            // Optimize: Get all stats in fewer queries
            $totalUsers = User::count();

            // Group by is_active status
            $statusCounts = User::selectRaw('is_active, count(*) as count')
                ->groupBy('is_active')
                ->pluck('count', 'is_active');

            // Get admin count efficiently
            // Assuming 'admin' is the role name. Adjust if it uses slugs or IDs.
            $adminCount = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->count();

            $stats = [
                'total' => $totalUsers,
                'active' => $statusCounts[1] ?? 0,
                'inactive' => $statusCounts[0] ?? 0,
                'admins' => $adminCount,
            ];

            return response()->json($stats);
        } catch (Exception $e) {
            Log::error('Error in getUserStats: ' . $e->getMessage());
            return response()->json([
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'admins' => 0
            ]);
        }
    }

    // Get User Stats (AJAX)

    public function editUser($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            // FIXED: Get all role IDs for the form (multiple roles)
            $roleIds = $user->roles->pluck('id')->toArray();

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_ids' => $roleIds, // Changed from role_id to role_ids (array)
                'is_active' => $user->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'User not found',
                'success' => false
            ], 404);
        }
    }

    // Edit User (AJAX)

    public function storeUser(StoreUserRequest $request)
    {
        try {
            $this->adminRepository->createUser($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    // Store User (AJAX)

    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {
            Log::info('updateUser:', $request->all());
            $this->adminRepository->updateUser($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User is updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    // Toggle User Status (AJAX)

    public function destroyUser($id)
    {
        try {
            $this->adminRepository->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete User (AJAX)

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:users,id'
        ]);

        try {
            $users = User::whereIn('id', $request->ids);

            switch ($request->action) {
                case 'activate':
                    $users->update(['is_active' => true]);
                    $message = 'Users activated successfully';
                    break;

                case 'deactivate':
                    $users->update(['is_active' => false]);
                    $message = 'Users deactivated successfully';
                    break;

                case 'delete':
                    $users->delete();
                    $message = 'Users deleted successfully';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action'
            ], 500);
        }
    }

    // Bulk Actions (AJAX)

    public function roles()
    {
        $roles = $this->adminRepository->getAllRoles();
        $permissions = $this->adminRepository->getAllPermissions();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    // Role Management

    public function storeRole(StoreRoleRequest $request)
    {
        try {
            Log::info('RolePermissionRequest: ', $request->all());
            $this->adminRepository->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editRole($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);

            // Return role data with permission IDs for form population
            return response()->json([
                'success' => true,
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'permissions' => $role->permissions->pluck('id')->toArray(),
                'created_at' => $role->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $role->updated_at->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // In AdminController.php - Add this method after the roles() method

    public function updateRole(UpdateRoleRequest $request, $id)
    {
        try {
            Log::info('RoleUpdate: ', $request->all());
            // Call repository
            $this->adminRepository->updateRole($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateRole:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating role: ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_id' => $id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyRole($id)
    {
        try {
            $this->adminRepository->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function permissions()
    {
        $permissions = $this->adminRepository->getAllPermissions();

        return view('admin.permissions.index', compact('permissions'));
    }

    // Permission Management

    public function settings()
    {
        $systemHealth = $this->adminService->getSystemHealth();

        return view('admin.settings', compact('systemHealth'));
    }

    // System Settings

    public function updateSettings($request, $id)
    {
        try {
            Log::info("Update Settings ID: " . $id);
            Log::info("Update Settings: " . $request->all());

            // $this->adminRepository->updateSettings($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickAction(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'clear_cache':
                Artisan::call('cache:clear');
                $message = 'Cache cleared successfully';
                break;

            case 'backup_database':
                Artisan::call('backup:run');
                $message = 'Database backup initiated';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // Quick Actions

    private function getDepartmentData()
    {
        // Your existing department data logic
        return [
            'labels' => ['General', 'Cardiology', 'Neurology', 'Orthopedic'],
            'series' => [44, 55, 13, 33]
        ];
    }
}
