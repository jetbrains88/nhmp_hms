<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        return view('pharmacy.medicine_categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer|default:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['is_active'] = $request->has('is_active') ? $request->is_active : true;

        $category = MedicineCategory::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $category
            ]);
        }

        return redirect()->route('pharmacy.medicine-categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, MedicineCategory $medicineCategory)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'display_order' => 'integer',
        ]);

        if ($medicineCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        }
        
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->is_active;
        }

        $medicineCategory->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => $medicineCategory
            ]);
        }

        return redirect()->route('pharmacy.medicine-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MedicineCategory $medicineCategory)
    {
        $medicineCategory->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);
        }

        return redirect()->route('pharmacy.medicine-categories.index')->with('success', 'Category deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:medicine_categories,id'
        ]);

        MedicineCategory::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' categories purged successfully.'
        ]);
    }

    public function toggleStatus(MedicineCategory $medicineCategory)
    {
        $medicineCategory->update(['is_active' => !$medicineCategory->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Category status updated.',
            'is_active' => $medicineCategory->is_active
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:medicine_categories,id',
            'is_active' => 'required|boolean'
        ]);

        MedicineCategory::whereIn('id', $validated['ids'])->update([
            'is_active' => $validated['is_active']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated for ' . count($validated['ids']) . ' categories.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => MedicineCategory::count(),
            'active' => MedicineCategory::where('is_active', true)->count(),
            'inactive' => MedicineCategory::where('is_active', false)->count(),
        ];

        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = MedicineCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'display_order');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $categories = $query->paginate($perPage);

        return response()->json($categories);
    }
}
