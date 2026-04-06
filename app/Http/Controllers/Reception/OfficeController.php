<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfficeController extends Controller
{
    public function index()
    {
        return view('reception.offices.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|max:255',
            'parent_id' => 'nullable|exists:offices,id',
        ]);

        $validated['uuid'] = (string) Str::uuid();

        $office = new Office($validated);
        $office->is_active = $request->has('is_active') ? $request->is_active : true;
        $office->save();

        return response()->json([
            'success' => true,
            'message' => 'Office created successfully.',
            'data' => $office
        ]);
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|max:255',
            'parent_id' => 'nullable|exists:offices,id',
        ]);

        $office->fill($validated);
        if ($request->has('is_active')) {
            $office->is_active = $request->is_active;
        }
        $office->save();

        return response()->json([
            'success' => true,
            'message' => 'Office updated successfully.',
            'data' => $office
        ]);
    }

    public function destroy(Office $office)
    {
        if ($office->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an office with child offices.'
            ], 422);
        }

        $office->delete();

        return response()->json([
            'success' => true,
            'message' => 'Office deleted successfully.'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:offices,id'
        ]);

        $offices = Office::whereIn('id', $validated['ids'])->withCount('children')->get();
        
        $deletedCount = 0;
        foreach ($offices as $office) {
            if ($office->children_count == 0) {
                $office->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {
            return response()->json(['success' => false, 'message' => 'None of the selected offices could be deleted because they have child offices.'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $deletedCount . ' offices purged successfully.' . ($deletedCount < count($validated['ids']) ? ' Some skipped due to child dependencies.' : '')
        ]);
    }

    public function toggleStatus(Office $office)
    {
        $office->is_active = !$office->is_active;
        $office->save();

        return response()->json([
            'success' => true,
            'message' => 'Office status updated.',
            'is_active' => $office->is_active
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Office::count(),
            'active' => Office::where('is_active', true)->count(),
            'inactive' => Office::where('is_active', false)->count(),
        ];

        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = Office::with('parent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $offices = $query->paginate($perPage);

        return response()->json($offices);
    }
}
