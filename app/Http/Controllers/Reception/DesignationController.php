<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesignationController extends Controller
{
    public function index()
    {
        return view('reception.designations.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:100',
            'short_form' => 'nullable|max:50',
            'bps' => 'nullable|integer',
            'cadre_type' => 'nullable|max:50',
            'rank_group' => 'nullable|max:100',
        ]);

        $validated['uuid'] = (string) Str::uuid();

        $designation = Designation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Designation created successfully.',
            'data' => $designation
        ]);
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'title' => 'required|max:100',
            'short_form' => 'nullable|max:50',
            'bps' => 'nullable|integer',
            'cadre_type' => 'nullable|max:50',
            'rank_group' => 'nullable|max:100',
        ]);

        $designation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Designation updated successfully.',
            'data' => $designation
        ]);
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully.'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:designations,id'
        ]);

        Designation::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' designations purged successfully.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Designation::count(),
            'ranks' => Designation::distinct('rank_group')->count('rank_group')
        ];

        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = Designation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('short_form', 'LIKE', "%{$search}%")
                  ->orWhere('cadre_type', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('rank_group')) {
            $query->where('rank_group', $request->rank_group);
        }

        $sort = $request->get('sort', 'bps');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $designations = $query->paginate($perPage);

        return response()->json($designations);
    }
}
