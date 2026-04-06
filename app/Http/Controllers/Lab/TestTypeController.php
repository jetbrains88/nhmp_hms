<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabTestType;
use Illuminate\Http\Request;

class TestTypeController extends Controller
{
    public function index()
    {
        return view('lab.test_types.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:lab_test_types,name|max:255',
            'department' => 'nullable|max:255',
            'sample_type' => 'nullable|max:255',
        ]);

        $testType = LabTestType::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab Test Type created successfully.',
                'data' => $testType
            ]);
        }

        return redirect()->route('lab.test-types.index')->with('success', 'Lab Test Type created successfully.');
    }

    public function update(Request $request, LabTestType $testType)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:lab_test_types,name,' . $testType->id,
            'department' => 'nullable|max:255',
            'sample_type' => 'nullable|max:255',
        ]);

        $testType->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab Test Type updated successfully.',
                'data' => $testType
            ]);
        }

        return redirect()->route('lab.test-types.index')->with('success', 'Lab Test Type updated successfully.');
    }

    public function destroy(LabTestType $testType)
    {
        $testType->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab Test Type deleted successfully.'
            ]);
        }

        return redirect()->route('lab.test-types.index')->with('success', 'Lab Test Type deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lab_test_types,id'
        ]);

        LabTestType::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' test types purged successfully.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => LabTestType::count(),
            'departments' => LabTestType::distinct('department')->whereNotNull('department')->count(),
            'available_departments' => LabTestType::distinct('department')->whereNotNull('department')->pluck('department')->filter(),
        ];

        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = LabTestType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('department', 'LIKE', "%{$search}%")
                  ->orWhere('sample_type', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $testTypes = $query->paginate($perPage);

        return response()->json($testTypes);
    }
}
