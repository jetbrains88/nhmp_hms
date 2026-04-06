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
     * Dispense a prescription
     */
    public function dispensePrescription(Prescription $prescription, int $pharmacistId, array $data): PrescriptionDispensation
    {
        return DB::transaction(function () use ($prescription, $pharmacistId, $data) {
            // Check if prescription can be dispensed
            if ($prescription->status === 'completed') {
                throw new \InvalidArgumentException('Prescription already fully dispensed');
            }
            
            if ($prescription->status === 'cancelled') {
                throw new \InvalidArgumentException('Prescription is cancelled');
            }
            
            $quantityToDispense = $data['quantity'] ?? $prescription->remaining_quantity;
            
            if ($quantityToDispense <= 0) {
                throw new \InvalidArgumentException('Invalid quantity to dispense');
            }
            
            // Find suitable batch
            $batch = null;
            if (!empty($data['batch_number'])) {
                $batch = MedicineBatch::where('batch_number', $data['batch_number'])
                    ->where('medicine_id', $prescription->medicine_id)
                    ->where('branch_id', $prescription->branch_id)
                    ->where('remaining_quantity', '>=', $quantityToDispense)
                    ->first();
            }
            
            if (!$batch) {
                $batch = $this->findSuitableBatch($prescription->medicine_id, $prescription->branch_id, $quantityToDispense);
            }
            
            if (!$batch) {
                throw new \InvalidArgumentException('Insufficient stock available');
            }
            
            // Create dispensation record
            $dispensation = PrescriptionDispensation::create([
                'uuid' => (string) Str::uuid(),
                'prescription_id' => $prescription->id,
                'quantity_dispensed' => $quantityToDispense,
                'dispensed_by' => $pharmacistId,
                'dispensed_at' => now(),
                'medicine_batch_id' => $batch->id,
                'notes' => $data['notes'] ?? null,
            ]);
            
            // Update stock using inventory service
            $this->inventoryService->removeStock(
                $batch,
                $quantityToDispense,
                $pharmacistId,
                'dispense',
                "Dispensed for prescription #{$prescription->id}",
                $dispensation
            );
            
            // Update prescription status
            $remainingAfter = $prescription->remaining_quantity - $quantityToDispense;
            
            if ($remainingAfter <= 0) {
                $prescription->update(['status' => 'completed']);
            } elseif ($prescription->status === 'pending') {
                $prescription->update(['status' => 'partially_dispensed']);
            }
            
            return $dispensation;
        });
    }
    
    /**
     * Find the most suitable batch for dispensing
     * Uses FIFO (First In, First Out) based on expiry date
     */
    protected function findSuitableBatch(int $medicineId, int $branchId, int $quantityNeeded): ?MedicineBatch
    {
        return MedicineBatch::where('medicine_id', $medicineId)
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>=', $quantityNeeded)
            ->where('expiry_date', '>', now())
            ->where('is_active', true)
            ->orderBy('expiry_date', 'asc') // Use soonest expiring first
            ->first();
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