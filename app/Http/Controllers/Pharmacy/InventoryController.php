<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pharmacy\AddStockRequest;
use App\Http\Requests\Pharmacy\TransferStockRequest;
use App\Http\Requests\Pharmacy\AdjustStockRequest;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineCategory;
use App\Models\Branch;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display inventory
     */
    public function index(Request $request)
    {
        $categories = MedicineCategory::orderBy('name')->get();
        
        $initialFilters = json_encode([
            'category' => $request->get('category', 'All'),
            'stock_status' => $request->get('stock_status', 'All'),
            'sort_by' => $request->get('sort_by', 'expiry_date'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
            'length' => (int) $request->get('length', 16),
            'search' => $request->get('search', ''),
            'page' => (int) $request->get('page', 1)
        ]);
        
        return view('pharmacy.inventory.index', compact('categories', 'initialFilters'));
    }

    /**
     * Show add stock form
     */
    public function create(Request $request)
    {
        $selectedMedicine = null;
        if ($request->has('medicine_id')) {
            $selectedMedicine = Medicine::find($request->medicine_id);
        }

        $medicines = Medicine::where(function ($q) {
            $q->where('branch_id', auth()->user()->current_branch_id)
              ->orWhere('is_global', true);
        })->orderBy('name')->get();

        $categories = MedicineCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('pharmacy.inventory.create', compact('medicines', 'categories', 'selectedMedicine'));
    }

    /**
     * Add stock to inventory
     */
    public function store(AddStockRequest $request)
    {
        $medicine = Medicine::findOrFail($request->medicine_id);
        
        // Find or create batch
        $batch = MedicineBatch::firstOrCreate(
            [
                'branch_id' => auth()->user()->current_branch_id,
                'medicine_id' => $medicine->id,
                'batch_number' => $request->batch_number,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'expiry_date' => $request->expiry_date,
                'unit_price' => $request->unit_price,
                'sale_price' => $request->sale_price,
                'remaining_quantity' => 0,
                'is_active' => true,
            ]
        );
        
        // Add stock
        $log = $this->inventoryService->addStock(
            $batch,
            $request->quantity,
            auth()->id(),
            $request->notes,
            $request->rc_number
        );
        
        return redirect()
            ->route('pharmacy.inventory')
            ->with('success', "Added {$request->quantity} units to batch {$batch->batch_number}");
    }

    /**
     * Show batch details
     */
    public function showBatch(MedicineBatch $batch)
    {
        $batch->load(['medicine', 'inventoryLogs.user' => function ($q) {
            $q->latest();
        }]);
        
        return view('pharmacy.inventory.batch', compact('batch'));
    }

    /**
     * Transfer stock form
     */
    public function transferForm(MedicineBatch $batch)
    {
        $branches = Branch::where('id', '!=', $batch->branch_id)
            ->where('is_active', true)
            ->get();
        
        return view('pharmacy.inventory.transfer', compact('batch', 'branches'));
    }

    /**
     * Transfer stock
     */
    public function transfer(TransferStockRequest $request)
    {
        $batch = MedicineBatch::findOrFail($request->batch_id);
        
        $result = $this->inventoryService->transferStock(
            $batch,
            $request->quantity,
            $request->target_branch_id,
            auth()->id(),
            $request->notes
        );
        
        return redirect()
            ->route('pharmacy.inventory')
            ->with('success', "Transferred {$request->quantity} units to branch {$result['target_batch']->branch_id}");
    }

    /**
     * Adjust stock form
     */
    public function adjustForm(MedicineBatch $batch)
    {
        return view('pharmacy.inventory.adjust', compact('batch'));
    }

    /**
     * Adjust stock
     */
    public function adjust(AdjustStockRequest $request, MedicineBatch $batch)
    {
        $log = $this->inventoryService->adjustStock(
            $batch,
            $request->new_quantity,
            auth()->id(),
            $request->reason
        );
        
        return redirect()
            ->route('pharmacy.inventory.batch', $batch)
            ->with('success', "Stock adjusted to {$request->new_quantity} units");
    }


    /**
     * Get inventory list for AJAX
     */
    public function inventoryList(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        
        $query = MedicineBatch::with(['medicine.category', 'medicine.form'])
            ->where('medicine_batches.branch_id', $branchId);
            
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('medicine', function($mq) use ($search) {
                    $mq->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('generic_name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")
                      ->orWhere('brand', 'LIKE', "%{$search}%");
                    
                    // Priority for exact matches (this doesn't change the set, but we could sort by it later)
                })->orWhere('batch_number', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('category') && $request->category !== 'All') {
            $query->whereHas('medicine', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }
        
        if ($request->filled('stock_status') && $request->stock_status !== 'All') {
            if ($request->stock_status === 'low') {
                $query->whereHas('medicine', function($q) {
                    $q->whereRaw('medicine_batches.remaining_quantity <= medicines.reorder_level');
                });
            } elseif ($request->stock_status === 'out') {
                $query->where('medicine_batches.remaining_quantity', '<=', 0);
            } elseif ($request->stock_status === 'near_expiry') {
                $query->where('expiry_date', '<=', now()->addDays(30))
                      ->where('expiry_date', '>=', now()->startOfDay())
                      ->where('remaining_quantity', '>', 0);
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'expiry_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortBy === 'name') {
            $query->join('medicines', 'medicine_batches.medicine_id', '=', 'medicines.id')
                ->orderBy('medicines.name', $sortDirection)
                ->select('medicine_batches.*');
        } elseif ($sortBy === 'stock') {
            $query->orderBy('medicine_batches.remaining_quantity', $sortDirection);
        } else {
            $query->orderBy('medicine_batches.' . $sortBy, $sortDirection);
        }
        
        $length = $request->get('length', 16);
        $batches = ($length === 'All') ? $query->get() : $query->paginate($length === 'All' ? 1000 : (int)$length);
        
        // Map data for Alpine.js
        $data = collect($batches instanceof \Illuminate\Pagination\LengthAwarePaginator ? $batches->items() : $batches)->map(function($batch) {
            $medicine = $batch->medicine;
            
            if (!$medicine) {
                return [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'medicine_name' => 'Unknown Medicine (ID: ' . $batch->medicine_id . ')',
                    'medicine_code' => 'N/A',
                    'medicine_brand' => 'N/A',
                    'strength' => 'N/A',
                    'form' => 'N/A',
                    'category_name' => 'N/A',
                    'stock' => (int)$batch->remaining_quantity,
                    'reorder_level' => 0,
                    'requires_prescription' => false,
                    'stock_percentage' => 0,
                    'stock_color' => 'bg-slate-500',
                    'expiry_date' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : 'N/A',
                    'is_about_to_expire' => false,
                    'unit_price' => number_format($batch->unit_price, 2),
                    'sale_price' => number_format($batch->sale_price, 2),
                    'view_url' => route('pharmacy.inventory.batch', $batch->id),
                    'edit_url' => route('pharmacy.inventory.adjust-form', $batch->id),
                ];
            }
            
            $stockPercentage = $medicine->reorder_level > 0 ? min(100, ($batch->remaining_quantity / max(1, ($medicine->reorder_level * 2))) * 100) : 100;
            
            return [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'medicine_name' => $medicine->name,
                'medicine_code' => $medicine->code,
                'medicine_brand' => $medicine->brand,
                'strength' => $medicine->strength,
                'form' => $medicine->form?->name ?? 'N/A',
                'category_name' => $medicine->category?->name ?? 'Default',
                'stock' => (int)$batch->remaining_quantity,
                'reorder_level' => (int)$medicine->reorder_level,
                'requires_prescription' => (bool)$medicine->requires_prescription,
                'stock_percentage' => $stockPercentage,
                'stock_color' => $batch->remaining_quantity <= $medicine->reorder_level ? 'bg-rose-500' : 'bg-emerald-500',
                'expiry_date' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : 'N/A',
                'is_about_to_expire' => $batch->expiry_date ? $batch->expiry_date->diffInDays(now()) <= 30 : false,
                'unit_price' => number_format($batch->unit_price, 2),
                'sale_price' => number_format($batch->sale_price, 2),
                'view_url' => route('pharmacy.inventory.batch', $batch->id),
                'edit_url' => route('pharmacy.inventory.adjust-form', $batch->id),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => $batches instanceof \Illuminate\Pagination\LengthAwarePaginator ? [
                'current_page' => $batches->currentPage(),
                'last_page' => $batches->lastPage(),
                'per_page' => $batches->perPage(),
                'total' => $batches->total(),
                'links' => (string)$batches->links()
            ] : null,
            'stats' => [
                'total' => MedicineBatch::where('branch_id', $branchId)->count(),
                'low_stock' => MedicineBatch::where('branch_id', $branchId)
                    ->whereHas('medicine', function($q) {
                        $q->whereColumn('medicine_batches.remaining_quantity', '<=', 'medicines.reorder_level');
                    })
                    ->count(),
                'out_of_stock' => MedicineBatch::where('branch_id', $branchId)->where('remaining_quantity', '<=', 0)->count(),
                'near_expiry' => MedicineBatch::where('branch_id', $branchId)
                    ->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>=', now())
                    ->where('remaining_quantity', '>', 0)
                    ->count(),
            ]
        ]);
    }
    public function medicineStock($id)
    {
        $stock = MedicineBatch::where('medicine_id', $id)
            ->where('branch_id', auth()->user()->current_branch_id)
            ->sum('remaining_quantity');
            
        return response()->json(['stock' => (int)$stock]);
    }
}