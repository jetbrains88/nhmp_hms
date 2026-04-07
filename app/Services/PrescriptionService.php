<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\PrescriptionDispensation;
use App\Models\MedicineBatch;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrescriptionService
{
    protected $inventoryService;
    
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    
    /**
     * Create a new prescription
     */
    public function createPrescription(array $data, int $doctorId, int $branchId): Prescription
    {
        return Prescription::create([
            'uuid' => (string) Str::uuid(),
            'branch_id' => $branchId,
            'diagnosis_id' => $data['diagnosis_id'],
            'medicine_id' => $data['medicine_id'],
            'prescribed_by' => $doctorId,
            'dosage' => $data['dosage'],
            'frequency' => ($data['morning'] ?? 0) + ($data['evening'] ?? 0) + ($data['night'] ?? 0),
            'morning' => $data['morning'] ?? 0,
            'evening' => $data['evening'] ?? 0,
            'night' => $data['night'] ?? 0,
            'days' => $data['days'],
            'quantity' => $data['quantity'],
            'status' => 'pending',
            'instructions' => $data['instructions'] ?? null,
        ]);
    }
    
    /**
     * Dispense a prescription (Supports Multi-Batch FIFO)
     */
    public function dispensePrescription(Prescription $prescription, int $pharmacistId, array $data): array
    {
        return DB::transaction(function () use ($prescription, $pharmacistId, $data) {
            // 1. Validation
            if ($prescription->status === 'completed') {
                throw new \InvalidArgumentException('Prescription already fully dispensed');
            }
            
            if ($prescription->status === 'cancelled') {
                throw new \InvalidArgumentException('Prescription is cancelled');
            }
            
            $quantityToDispense = $data['quantity_dispensed'] ?? $prescription->remaining_quantity;
            
            if ($quantityToDispense <= 0) {
                throw new \InvalidArgumentException('Invalid quantity to dispense');
            }

            if ($quantityToDispense > $prescription->remaining_quantity) {
                throw new \InvalidArgumentException("Cannot dispense more than remaining ({$prescription->remaining_quantity})");
            }
            
            // 2. Batch Selection Logic (Prioritize Assigned -> FIFO)
            $availableBatches = $this->getAvailableBatches($prescription->medicine_id, $prescription->branch_id);
            $selectedBatches = [];
            $remainingToPull = $quantityToDispense;

            // If a specific batch was assigned, pull from it first
            if (!empty($data['medicine_batch_id'])) {
                $assignedBatch = $availableBatches->firstWhere('id', $data['medicine_batch_id']);
                if (!$assignedBatch) {
                    throw new \InvalidArgumentException('Selected batch is unavailable or empty');
                }
                
                $pullAmount = min($remainingToPull, $assignedBatch->remaining_quantity);
                $selectedBatches[] = ['batch' => $assignedBatch, 'quantity' => $pullAmount];
                $remainingToPull -= $pullAmount;
            }

            // Pull remainder from other batches using FIFO (Expiry Date)
            if ($remainingToPull > 0) {
                foreach ($availableBatches as $batch) {
                    // Skip if this was the already partially used assigned batch
                    if (isset($assignedBatch) && $batch->id === $assignedBatch->id) continue;
                    
                    if ($batch->remaining_quantity <= 0) continue;

                    $pullAmount = min($remainingToPull, $batch->remaining_quantity);
                    $selectedBatches[] = ['batch' => $batch, 'quantity' => $pullAmount];
                    $remainingToPull -= $pullAmount;

                    if ($remainingToPull <= 0) break;
                }
            }

            if ($remainingToPull > 0) {
                throw new \InvalidArgumentException('Insufficient total stock available across all batches');
            }
            
            // 3. Execution (Create Dispenations & Update Inventory)
            $dispensations = [];
            foreach ($selectedBatches as $item) {
                $batch = $item['batch'];
                $qty = $item['quantity'];

                $dispensation = PrescriptionDispensation::create([
                    'uuid' => (string) Str::uuid(),
                    'prescription_id' => $prescription->id,
                    'quantity_dispensed' => $qty,
                    'dispensed_by' => $pharmacistId,
                    'dispensed_at' => now(),
                    'medicine_batch_id' => $batch->id,
                    'alternative_medicine_id' => $data['alternative_medicine_id'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $this->inventoryService->removeStock(
                    $batch,
                    $qty,
                    $pharmacistId,
                    'dispense',
                    "Dispensed for prescription #{$prescription->id}",
                    $dispensation
                );

                $dispensations[] = $dispensation;
            }
            
            // 4. Update Prescription Status
            $totalDispensedNow = collect($selectedBatches)->sum('quantity');
            $newRemaining = $prescription->remaining_quantity - $totalDispensedNow;
            
            if ($newRemaining <= 0) {
                $prescription->update(['status' => 'completed']);
            } else {
                $prescription->update(['status' => 'partially_dispensed']);
            }
            
            return $dispensations;
        });
    }
    
    /**
     * Get all active, in-stock batches for a medicine (FEFO)
     */
    protected function getAvailableBatches(int $medicineId, int $branchId)
    {
        return MedicineBatch::where('medicine_id', $medicineId)
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '>', now())
            ->where('is_active', true)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }
    
    /**
     * Cancel a prescription
     */
    public function cancelPrescription(Prescription $prescription, string $reason): Prescription
    {
        if ($prescription->status === 'completed') {
            throw new \InvalidArgumentException('Cannot cancel a completed prescription');
        }
        
        $prescription->update([
            'status' => 'cancelled',
        ]);
        
        return $prescription;
    }
    
    /**
     * Get dispensing history for a prescription
     */
    public function getDispensingHistory(Prescription $prescription)
    {
        return $prescription->dispensations()
            ->with(['dispensedBy', 'medicineBatch'])
            ->orderBy('dispensed_at', 'desc')
            ->get();
    }
    
    /**
     * Get pending prescriptions for a branch
     */
    public function getPendingPrescriptions(int $branchId)
    {
        return Prescription::with(['diagnosis.visit.patient', 'medicine', 'prescribedBy'])
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'partially_dispensed'])
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    /**
     * Get prescription statistics
     */
    public function getStats(int $branchId): array
    {
        return [
            'pending' => Prescription::where('branch_id', $branchId)
                ->where('status', 'pending')
                ->count(),
            'partially_dispensed' => Prescription::where('branch_id', $branchId)
                ->where('status', 'partially_dispensed')
                ->count(),
            'completed_today' => Prescription::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
            'total_dispensed_today' => PrescriptionDispensation::whereHas('prescription', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->whereDate('dispensed_at', today())
                ->sum('quantity_dispensed'),
        ];
    }
}