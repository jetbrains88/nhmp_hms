<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabTestParameter;
use App\Models\LabTestType;
use Illuminate\Http\Request;

class TestParameterController extends Controller
{
    public function index()
    {
        // For the frontend dropdowns
        $testTypes = LabTestType::orderBy('name')->get();
        return view('lab.test_parameters.index', compact('testTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lab_test_type_id' => 'required|exists:lab_test_types,id',
            'name' => 'required|max:255',
            'group_name' => 'nullable|max:255',
            'unit' => 'nullable|max:255',
            'reference_range' => 'nullable|string',
            'min_range' => 'nullable|numeric',
            'max_range' => 'nullable|numeric',
            'input_type' => 'required|in:text,number,numeric',
            'order' => 'integer|default:0'
        ]);

        $testParameter = LabTestParameter::create($validated);
        $testParameter->load('labTestType');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Test Parameter created successfully.',
                'data' => $testParameter
            ]);
        }

        return redirect()->route('lab.test-parameters.index')->with('success', 'Test Parameter created successfully.');
    }

    public function update(Request $request, LabTestParameter $testParameter)
    {
        $validated = $request->validate([
            'lab_test_type_id' => 'required|exists:lab_test_types,id',
            'name' => 'required|max:255',
            'group_name' => 'nullable|max:255',
            'unit' => 'nullable|max:255',
            'reference_range' => 'nullable|string',
            'min_range' => 'nullable|numeric',
            'max_range' => 'nullable|numeric',
            'input_type' => 'required|in:text,number,numeric',
            'order' => 'integer'
        ]);

        $testParameter->update($validated);
        $testParameter->load('labTestType');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Test Parameter updated successfully.',
                'data' => $testParameter
            ]);
        }

        return redirect()->route('lab.test-parameters.index')->with('success', 'Test Parameter updated successfully.');
    }

    public function destroy(LabTestParameter $testParameter)
    {
        $testParameter->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Test Parameter deleted successfully.'
            ]);
        }

        return redirect()->route('lab.test-parameters.index')->with('success', 'Test Parameter deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lab_test_parameters,id'
        ]);

        LabTestParameter::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' parameters purged successfully.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => LabTestParameter::count(),
            'groups' => LabTestParameter::distinct('group_name')->whereNotNull('group_name')->count(),
            'types' => LabTestParameter::distinct('lab_test_type_id')->count(),
        ];

        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = LabTestParameter::with('labTestType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('group_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('labTestType', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('test_type')) {
            $query->where('lab_test_type_id', $request->test_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        if ($sort === 'test_type') {
            $query->join('lab_test_types', 'lab_test_parameters.lab_test_type_id', '=', 'lab_test_types.id')
                  ->orderBy('lab_test_types.name', $direction)
                  ->select('lab_test_parameters.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $perPage = $request->get('per_page', 10);
        $parameters = $query->paginate($perPage);

        return response()->json($parameters);
    }
}
