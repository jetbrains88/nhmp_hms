<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pharmacy\DispenseRequest;
use App\Http\Requests\Pharmacy\MedicineRequest;
use App\Http\Requests\Pharmacy\UpdateInventoryRequest;
use App\Models\InventoryLog;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Prescription;
use App\Models\StockAlert;
use App\Repositories\PharmacyRepository;
use App\Services\Pharmacy\DispenseService;
use App\Services\Pharmacy\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    protected DispenseService $dispenseService;
    protected InventoryService $inventoryService;
    protected PharmacyRepository $pharmacyRepository;

    public function __construct(
        DispenseService $dispenseService,
        InventoryService $inventoryService,
        PharmacyRepository $pharmacyRepository
    ) {
        $this->dispenseService = $dispenseService;
        $this->inventoryService = $inventoryService;
        $this->pharmacyRepository = $pharmacyRepository;
    }

    /**
     * Pharmacy Dashboard
     */
    public function dashboard()
    {
        $stats = $this->pharmacyRepository->getDashboardStats();

        return view('pharmacy.dashboard', [
            'stats' => $stats,
            'alerts' => $stats['inventory_alerts'],
            'recentDispenses' => $stats['recent_dispenses'],
        ]);
    }

    public function getStats()
    {
        $today = now()->format('Y-m-d');

        $stats = [
            'total' => Prescription::count(),
            'pending' => Prescription::where('status', 'pending')->count(),
            'completed' => Prescription::where('status', 'dispensed')->count(),
            'cancelled' => Prescription::where('status', 'cancelled')->count(),
            'today_dispensed' => Prescription::whereDate('dispensed_at', $today)->count(),
            'completed_today' => Prescription::whereDate('created_at', $today)
                ->where('status', 'dispensed')
                ->count(),
        ];

        return response()->json(['stats' => $stats]);
    }

    /**
     * Pending Prescriptions
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'priority']);
        $prescriptions = $this->dispenseService->getPendingPrescriptions($filters);

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function getPrescriptions(Request $request)
    {
        try {
            Log::info('getPrescriptions', $request->all());
            $query = Prescription::with([
                'diagnosis.visit.patient',
                'medicine',
                'prescriber'
            ]);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas(
                        'diagnosis.visit.patient',
                        function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('emrn', 'like', "%{$search}%");
                        }
                    )
                        ->orWhereHas(
                            'medicine',
                            function ($q2) use ($search) {
                                $q2->where('name', 'like', "%{$search}%")
                                    ->orWhere('generic_name', 'like', "%{$search}%");
                            }
                        )
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Get paginated results
            $perPage = $request->get('per_page', 10);
            if ($perPage == 'All') {
                $perPage = $query->count();
            }
            $prescriptions = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform the data for the frontend
            $transformed = $prescriptions->getCollection()->map(function ($prescription) {
                $patient = $prescription->diagnosis->visit->patient ?? null;
                $medicine = $prescription->medicine ?? null;

                // Check stock availability
                $availableStock = $medicine ? $medicine->stock : 0;
                $stockStatus = 'out_of_stock';
                if ($availableStock >= $prescription->quantity) {
                    $stockStatus = 'in_stock';
                } elseif ($availableStock > 0) {
                    $stockStatus = 'low_stock';
                }

                return [
                    'id' => $prescription->id,
                    'patient_name' => $patient ? $patient->name : 'N/A',
                    'emrn' => $patient ? $patient->emrn : 'N/A',
                    'age' => $patient && $patient->dob ?
                        Carbon::parse($patient->dob)->age . ' years' : 'N/A',
                    'medicine_name' => $medicine ? $medicine->name : 'N/A',
                    'generic_name' => $medicine ? $medicine->generic_name : '',
                    'dosage' => $prescription->dosage,
                    'frequency' => $prescription->frequency,
                    'duration' => $prescription->duration,
                    'instructions' => $prescription->instructions,
                    'quantity' => $prescription->quantity,
                    'status' => $prescription->status,
                    'priority' => $prescription->priority,
                    'available_stock' => $availableStock,
                    'stock_status' => $stockStatus,
                    'doctor_name' => $prescription->prescriber->name ?? 'N/A',
                    'created_at' => $prescription->created_at->toISOString(),
                    'dispensed_at' => $prescription->dispensed_at ?
                        $prescription->dispensed_at->toISOString() : null,
                    'refills_allowed' => $prescription->refills_allowed,
                    'refills_used' => $prescription->refills_used,
                ];
            });

            return response()->json([
                'data' => $transformed,
                'current_page' => $prescriptions->currentPage(),
                'from' => $prescriptions->firstItem(),
                'to' => $prescriptions->lastItem(),
                'total' => $prescriptions->total(),
                'last_page' => $prescriptions->lastPage(),
                'links' => $this->generatePaginationLinks($prescriptions),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch prescriptions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generatePaginationLinks($paginator)
    {
        $links = [];

        // Page links
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        // Show limited pagination (first, last, and pages around current)
        if ($lastPage <= 7) {
            for ($i = 1; $i <= $lastPage; $i++) {
                $links[] = [
                    'url' => $paginator->url($i),
                    'label' => (string)$i,
                    'active' => $i === $currentPage
                ];
            }
        } else {
            // Always show first page
            $links[] = [
                'url' => $paginator->url(1),
                'label' => '1',
                'active' => 1 === $currentPage
            ];

            // Show dots if needed
            if ($currentPage > 3) {
                $links[] = [
                    'url' => null,
                    'label' => '...',
                    'active' => false
                ];
            }

            // Show pages around current
            $start = max(2, $currentPage - 1);
            $end = min($lastPage - 1, $currentPage + 1);

            for ($i = $start; $i <= $end; $i++) {
                $links[] = [
                    'url' => $paginator->url($i),
                    'label' => (string)$i,
                    'active' => $i === $currentPage
                ];
            }

            // Show dots if needed
            if ($currentPage < $lastPage - 2) {
                $links[] = [
                    'url' => null,
                    'label' => '...',
                    'active' => false
                ];
            }

            // Always show last page
            $links[] = [
                'url' => $paginator->url($lastPage),
                'label' => (string)$lastPage,
                'active' => $lastPage === $currentPage
            ];
        }

        return $links;
    }

    /**
     * Show Prescription Details
     */
    public function show(Prescription $prescription)
    {
        $prescription->load([
            'diagnosis.visit.patient',
            'medicine',
            'prescriber'
        ]);

        $inventoryLogs = $this->pharmacyRepository->getInventoryHistory($prescription->medicine);

        return view('pharmacy.prescriptions.show', compact('prescription', 'inventoryLogs'));
    }

    /**
     * Dispense Medicine
     */
    /**
     * Dispense Medicine - JSON API version
     */
    public function dispense(DispenseRequest $request, Prescription $prescription)
    {
        try {
            Log::info('DispenseRequest: Processing dispense for prescription ID: ' . $prescription->id);
            Log::info('DispenseRequest data: ', $request->validated());

            $result = $this->dispenseService->dispensePrescription($prescription, $request->validated());

            $response = [
                'success' => true,
                'message' => "{$prescription->medicine->name} dispensed successfully.",
                'prescription' => [
                    'id' => $prescription->id,
                    'status' => $prescription->status,
                    'dispensed_at' => $prescription->dispensed_at ? $prescription->dispensed_at->toISOString() : null,
                    'dispensed_quantity' => $prescription->dispensed_quantity,
                    'remaining_quantity' => $prescription->quantity - $prescription->dispensed_quantity,
                ],
                'medicine' => [
                    'id' => $prescription->medicine->id,
                    'name' => $prescription->medicine->name,
                    'stock' => $prescription->medicine->stock,
                    'stock_status' => $prescription->medicine->stock <= $prescription->medicine->reorder_level ? 'low' : 'normal'
                ]
            ];

            // Add low stock warning if applicable
            if ($result['low_stock'] ?? false) {
                $response['warning'] = "Low stock alert! Current stock: {$result['medicine']->stock}";
                $response['low_stock'] = true;
                $response['message'] .= " Low stock alert!";
            }

            // Add any additional data from the service
            if (isset($result['additional_data'])) {
                $response['additional_data'] = $result['additional_data'];
            }

            Log::info('DispenseRequest: Successfully dispensed prescription ID: ' . $prescription->id);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('DispenseRequest failed: ' . $e->getMessage(), [
                'prescription_id' => $prescription->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to dispense medication: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing the dispense.'
            ], 500);
        } catch (\Throwable $e) {
            Log::error('DispenseRequest failed (Throwable): ' . $e->getMessage(), [
                'prescription_id' => $prescription->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to dispense medication: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
            ], 500);
        }
    }


    /**
     * Inventory Management - Main View
     * This now only loads categories and passes initial filter values
     */
    public function inventory(Request $request)
    {
        // Get categories for filters
        $categories = MedicineCategory::whereIsActive(1)
            ->orderBy('display_order')
            ->select(['id', 'name'])
            ->get();

        // Pass initial filter values from URL to Alpine.js
        $initialFilters = [
            'length' => $request->input('length', 16),
            'category' => $request->input('category', 'All'),
            'stock_status' => $request->input('stock_status', 'All'),
            'sort_by' => $request->input('sort_by', 'name'),
            'sort_direction' => $request->input('sort_direction', 'asc'),
        ];

        return view('pharmacy.inventory.index', [
            'categories' => $categories,
            'initialFilters' => json_encode($initialFilters),
        ]);
    }

    /**
     * AJAX endpoint for ALL inventory data
     * Used for both initial page load and filtered requests
     */
    public function inventoryList(Request $request)
    {
        try {

            Log::info('InventoryListRequest: INSIDE ', request()->all());
            // Get filters from request
            $filters = [
                'length' => $request->input('length', 16),
                'search' => $request->input('search', ''),
                'category' => $request->input('category', 'All'),
                'stock_status' => $request->input('stock_status', 'All'),
                'sort_by' => $request->input('sort_by', 'name'),
                'sort_direction' => $request->input('sort_direction', 'asc'),
            ];

            // Get paginated medicines from repository
            $medicines = $this->pharmacyRepository->getMedicines($filters);

            // Transform data for JSON response
            $data = $medicines->map(function ($medicine) {
                $percentage = $medicine->reorder_level > 0
                    ? min(100, ($medicine->stock / $medicine->reorder_level) * 100)
                    : 0;

                $color = $medicine->stock == 0 ? 'bg-rose-500' : ($medicine->stock <= $medicine->reorder_level ? 'bg-orange-500' : 'bg-emerald-500');

                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'code' => $medicine->code,
                    'requires_prescription' => $medicine->requires_prescription,
                    'stock' => $medicine->stock,
                    'reorder_level' => $medicine->reorder_level,
                    'strength' => $medicine->strength,
                    'form' => $medicine->form,
                    'category_name' => $medicine->category->name ?? 'Uncategorized',
                    'brand' => $medicine->brand,
                    'expiry_date' => $medicine->expiry_date ? Carbon::parse($medicine->expiry_date)->format('d M Y') : null,
                    'is_about_to_expire' => $medicine->isAboutToExpire(),
                    'supplier_name' => $medicine->supplier ? $medicine->supplier->name : null,
                    'view_url' => route('pharmacy.inventory.show', $medicine),
                    'edit_url' => route('pharmacy.inventory.edit', $medicine),
                    'stock_percentage' => $percentage,
                    'stock_color' => $color,
                ];
            });

            $lowStockCount = $medicines->filter(function ($medicine) {
                return $medicine->stock > 0 && $medicine->stock <= $medicine->reorder_level;
            })->count();

            $outOfStockCount = $medicines->filter(function ($medicine) {
                return $medicine->stock <= 0;
            })->count();

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $medicines->currentPage(),
                    'last_page' => $medicines->lastPage(),
                    'per_page' => $medicines->perPage(),
                    'total' => $medicines->total(),
                    'links' => $medicines->links()->toHtml(),
                ],
                'stats' => [
                    'total' => $medicines->total(),
                    'low_stock' => $lowStockCount,
                    'out_of_stock' => $outOfStockCount,
                ],
                'filters' => $filters, // Return applied filters for reference
            ]);
        } catch (\Exception $e) {
            Log::error('Inventory list error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching inventory data',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * API endpoint to get current stock for a medicine
     */
    public function getMedicineStock(Medicine $medicine)
    {
        return response()->json([
            'stock' => $medicine->stock,
            'reorder_level' => $medicine->reorder_level,
            'name' => $medicine->name
        ]);
    }

    /**
     * Show Medicine Details
     */
    public function showMedicine(Medicine $medicine)
    {
        $medicine->load('category');
        $inventoryLogs = $this->pharmacyRepository->getInventoryHistory($medicine);
        $dispenseHistory = $medicine->prescriptions()->where('status', 'dispensed')->latest()->limit(10)->get();

        return view('pharmacy.inventory.show', compact('medicine', 'inventoryLogs', 'dispenseHistory'));
    }

    /**
     * Update Inventory Stock
     */
    public function updateStock(UpdateInventoryRequest $request, Medicine $medicine)
    {
        try {
            $result = $this->inventoryService->updateStock($medicine, $request->validated());

            $message = "Stock updated successfully. New stock: {$medicine->stock}";

            if ($result['low_stock']) {
                $message .= " Low stock alert!";
                return back()->with('success', $message)->with('warning', "Low stock alert for {$medicine->name}");
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Medicine CRUD
     */
    public function createMedicine()
    {
        $categories = MedicineCategory::whereIsActive(1)->orderBy('display_order')->select(['id', 'name'])->get();
        return view('pharmacy.inventory.create', compact('categories'));
    }

    public function storeMedicine(MedicineRequest $request)
    {
        try {
            $validated = $request->validated();

            // Convert category name to category_id if needed
            if (isset($validated['category']) && !isset($validated['category_id'])) {
                $category = MedicineCategory::firstOrCreate(
                    ['name' => $validated['category']],
                    ['slug' => \Str::slug($validated['category'])]
                );
                $validated['category_id'] = $category->id;
                unset($validated['category']);
            }

            // Set default values for boolean fields
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['requires_prescription'] = $validated['requires_prescription'] ?? false;

            $medicine = Medicine::create($validated);

            // Log initial stock (Create Batch)
            if (($validated['stock'] ?? 0) > 0) {
                $batchNumber = $validated['batch_number'] ?? 'BATCH-' . strtoupper(uniqid());

                \App\Models\MedicineBatch::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $batchNumber,
                    'remaining_quantity' => $validated['stock'],
                    'expiry_date' => $validated['expiry_date'] ?? now()->addYear(),
                    'unit_price' => $medicine->unit_price,
                    'sale_price' => $medicine->selling_price,
                    'is_active' => true,
                ]);

                InventoryLog::create([
                    'medicine_id' => $medicine->id,
                    'user_id' => auth()->id(),
                    'type' => 'initial',
                    'quantity' => $validated['stock'],
                    'previous_stock' => 0,
                    'new_stock' => $validated['stock'],
                    'notes' => 'Initial stock entry',
                    'unit_cost' => $medicine->unit_price,
                    'batch_number' => $batchNumber,
                ]);
            }

            return redirect()->route('pharmacy.inventory')
                ->with('success', "Medicine {$medicine->name} added successfully.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function editMedicine(Medicine $medicine)
    {
        $categories = MedicineCategory::whereIsActive(1)->orderBy('display_order')->get();
        return view('pharmacy.inventory.edit', compact('medicine', 'categories'));
    }

    public function updateMedicine(MedicineRequest $request, Medicine $medicine)
    {
        $validated = $request->validated();

        // Handle category conversion
        if (isset($validated['category']) && !isset($validated['category_id'])) {
            $category = MedicineCategory::firstOrCreate(
                ['name' => $validated['category']],
                ['slug' => \Str::slug($validated['category'])]
            );
            $validated['category_id'] = $category->id;
            unset($validated['category']);
        }

        $medicine->update($validated);
        return redirect()->route('pharmacy.inventory.show', $medicine)
            ->with('success', "Medicine updated successfully.");
    }

    /**
     * Dispense History
     */
    public function dispenseHistory(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'medicine_id']);
        $dispenses = $this->pharmacyRepository->getDispenseHistory($filters);
        $medicines = Medicine::active()->orderBy('name')->get();

        Log::info(json_encode($dispenses));
        Log::info(json_encode($medicines));

        return view('pharmacy.dispenses.history', compact('dispenses', 'medicines'));
    }

    /**
     * Reports
     */

    public function reports()
    {
        $metrics = $this->inventoryService->getInventoryMetrics();
        $lowStock = $this->inventoryService->getLowStockMedicines();
        $expiringSoon = $this->inventoryService->getExpiringMedicines();

        return view('pharmacy.reports.index', compact('metrics', 'lowStock', 'expiringSoon'));
    }

    /**
     * Stock Alerts Management
     */
    public function alerts()
    {
        $alerts = StockAlert::with('medicine')
            ->where('is_resolved', false)
            ->latest()
            ->paginate(20);

        return view('pharmacy.alerts.index', compact('alerts'));
    }

    /**
     * Resolve Stock Alert
     */
    public function resolveAlert(Request $request, StockAlert $alert)
    {
        $request->validate([
            'resolution_notes' => 'required|string|max:500',
        ]);

        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        return back()->with('success', 'Alert resolved successfully.');
    }
}
