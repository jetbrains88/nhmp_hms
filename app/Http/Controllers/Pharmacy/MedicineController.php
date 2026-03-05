<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pharmacy\StoreMedicineRequest;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicineController extends Controller
{
    /**
     * Display medicine catalog
     */
    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'form'])
            ->where(function ($q) {
                // Show branch-specific and global medicines
                $q->where('branch_id', auth()->user()->current_branch_id)
                  ->orWhere('is_global', true);
            });
        
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('generic_name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('prescription')) {
            $query->where('requires_prescription', $request->prescription === 'required');
        }
        
        $medicines = $query->paginate(15);
        $categories = MedicineCategory::where('is_active', true)->get();
        
        return view('pharmacy.medicines.index', compact('medicines', 'categories'));
    }

    /**
     * Show medicine creation form
     */
    public function create()
    {
        $categories = MedicineCategory::where('is_active', true)->get();
        $forms = MedicineForm::all();
        
        return view('pharmacy.medicines.create', compact('categories', 'forms'));
    }

    /**
     * Store new medicine
     */
    public function store(StoreMedicineRequest $request)
    {
        $data = $request->validated();
        $data['uuid'] = (string) Str::uuid();
        
        // If not global, assign to current branch
        if (!($data['is_global'] ?? false)) {
            $data['branch_id'] = auth()->user()->current_branch_id;
        }
        
        $medicine = Medicine::create($data);
        
        return redirect()
            ->route('pharmacy.medicines.show', $medicine)
            ->with('success', 'Medicine added successfully');
    }

    /**
     * Show medicine details
     */
    public function show(Medicine $medicine)
    {
        $medicine->load(['category', 'form', 'batches' => function ($q) {
            $q->where('branch_id', auth()->user()->current_branch_id)
              ->orderBy('expiry_date');
        }]);
        
        $totalStock = $medicine->batches->sum('remaining_quantity');
        
        return view('pharmacy.medicines.show', compact('medicine', 'totalStock'));
    }

    /**
     * Show medicine edit form
     */
    public function edit(Medicine $medicine)
    {
        $categories = MedicineCategory::where('is_active', true)->get();
        $forms = MedicineForm::all();
        
        return view('pharmacy.medicines.edit', compact('medicine', 'categories', 'forms'));
    }

    /**
     * Update medicine
     */
    public function update(StoreMedicineRequest $request, Medicine $medicine)
    {
        $medicine->update($request->validated());
        
        return redirect()
            ->route('pharmacy.medicines.show', $medicine)
            ->with('success', 'Medicine updated successfully');
    }

    /**
     * AJAX: Search medicines for typeahead/dropdowns
     */
    public function apiSearch(Request $request)
    {
        $search = $request->get('q') ?: $request->get('search');
        
        $query = Medicine::with(['category', 'form'])
            ->where(function ($q) {
                $q->where('branch_id', auth()->user()->current_branch_id)
                  ->orWhere('is_global', true);
            })
            ->where('is_active', true);

        if ($search) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('generic_name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
            
            // Optional: Order by exact match first if needed by using a raw statement
            $query->orderByRaw("CASE 
                WHEN name = ? THEN 1 
                WHEN generic_name = ? THEN 2
                ELSE 3 
            END", [$search, $search]);
        }

        $medicines = $query->limit(20)->get();

        return response()->json($medicines);
    }

    /**
     * AJAX: Get medicine list for catalog
     */
    public function apiList(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        
        $query = Medicine::with(['category', 'form'])
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('is_global', true);
            });
            
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('generic_name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('prescription')) {
            $query->where('requires_prescription', $request->prescription === 'required');
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
        $length = $request->get('length', 15);
        $medicines = ($length === 'All') ? $query->get() : $query->paginate((int)$length);
        
        // Map data for Alpine.js
        $data = collect($medicines instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicines->items() : $medicines)->map(function($medicine) {
            $totalStock = $medicine->batches()->where('branch_id', auth()->user()->current_branch_id)->sum('remaining_quantity');
            
            return [
                'id' => $medicine->id,
                'uuid' => $medicine->uuid,
                'name' => $medicine->name,
                'generic_name' => $medicine->generic_name,
                'brand' => $medicine->brand,
                'manufacturer' => $medicine->manufacturer ?? 'N/A',
                'category_name' => $medicine->category?->name ?? 'N/A',
                'form_name' => $medicine->form?->name ?? 'N/A',
                'total_stock' => (int)$totalStock,
                'unit' => $medicine->unit,
                'reorder_level' => (int)$medicine->reorder_level,
                'requires_prescription' => (bool)$medicine->requires_prescription,
                'view_url' => route('pharmacy.medicines.show', $medicine->id),
                'edit_url' => route('pharmacy.medicines.edit', $medicine->id),
                'delete_url' => route('pharmacy.medicines.destroy', $medicine->id),
                'is_low_stock' => $totalStock <= $medicine->reorder_level,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => $medicines instanceof \Illuminate\Pagination\LengthAwarePaginator ? [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total(),
                'links' => (string)$medicines->links()
            ] : null,
            'stats' => [
                'total' => Medicine::where('branch_id', $branchId)->orWhere('is_global', true)->count(),
                'rx_required' => Medicine::where('requires_prescription', true)->count(),
                'global' => Medicine::where('is_global', true)->count(),
                'low_stock' => $data->filter(fn($m) => $m['is_low_stock'])->count(),
            ]
        ]);
    }
}