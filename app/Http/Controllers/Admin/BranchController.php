<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranchRequest;
use App\Http\Requests\Admin\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\Office;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display branches list
     */
    public function index()
    {
        $branches = Branch::with(['office', 'users'])
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(15);

        $offices = Office::where('is_active', true)->get();

        return view('admin.branches.index', compact('branches', 'offices'));
    }

    /**
     * Show create branch form
     */
    public function create()
    {
        $offices = Office::where('is_active', true)->get();

        return view('admin.branches.create', compact('offices'));
    }

    /**
     * Store new branch
     */
    public function store(StoreBranchRequest $request)
    {
        $branch = $this->branchService->createBranch($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Branch created successfully',
                'branch' => $branch
            ]);
        }

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'Branch created successfully');
    }

    /**
     * Show branch details
     */
    public function show(Branch $branch)
    {
        $branch->load(['office', 'users' => function ($q) {
            $q->with('roles')->limit(10);
        }]);

        $stats = $this->branchService->getBranchStats($branch->id);

        return view('admin.branches.show', compact('branch', 'stats'));
    }

    /**
     * Show edit branch form
     */
    public function edit(Branch $branch)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'branch' => $branch
            ]);
        }

        $offices = Office::where('is_active', true)->get();

        return view('admin.branches.edit', compact('branch', 'offices'));
    }

    /**
     * Update branch
     */
    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        $this->branchService->updateBranch($branch, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully',
                'branch' => $branch
            ]);
        }

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'Branch updated successfully');
    }

    /**
     * Toggle branch status
     */
    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Branch status updated successfully',
                'is_active' => $branch->is_active
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Branch status updated');
    }

    /**
     * Show branch users
     */
    public function users(Branch $branch)
    {
        $users = $branch->users()->with('roles')->paginate(20);

        return view('admin.branches.users', compact('branch', 'users'));
    }

    /**
     * Get branch statistics
     */
    public function stats()
    {
        $stats = [
            'total' => Branch::count(),
            'active' => Branch::where('is_active', true)->count(),
            'staff' => \App\Models\User::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated branch data for AJAX table
     */
    public function data(Request $request)
    {
        $query = Branch::with(['office', 'users']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        // Filters
        if ($request->active == '1' && $request->inactive == '0') {
            $query->where('is_active', true);
        } elseif ($request->active == '0' && $request->inactive == '1') {
            $query->where('is_active', false);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $branches = $query->paginate($perPage);

        return response()->json($branches);
    }
}
