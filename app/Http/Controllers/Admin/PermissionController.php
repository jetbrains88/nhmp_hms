<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions|max:255',
            'display_name' => 'required|max:255',
            'group' => 'required|max:255',
        ]);

        $permission = Permission::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'data' => $permission
            ]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id . '|max:255',
            'display_name' => 'required|max:255',
            'group' => 'required|max:255',
        ]);

        $permission->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'data' => $permission
            ]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.'
            ]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Toggle permission status.
     */
    public function toggleStatus(Permission $permission)
    {
        $permission->update([
            'is_active' => !$permission->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission status updated successfully.',
            'is_active' => $permission->is_active
        ]);
    }

    /**
     * Get permission statistics with filters
     */
    public function stats(Request $request)
    {
        $query = Permission::query();
        $query = $this->applyFiltersToQuery($query, $request);

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'inactive' => (clone $query)->where('is_active', false)->count(),
            'groups' => (clone $query)->distinct('group')->count(),
            'available_groups' => Permission::getGroups(), // This usually returns all groups for the filter dropdown
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated permission data for AJAX table
     */
    public function data(Request $request)
    {
        $query = Permission::query();
        $query = $this->applyFiltersToQuery($query, $request);

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $permissions = $query->paginate($perPage);

        return response()->json($permissions);
    }

    /**
     * Apply common filters to permission query
     */
    protected function applyFiltersToQuery($query, Request $request)
    {
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('display_name', 'LIKE', "%{$search}%")
                  ->orWhere('group', 'LIKE', "%{$search}%");
            });
        }

        // Group filter
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    /**
     * Bulk update permission status.
     */
    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:permissions,id',
            'is_active' => 'required|boolean'
        ]);

        Permission::whereIn('id', $validated['ids'])->update([
            'is_active' => $validated['is_active']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated for ' . count($validated['ids']) . ' permissions.'
        ]);
    }

    /**
     * Bulk remove permissions.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:permissions,id'
        ]);

        Permission::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' permissions purged successfully.'
        ]);
    }
}
