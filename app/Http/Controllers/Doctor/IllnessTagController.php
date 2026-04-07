<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\IllnessTag;
use Illuminate\Http\Request;

class IllnessTagController extends Controller
{
    /**
     * List all illness tags (for Doctor Medical menu CRUD page).
     */
    public function index()
    {
        $stats = [
            'total'      => IllnessTag::count(),
            'active'     => IllnessTag::where('is_active', true)->count(),
            'inactive'   => IllnessTag::where('is_active', false)->count(),
            'chronic'    => IllnessTag::where('category', 'chronic')->count(),
            'acute'      => IllnessTag::where('category', 'acute')->count(),
            'infectious' => IllnessTag::where('category', 'infectious')->count(),
        ];

        return view('doctor.setup.illness-tags.index', compact('stats'));
    }

    /**
     * JSON data endpoint for the Alpine.js table.
     */
    public function data(Request $request)
    {
        $query = IllnessTag::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('icd_code', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortField = in_array($request->sort_by, ['name', 'category', 'is_active', 'created_at'])
            ? $request->sort_by : 'name';
        $sortDir   = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $perPage = in_array((int) $request->per_page, [10, 15, 25, 50, 100])
            ? (int) $request->per_page : 15;

        $tags = $query->orderBy($sortField, $sortDir)->paginate($perPage);

        return response()->json($tags);
    }

    /**
     * Stats endpoint for stat cards.
     */
    public function stats()
    {
        return response()->json([
            'total'      => IllnessTag::count(),
            'active'     => IllnessTag::where('is_active', true)->count(),
            'inactive'   => IllnessTag::where('is_active', false)->count(),
            'chronic'    => IllnessTag::where('category', 'chronic')->count(),
            'acute'      => IllnessTag::where('category', 'acute')->count(),
            'infectious' => IllnessTag::where('category', 'infectious')->count(),
        ]);
    }

    /**
     * Store new illness tag.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:illness_tags,name',
            'category'    => 'required|in:chronic,acute,infectious,other',
            'icd_code'    => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $tag = IllnessTag::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Illness tag created successfully.',
            'tag'     => $tag,
        ]);
    }

    /**
     * Update an illness tag.
     */
    public function update(Request $request, IllnessTag $illnessTag)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:illness_tags,name,' . $illnessTag->id,
            'category'    => 'required|in:chronic,acute,infectious,other',
            'icd_code'    => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $illnessTag->update(array_merge($validated, [
            'updated_by' => auth()->id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Illness tag updated successfully.',
            'tag'     => $illnessTag->fresh(),
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(IllnessTag $illnessTag)
    {
        $illnessTag->update(['is_active' => !$illnessTag->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated.',
            'tag'     => $illnessTag->fresh(),
        ]);
    }

    /**
     * Delete an illness tag.
     */
    public function destroy(IllnessTag $illnessTag)
    {
        $illnessTag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Illness tag deleted successfully.',
        ]);
    }

    /**
     * Bulk toggle status.
     */
    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:illness_tags,id',
            'is_active' => 'required|boolean'
        ]);

        IllnessTag::whereIn('id', $validated['ids'])->update([
            'is_active' => $validated['is_active']
        ]);

        $count = count($validated['ids']);
        $action = $validated['is_active'] ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action} {$count} tags."
        ]);
    }

    /**
     * Bulk destroy.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:illness_tags,id'
        ]);

        IllnessTag::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' tags purged successfully.'
        ]);
    }
}
