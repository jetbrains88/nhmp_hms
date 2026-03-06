<?php

namespace App\Services\Pharmacy;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Prescription;
use App\Models\PrescriptionDispensation;
use App\Models\StockAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Get inventory metrics for reports
     */
    public function getInventoryMetrics(): array
    {
        try {
            // Total medicines count
            $totalMedicines = Medicine::count();

            // Total categories count
            $totalCategories = \App\Models\MedicineCategory::count();

            // Low stock count - using the view if available, otherwise fallback
            try {
                $lowStockCount = DB::table('medicine_stock_value')
                    ->where('stock_status', 'low_stock')
                    ->count();
            } catch (\Exception $e) {
                // Fallback calculation
                $lowStockCount = Medicine::whereHas('batches', function ($query) {
                    $query->select(DB::raw('SUM(remaining_quantity) as total_stock'))
                        ->having('total_stock', '>', 0)
                        ->having('total_stock', '<=', DB::raw('medicines.reorder_level'));
                })->count();
            }

            // Out of stock count
            try {
                $outOfStockCount = DB::table('medicine_stock_value')
                    ->where('stock', 0)
                    ->count();
            } catch (\Exception $e) {
                // Fallback calculation
                $outOfStockCount = Medicine::whereDoesntHave('batches', function ($query) {
                    $query->where('remaining_quantity', '>', 0);
                })->count();
            }

            // Total inventory value
            $totalValue = MedicineBatch::where('remaining_quantity', '>', 0)
                ->sum(DB::raw('remaining_quantity * unit_price')) ?? 0;

            // Average stock turnover (days) - this is a complex calculation
            // For now, we'll use a placeholder
            $avgStockTurnover = 30; // Placeholder value

            // Total dispenses this month - FIXED: Use prescription_dispensations
            $totalDispenses = PrescriptionDispensation::whereMonth('dispensed_at', now()->month)
                ->whereYear('dispensed_at', now()->year)
                ->count();

            // Monthly revenue - FIXED: Use prescription_dispensations with medicine_batches
            $monthlyRevenue = PrescriptionDispensation::join('medicine_batches', 'prescription_dispensations.medicine_batch_id', '=', 'medicine_batches.id')
                ->whereMonth('prescription_dispensations.dispensed_at', now()->month)
                ->whereYear('prescription_dispensations.dispensed_at', now()->year)
                ->sum(DB::raw('prescription_dispensations.quantity_dispensed * medicine_batches.sale_price')) ?? 0;

            return [
                'total_medicines' => $totalMedicines,
                'total_categories' => $totalCategories,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
                'total_value' => number_format($totalValue, 2),
                'avg_stock_turnover' => $avgStockTurnover,
                'total_dispenses' => $totalDispenses,
                'monthly_revenue' => $monthlyRevenue,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting inventory metrics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_medicines' => 0,
                'total_categories' => 0,
                'low_stock_count' => 0,
                'out_of_stock_count' => 0,
                'total_value' => '0.00',
                'avg_stock_turnover' => 0,
                'total_dispenses' => 0,
                'monthly_revenue' => 0,
            ];
        }
    }

    /**
     * Get low stock medicines
     */
    public function getLowStockMedicines()
    {
        try {
            return DB::table('medicine_stock_value')
                ->where('stock_status', 'low_stock')
                ->where('stock', '>', 0)
                ->select('name', 'stock', 'reorder_level as min_stock_level', 'unit')
                ->orderBy('stock')
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            // Fallback query
            return Medicine::whereHas('batches', function ($query) {
                $query->select(DB::raw('SUM(remaining_quantity) as total_stock'))
                    ->having('total_stock', '>', 0)
                    ->having('total_stock', '<=', DB::raw('medicines.reorder_level'));
            })
                ->with('category')
                ->limit(20)
                ->get()
                ->map(function ($medicine) {
                    return (object)[
                        'name' => $medicine->name,
                        'stock' => $medicine->stock,
                        'min_stock_level' => $medicine->reorder_level,
                        'unit' => $medicine->unit,
                        'category' => $medicine->category?->name,
                    ];
                });
        }
    }

    /**
     * Get expiring medicines
     */
    public function getExpiringMedicines()
    {
        return MedicineBatch::with('medicine')
            ->whereDate('expiry_date', '<=', now()->addMonths(3))
            ->whereDate('expiry_date', '>', now())
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();
    }

    /**
     * Update stock for a medicine
     */
    public function updateStock(Medicine $medicine, array $data)
    {
        try {
            DB::beginTransaction();

            $oldStock = $medicine->stock;
            $quantity = $data['quantity'];
            $action = $data['action'];

            // Calculate new stock based on action
            $newStock = match ($action) {
                'add' => $oldStock + $quantity,
                'remove' => $oldStock - $quantity,
                'adjust' => $quantity, // Direct adjustment
                default => $oldStock,
            };

            // Ensure non-negative stock
            if ($newStock < 0) {
                throw new \Exception('Stock cannot be negative');
            }

            // Update or create batch
            if (!empty($data['batch_number'])) {
                $batch = MedicineBatch::updateOrCreate(
                    [
                        'medicine_id' => $medicine->id,
                        'batch_number' => $data['batch_number'],
                    ],
                    [
                        'expiry_date' => $data['expiry_date'] ?? now()->addYear(),
                        'unit_price' => $data['unit_cost'] ?? $medicine->unit_price,
                        'sale_price' => $medicine->selling_price,
                        'remaining_quantity' => $action === 'add' ? $quantity : $medicine->batches()->sum('remaining_quantity'),
                        'is_active' => true,
                    ]
                );
            }

            // Log the inventory change
            \App\Models\InventoryLog::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => $action,
                'quantity' => $action === 'remove' ? -$quantity : $quantity,
                'previous_stock' => $oldStock,
                'new_stock' => $newStock,
                'notes' => $data['notes'] ?? null,
                'batch_number' => $data['batch_number'] ?? null,
                'unit_cost' => $data['unit_cost'] ?? $medicine->unit_price,
            ]);

            // Check if we need to create a stock alert
            if ($newStock <= $medicine->reorder_level && $newStock > 0) {
                StockAlert::create([
                    'branch_id' => auth()->user()->current_branch_id ?? $medicine->branch_id ?? 1,
                    'medicine_id' => $medicine->id,
                    'alert_type' => 'low_stock',
                    'message' => "{$medicine->name} is low on stock. Current stock: {$newStock}, Reorder level: {$medicine->reorder_level}",
                    'is_resolved' => false,
                ]);
            } elseif ($newStock == 0) {
                StockAlert::create([
                    'branch_id' => auth()->user()->current_branch_id ?? $medicine->branch_id ?? 1,
                    'medicine_id' => $medicine->id,
                    'alert_type' => 'out_of_stock',
                    'message' => "{$medicine->name} is out of stock.",
                    'is_resolved' => false,
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'low_stock' => $newStock <= $medicine->reorder_level,
                'medicine' => $medicine->fresh(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * Synchronize stock alerts for all medicines
     */
    public function syncStockAlerts(): int
    {
        $count = 0;
        $medicines = Medicine::active()->get();

        foreach ($medicines as $medicine) {
            $stock = (int) $medicine->stock;
            $reorderLevel = (int) $medicine->reorder_level;

            if ($stock <= $reorderLevel) {
                $alertType = $stock == 0 ? 'out_of_stock' : 'low_stock';
                $message = $stock == 0
                    ? "{$medicine->name} is out of stock."
                    : "{$medicine->name} is low on stock. Current stock: {$stock}, Reorder level: {$reorderLevel}";

                // Check if an unresolved alert already exists
                $existingAlert = StockAlert::where('medicine_id', $medicine->id)
                    ->where('is_resolved', false)
                    ->first();

                if ($existingAlert) {
                    // Update if alert type or message changed
                    if ($existingAlert->alert_type !== $alertType || $existingAlert->message !== $message) {
                        $existingAlert->update([
                            'alert_type' => $alertType,
                            'message' => $message,
                        ]);
                        $count++;
                    }
                } else {
                    // Create new alert
                    StockAlert::create([
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'branch_id' => $medicine->branch_id ?? 1,
                        'medicine_id' => $medicine->id,
                        'alert_type' => $alertType,
                        'message' => $message,
                        'is_resolved' => false,
                    ]);
                    $count++;
                }
            }
        }

        return $count;
    }
}
