<?php

namespace App\Services\Pharmacy;

use App\Models\InventoryLog;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Prescription;
use App\Models\StockAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispenseService
{
    public function dispensePrescription(Prescription $prescription, array $data): array
    {
        return DB::transaction(function () use ($prescription, $data) {
            $medicine = $prescription->medicine;
            Log::info('DispenseService::dispensePrescription', $data);

            $quantityToDispense = $data['dispensed_quantity'];

            // Validate total stock
            if ($medicine->stock < $quantityToDispense) {
                throw new \Exception("Insufficient stock. Available: {$medicine->stock}, Required: {$quantityToDispense}");
            }

            $remainingToDispense = $quantityToDispense;
            $previousStock = $medicine->stock;

            // Update prescription (do this once)
            $prescription->update([
                'status' => 'dispensed',
                'dispensed_quantity' => $quantityToDispense,
                'dispensed_by' => auth()->id(),
                'dispensed_at' => now(),
                'dispense_notes' => $data['dispense_notes'] ?? null,
                'batch_number' => $data['batch_number'] ?? null,
            ]);

            // Deduction Logic
            $query = $medicine->batches()
                ->where('remaining_quantity', '>', 0)
                ->where('is_active', true)
                ->orderBy('expiry_date', 'asc');

            // 1. Try specific batch if provided
            if (!empty($data['batch_number'])) {
                $specificBatch = (clone $query)->where('batch_number', $data['batch_number'])->first();
                if ($specificBatch) {
                    $deduct = min($remainingToDispense, $specificBatch->remaining_quantity);
                    $this->deductFromBatch($medicine, $specificBatch, $deduct, $prescription, $previousStock);

                    $remainingToDispense -= $deduct;
                    $previousStock -= $deduct;
                }
            }

            // 2. Follow FEFO for remaining
            if ($remainingToDispense > 0) {
                $batches = $query->get();
                foreach ($batches as $batch) {
                    if ($remainingToDispense <= 0)
                        break;

                    $deduct = min($remainingToDispense, $batch->remaining_quantity);
                    $this->deductFromBatch($medicine, $batch, $deduct, $prescription, $previousStock);

                    $remainingToDispense -= $deduct;
                    $previousStock -= $deduct;
                }
            }

            if ($remainingToDispense > 0) {
                throw new \Exception("Failed to satisfy dispense quantity from available batches.");
            }

            // Check for low stock and create/update alert
            if ($medicine->stock <= $medicine->reorder_level) {
                $this->updateStockAlert($medicine);
            }

            return [
                'success' => true,
                'prescription' => $prescription,
                'medicine' => $medicine,
                'low_stock' => $medicine->stock <= $medicine->reorder_level
            ];
        });
    }

    private function deductFromBatch(Medicine $medicine, MedicineBatch $batch, float $quantity, Prescription $prescription, int $previousTotalStock): void
    {
        $batch->decrement('remaining_quantity', $quantity);
        if ($batch->remaining_quantity <= 0) {
            $batch->update(['is_active' => false]);
        }

        // Log inventory change
        InventoryLog::create([
            'medicine_id' => $medicine->id,
            'medicine_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'dispense',
            'quantity' => $quantity,
            'previous_stock' => $previousTotalStock,
            'new_stock' => $previousTotalStock - $quantity,
            'reference_id' => $prescription->id,
            'reference_type' => Prescription::class ,
            'notes' => 'Dispensed from batch ' . $batch->batch_number . ' for prescription #' . $prescription->id,
            'batch_number' => $batch->batch_number,
        ]);
    }

    private function updateStockAlert(Medicine $medicine): void
    {
        $alertType = $medicine->stock == 0 ? 'out_of_stock' : 'low_stock';
        $message = $medicine->stock == 0
            ? "{$medicine->name} is out of stock. Current stock: 0"
            : "{$medicine->name} is low on stock. Current stock: {$medicine->stock}, Reorder level: {$medicine->reorder_level}";

        // Check if alert already exists and is unresolved
        $existingAlert = StockAlert::where('medicine_id', $medicine->id)
            ->where('is_resolved', false)
            ->first();

        if ($existingAlert) {
            // Update existing alert if type changed
            if ($existingAlert->alert_type !== $alertType) {
                $existingAlert->update([
                    'alert_type' => $alertType,
                    'message' => $message,
                    'updated_at' => now(),
                ]);
            }
        }
        else {
            // Create new alert
            StockAlert::create([
                'branch_id' => auth()->user()->current_branch_id ?? $medicine->branch_id ?? 1,
                'medicine_id' => $medicine->id,
                'alert_type' => $alertType,
                'message' => $message,
                'is_resolved' => false,
            ]);
        }
    }

    public function getPendingPrescriptions(array $filters = [])
    {
        $query = Prescription::where('status', 'pending')
            ->with([
            'diagnosis.visit.patient',
            'medicine',
            'prescriber'
        ])
            ->latest();

        // Apply filters
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('diagnosis.visit.patient', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('emrn', 'like', "%{$search}%");
                    }
                    )
                        ->orWhereHas('medicine', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                }
                );
            });
        }

        return $query->paginate(20);
    }
}
