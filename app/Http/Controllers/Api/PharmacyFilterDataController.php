<?php
// app/Http/Controllers/Api/FilterDataController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineForm;
use App\Models\User;
use Illuminate\Http\Request;

class PharmacyFilterDataController extends Controller
{
    /**
     * Get all filter data for medicine/Pharmacy filters
     */
    public function getMedicineFilterData(Request $request)
    {
        try {
            $branchId = auth()->user()->current_branch_id;

            // Get medicine categories with active medicines count
            $categories = MedicineCategory::where('is_active', true)
                ->withCount(['medicines' => function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                }])
                ->orderBy('name')
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'medicines_count' => $category->medicines_count,
                        'icon' => $this->getCategoryIcon($category->name)
                    ];
                });

            // Get all medicines with their details
            $medicines = Medicine::where(function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                })
                ->with(['category', 'form'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function($medicine) {
                    return [
                        'id' => $medicine->id,
                        'name' => $medicine->name,
                        'generic_name' => $medicine->generic_name,
                        'brand' => $medicine->brand,
                        'category_id' => $medicine->category_id,
                        'category_name' => $medicine->category?->name,
                        'form_id' => $medicine->form_id,
                        'form_name' => $medicine->form?->name,
                        'manufacturer' => $medicine->manufacturer,
                        'strength' => $medicine->strength_value ? 
                            $medicine->strength_value . ' ' . $medicine->strength_unit : null,
                        'requires_prescription' => $medicine->requires_prescription,
                        'display_name' => $medicine->name . 
                            ($medicine->strength_value ? ' (' . $medicine->strength_value . ' ' . $medicine->strength_unit . ')' : '') .
                            ($medicine->generic_name ? ' - ' . $medicine->generic_name : '')
                    ];
                });

            // Get medicine forms with count
            $forms = MedicineForm::withCount(['medicines' => function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                }])
                ->orderBy('name')
                ->get()
                ->map(function($form) {
                    return [
                        'id' => $form->id,
                        'name' => $form->name,
                        'medicines_count' => $form->medicines_count
                    ];
                });

            // Get pharmacists (users with pharmacy role)
            $pharmacists = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['pharmacy', 'pharmacist', 'admin']);
                })
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            // Get manufacturers (unique list)
            $manufacturers = Medicine::where(function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                })
                ->whereNotNull('manufacturer')
                ->where('manufacturer', '!=', '')
                ->distinct()
                ->orderBy('manufacturer')
                ->pluck('manufacturer')
                ->map(function($manufacturer) {
                    return [
                        'name' => $manufacturer,
                        'medicines_count' => Medicine::where('manufacturer', $manufacturer)->count()
                    ];
                });

            // Get batch numbers with expiry info
            $batches = \App\Models\MedicineBatch::whereHas('medicine', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                })
                ->where('is_active', true)
                ->orderBy('batch_number')
                ->get(['id', 'batch_number', 'medicine_id', 'expiry_date', 'remaining_quantity'])
                ->map(function($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'medicine_id' => $batch->medicine_id,
                        'expiry_date' => $batch->expiry_date,
                        'expiry_status' => $this->getExpiryStatus($batch->expiry_date),
                        'stock' => $batch->remaining_quantity
                    ];
                });

            // Get unique strength units for filtering
            $strengthUnits = Medicine::where(function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                })
                ->whereNotNull('strength_unit')
                ->distinct()
                ->pluck('strength_unit');

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'medicines' => $medicines,
                    'forms' => $forms,
                    'pharmacists' => $pharmacists,
                    'manufacturers' => $manufacturers,
                    'batches' => $batches,
                    'strength_units' => $strengthUnits,
                    'filters' => [
                        'prescription_statuses' => [
                            ['value' => 'pending', 'label' => 'Pending', 'color' => 'yellow'],
                            ['value' => 'partially_dispensed', 'label' => 'Partially Dispensed', 'color' => 'blue'],
                            ['value' => 'completed', 'label' => 'Completed', 'color' => 'green'],
                            ['value' => 'cancelled', 'label' => 'Cancelled', 'color' => 'red'],
                        ],
                        'expiry_statuses' => [
                            ['value' => 'expired', 'label' => 'Expired', 'color' => 'red'],
                            ['value' => 'expiring_soon', 'label' => 'Expiring Soon (30 days)', 'color' => 'orange'],
                            ['value' => 'valid', 'label' => 'Valid', 'color' => 'green'],
                        ],
                        'stock_statuses' => [
                            ['value' => 'in_stock', 'label' => 'In Stock', 'color' => 'green'],
                            ['value' => 'low_stock', 'label' => 'Low Stock', 'color' => 'orange'],
                            ['value' => 'out_of_stock', 'label' => 'Out of Stock', 'color' => 'red'],
                        ],
                        'sort_options' => [
                            ['value' => 'dispensed_at', 'label' => 'Dispensed Date'],
                            ['value' => 'medicine_name', 'label' => 'Medicine Name'],
                            ['value' => 'quantity_dispensed', 'label' => 'Quantity'],
                            ['value' => 'patient_name', 'label' => 'Patient Name'],
                            ['value' => 'batch_number', 'label' => 'Batch Number'],
                            ['value' => 'expiry_date', 'label' => 'Expiry Date'],
                            ['value' => 'price', 'label' => 'Price'],
                            ['value' => 'manufacturer', 'label' => 'Manufacturer'],
                        ]
                    ]
                ],
                'meta' => [
                    'total_categories' => $categories->count(),
                    'total_medicines' => $medicines->count(),
                    'total_forms' => $forms->count(),
                    'total_pharmacists' => $pharmacists->count(),
                    'total_manufacturers' => $manufacturers->count(),
                    'total_batches' => $batches->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered medicines by category
     */
    public function getMedicinesByCategory(Request $request, $categoryId)
    {
        try {
            $branchId = auth()->user()->current_branch_id;
            
            $medicines = Medicine::where(function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->orWhere('is_global', true);
                })
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->with(['form', 'category'])
                ->orderBy('name')
                ->get()
                ->map(function($medicine) {
                    return [
                        'id' => $medicine->id,
                        'name' => $medicine->name,
                        'generic_name' => $medicine->generic_name,
                        'form' => $medicine->form?->name,
                        'strength' => $medicine->strength_value ? 
                            $medicine->strength_value . ' ' . $medicine->strength_unit : null,
                        'manufacturer' => $medicine->manufacturer,
                        'batches' => $medicine->batches()
                            ->where('remaining_quantity', '>', 0)
                            ->where('expiry_date', '>', now())
                            ->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $medicines
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching medicines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get batch numbers for a specific medicine
     */
    public function getMedicineBatches(Request $request, $medicineId)
    {
        try {
            $batches = \App\Models\MedicineBatch::where('medicine_id', $medicineId)
                ->where('is_active', true)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('expiry_date')
                ->get()
                ->map(function($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'expiry_date' => $batch->expiry_date,
                        'expiry_status' => $this->getExpiryStatus($batch->expiry_date),
                        'stock' => $batch->remaining_quantity,
                        'unit_price' => $batch->unit_price,
                        'sale_price' => $batch->sale_price
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $batches
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching batches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medicines (for autocomplete)
     */
    public function searchMedicines(Request $request)
    {
        try {
            $branchId = auth()->user()->current_branch_id;
            $search = $request->get('q', '');
            $categoryId = $request->get('category_id');

            $query = Medicine::where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhere('is_global', true);
                })
                ->where('is_active', true);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('generic_name', 'LIKE', "%{$search}%")
                      ->orWhere('brand', 'LIKE', "%{$search}%")
                      ->orWhere('manufacturer', 'LIKE', "%{$search}%");
                });
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $medicines = $query->limit(20)
                ->get()
                ->map(function($medicine) {
                    return [
                        'id' => $medicine->id,
                        'text' => $medicine->name . 
                            ($medicine->strength_value ? ' (' . $medicine->strength_value . ' ' . $medicine->strength_unit . ')' : '') .
                            ($medicine->generic_name ? ' - ' . $medicine->generic_name : ''),
                        'name' => $medicine->name,
                        'generic_name' => $medicine->generic_name,
                        'category_id' => $medicine->category_id,
                        'form_id' => $medicine->form_id,
                        'requires_prescription' => $medicine->requires_prescription
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $medicines
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching medicines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get expiry status
     */
    private function getExpiryStatus($expiryDate)
    {
        $today = now();
        $expiry = \Carbon\Carbon::parse($expiryDate);
        
        if ($expiry < $today) {
            return 'expired';
        } elseif ($expiry <= $today->addDays(30)) {
            return 'expiring_soon';
        }
        return 'valid';
    }

    /**
     * Helper: Get category icon
     */
    private function getCategoryIcon($categoryName)
    {
        $icons = [
            'antibiotic' => 'fa-capsules',
            'painkiller' => 'fa-tablets',
            'vitamin' => 'fa-leaf',
            'injection' => 'fa-syringe',
            'tablet' => 'fa-tablets',
            'syrup' => 'fa-flask',
            'cream' => 'fa-cream',
            'drops' => 'fa-eye-dropper',
            'inhaler' => 'fa-wind',
            'herbal' => 'fa-seedling',
        ];

        foreach ($icons as $key => $icon) {
            if (stripos($categoryName, $key) !== false) {
                return $icon;
            }
        }

        return 'fa-pills';
    }
}