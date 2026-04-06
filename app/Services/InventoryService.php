<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\InventoryLog;
use App\Models\StockAlert;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Add stock to a medicine batch
     */
    public function addStock(MedicineBatch $batch, int $quantity, int $userId, string $notes = null, string $rcNumber = null): InventoryLog
    {
        return DB::transaction(function () use ($batch, $quantity, $userId, $notes, $rcNumber) {
            $previousStock = $batch->remaining_quantity;
            $newStock = $previousStock + $quantity;
            
            // Update batch stock
            $batch->update([
                'remaining_quantity' => $newStock,
                'is_active' => $newStock > 0
            ]);
            
            // Create inventory log
            $log = InventoryLog::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $batch->branch_id,
                'medicine_id' => $batch->medicine_id,
                'medicine_batch_id' => $batch->id,
                'user_id' => $userId,
                'type' => 'purchase',
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'notes' => $notes,
                'rc_number' => $rcNumber,
            ]);
            
            // Resolve any resolved stock alerts
            $this->resolveStockAlerts($batch->medicine);
            
            return $log;
        });
    }
    
    /**
     * Remove stock from a medicine batch (dispense, adjustment, etc.)
     */
    public function removeStock(MedicineBatch $batch, int $quantity, int $userId, string $type, string $notes = null, $reference = null): InventoryLog
    {
        return DB::transaction(function () use ($batch, $quantity, $userId, $type, $notes, $reference) {
            if ($batch->remaining_quantity < $quantity) {
                throw new \InvalidArgumentException("Insufficient stock. Available: {$batch->remaining_quantity}, Requested: {$quantity}");
            }
            
            $previousStock = $batch->remaining_quantity;
            $newStock = $previousStock - $quantity;
            
            // Update batch stock
            $batch->update([
                'remaining_quantity' => $newStock,
                'is_active' => $newStock > 0
            ]);
            
            // Create inventory log
            $logData = [
                'uuid' => (string) Str::uuid(),
                'branch_id' => $batch->branch_id,
                'medicine_id' => $batch->medicine_id,
                'medicine_batch_id' => $batch->id,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'notes' => $notes,
            ];
            
            // Add polymorphic reference if provided
            if ($reference) {
                $logData['reference_id'] = $reference->id;
                $logData['reference_type'] = get_class($reference);
            }
            
            $log = InventoryLog::create($logData);
            
            // Check and create stock alerts if needed
            $this->checkAndCreateAlerts($batch->medicine);
            
            return $log;
        });
    }
    
    /**
     * Transfer stock between branches
     */
    public function transferStock(MedicineBatch $sourceBatch, int $quantity, int $targetBranchId, int $userId, string $notes = null): array
    {
        return DB::transaction(function () use ($sourceBatch, $quantity, $targetBranchId, $userId, $notes) {
            // Remove from source branch
            $sourceLog = $this->removeStock(
                $sourceBatch, 
                $quantity, 
                $userId, 
                'transfer', 
                "Transfer to branch {$targetBranchId}. " . ($notes ?? '')
            );
            
            // Find or create batch in target branch
            $targetBatch = MedicineBatch::firstOrCreate(
                [
                    'branch_id' => $targetBranchId,
                    'medicine_id' => $sourceBatch->medicine_id,
                    'batch_number' => $sourceBatch->batch_number,
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'expiry_date' => $sourceBatch->expiry_date,
                    'unit_price' => $sourceBatch->unit_price,
                    'sale_price' => $sourceBatch->sale_price,
                    'remaining_quantity' => 0,
                    'is_active' => true,
                ]
            );
            
            // Add to target branch
            $targetLog = $this->addStock(
                $targetBatch,
                $quantity,
                $userId,
                "Transfer from branch {$sourceBatch->branch_id}. " . ($notes ?? ''),
                $sourceLog->rc_number
            );
            
            return [
                'source_log' => $sourceLog,
                'target_log' => $targetLog,
                'source_batch' => $sourceBatch->fresh(),
                'target_batch' => $targetBatch->fresh(),
            ];
        });
    }
    
    /**
     * Adjust stock (for corrections)
     */
    public function adjustStock(MedicineBatch $batch, int $newQuantity, int $userId, string $reason): InventoryLog
    {
        return DB::transaction(function () use ($batch, $newQuantity, $userId, $reason) {
            $previousStock = $batch->remaining_quantity;
            $quantity = $newQuantity - $previousStock;
            
            $batch->update([
                'remaining_quantity' => $newQuantity,
                'is_active' => $newQuantity > 0
            ]);
            
            $log = InventoryLog::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $batch->branch_id,
                'medicine_id' => $batch->medicine_id,
                'medicine_batch_id' => $batch->id,
                'user_id' => $userId,
                'type' => 'adjustment',
                'quantity' => abs($quantity),
                'previous_stock' => $previousStock,
                'new_stock' => $newQuantity,
                'notes' => "Adjustment: {$reason}",
            ]);
            
            $this->checkAndCreateAlerts($batch->medicine);
            
            return $log;
        });
    }
    
    /**
     * Check stock levels and create alerts if needed
     */
    public function checkAndCreateAlerts(Medicine $medicine): void
    {
        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id ?? $medicine->branch_id;
        
        foreach ($medicine->batches as $batch) {
            // Check for expiring soon (30 days)
            if ($batch->expiry_date && $batch->expiry_date->diffInDays(now()) <= 30 && $batch->remaining_quantity > 0) {
                StockAlert::firstOrCreate(
                    [
                        'branch_id' => $batch->branch_id,
                        'medicine_id' => $medicine->id,
                        'alert_type' => 'expiring_soon',
                        'is_resolved' => false,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'message' => "Batch {$batch->batch_number} expires on {$batch->expiry_date->format('d M Y')}",
                    ]
                );
            }
        }
        
        // Check total stock level
        $totalStock = $medicine->total_stock;
        
        if ($totalStock <= 0) {
            StockAlert::firstOrCreate(
                [
                    'branch_id' => $branchId,
                    'medicine_id' => $medicine->id,
                    'alert_type' => 'out_of_stock',
                    'is_resolved' => false,
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'message' => "{$medicine->name} is out of stock",
                ]
            );
        } elseif ($totalStock <= $medicine->reorder_level) {
            StockAlert::firstOrCreate(
                [
                    'branch_id' => $branchId,
                    'medicine_id' => $medicine->id,
                    'alert_type' => 'low_stock',
                    'is_resolved' => false,
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'message' => "{$medicine->name} is low on stock ({$totalStock} units). Reorder level: {$medicine->reorder_level}",
                ]
            );
        }
    }
    
    /**
     * Resolve stock alerts for a medicine
     */
    public function resolveStockAlerts(Medicine $medicine, int $resolvedBy = null): void
    {
        $resolvedBy = $resolvedBy ?? auth()->id();
        
        StockAlert::where('medicine_id', $medicine->id)
            ->where('is_resolved', false)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => $resolvedBy,
                'resolution_notes' => 'Stock updated',
            ]);
    }
    
    /**
     * Get inventory value report
     */
    public function getInventoryValue(int $branchId): array
    {
        $batches = MedicineBatch::with('medicine')
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->get();
        
        $totalValue = 0;
        $totalSaleValue = 0;
        $categoryBreakdown = [];
        
        foreach ($batches as $batch) {
            $value = $batch->remaining_quantity * $batch->unit_price;
            $saleValue = $batch->remaining_quantity * ($batch->sale_price ?? $batch->unit_price);
            
            $totalValue += $value;
            $totalSaleValue += $saleValue;
            
            $category = $batch->medicine->category->name ?? 'Uncategorized';
            if (!isset($categoryBreakdown[$category])) {
                $categoryBreakdown[$category] = [
                    'value' => 0,
                    'items' => 0,
                ];
            }
            
            $categoryBreakdown[$category]['value'] += $value;
            $categoryBreakdown[$category]['items'] += $batch->remaining_quantity;
        }
        
        return [
            'total_value' => $totalValue,
            'total_sale_value' => $totalSaleValue,
            'potential_profit' => $totalSaleValue - $totalValue,
            'category_breakdown' => $categoryBreakdown,
            'batch_count' => $batches->count(),
        ];
    }
    
    /**
     * Get expiring batches
     */
    public function getExpiringBatches(int $branchId, int $days = 30)
    {
        return MedicineBatch::with('medicine')
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->get();
    }
}