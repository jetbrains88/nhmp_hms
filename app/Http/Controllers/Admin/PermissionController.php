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
     * Get permission statistics.
     */
    public function stats()
    {
        $stats = [
            'total' => Permission::count(),
            'active' => Permission::where('is_active', true)->count(),
            'inactive' => Permission::where('is_active', false)->count(),
            'groups' => Permission::distinct('group')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated permission data.
     */
    public function data(Request $request)
    {
        $query = Permission::query();

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

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $permissions = $query->paginate($perPage);

        return response()->json($permissions);
    }
}
