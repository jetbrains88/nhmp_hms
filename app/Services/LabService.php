<?php

namespace App\Services;

use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\LabTestType;
use App\Models\LabTestParameter;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabService
{
    /**
     * Create a new lab order with multiple test items
     */
    public function createLabOrder(array $data, int $doctorId, int $branchId): LabOrder
    {
        return DB::transaction(function () use ($data, $doctorId, $branchId) {
            // Generate lab number
            $labNumber = $this->generateLabNumber($branchId);
            
            // Create lab order
            $labOrder = LabOrder::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $branchId,
                'patient_id' => $data['patient_id'],
                'visit_id' => $data['visit_id'] ?? null,
                'doctor_id' => $doctorId,
                'lab_number' => $labNumber,
                'priority' => $data['priority'] ?? 'normal',
                'status' => 'pending',
                'comments' => $data['comments'] ?? null,
            ]);
            
            // Create order items for each test type
            foreach ($data['test_type_ids'] as $testTypeId) {
                LabOrderItem::create([
                    'uuid' => (string) Str::uuid(),
                    'lab_order_id' => $labOrder->id,
                    'lab_test_type_id' => $testTypeId,
                    'status' => 'pending',
                ]);
            }
            
            return $labOrder->load('items.labTestType');
        });
    }

    /**
     * Submit results for a lab order item
     */
    public function submitResults(LabOrderItem $orderItem, array $results, int $technicianId): LabOrderItem
    {
        return DB::transaction(function () use ($orderItem, $results, $technicianId) {
            // Update technician assignment
            $orderItem->update([
                'technician_id' => $technicianId,
                'status' => 'processing',
            ]);
            
            // Create/update results for each parameter
            foreach ($results as $parameterId => $value) {
                $parameter = LabTestParameter::findOrFail($parameterId);
                
                // Determine value type and store appropriately
                $resultData = $this->prepareResultData($parameter, $value);
                $resultData['lab_order_item_id'] = $orderItem->id;
                $resultData['lab_test_parameter_id'] = $parameterId;
                $resultData['uuid'] = (string) Str::uuid();
                
                // Check if result already exists
                $existingResult = LabResult::where('lab_order_item_id', $orderItem->id)
                    ->where('lab_test_parameter_id', $parameterId)
                    ->first();
                
                if ($existingResult) {
                    $existingResult->update($resultData);
                } else {
                    LabResult::create($resultData);
                }
            }
            
            // Check if all parameters have results
            $this->checkOrderItemCompletion($orderItem);
            
            return $orderItem->fresh(['labResults', 'labTestType.parameters']);
        });
    }

    /**
     * Verify a lab order
     */
    public function verifyOrder(LabOrder $labOrder, int $userId): LabOrder
    {
        return DB::transaction(function () use ($labOrder, $userId) {
            // Check if all items are completed
            foreach ($labOrder->items as $item) {
                if ($item->status !== 'completed') {
                    throw new \InvalidArgumentException("Cannot verify order: item {$item->id} is not completed");
                }
            }
            
            $labOrder->update([
                'is_verified' => true,
                'verified_by_user_id' => $userId,
                'verified_at' => now(),
                'status' => 'completed',
                'reporting_date' => now(),
            ]);
            
            return $labOrder->fresh();
        });
    }

    /**
     * Get pending lab orders for a branch
     */
    public function getPendingOrders(int $branchId)
    {
        return LabOrder::with(['patient', 'doctor', 'items.labTestType'])
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get lab orders for a patient
     */
    public function getPatientOrders(int $patientId)
    {
        return LabOrder::with(['doctor', 'items.labTestType', 'items.labResults'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Generate unique lab number
     */
    protected function generateLabNumber(int $branchId): string
    {
        $date = now()->format('Ymd');
        $prefix = 'LAB';
        
        // Get count of orders today
        $count = LabOrder::whereDate('created_at', today())->count() + 1;
        
        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Prepare result data based on parameter type
     */
    protected function prepareResultData(LabTestParameter $parameter, $value): array
    {
        $data = [
            'value_type' => $parameter->input_type === 'number' ? 'numeric' : 'text',
            'is_abnormal' => false,
        ];
        
        // Handle different input types
        switch ($parameter->input_type) {
            case 'number':
                $data['numeric_value'] = (float) $value;
                // Check if abnormal based on reference ranges
                if ($parameter->min_range !== null && $parameter->max_range !== null) {
                    $data['is_abnormal'] = $value < $parameter->min_range || $value > $parameter->max_range;
                }
                break;
                
            case 'boolean':
                $data['value_type'] = 'boolean';
                $data['boolean_value'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
                
            default:
                $data['text_value'] = (string) $value;
                break;
        }
        
        return $data;
    }

    /**
     * Check if all parameters for an order item have results
     */
    protected function checkOrderItemCompletion(LabOrderItem $orderItem): void
    {
        $expectedParameters = $orderItem->labTestType->parameters()->count();
        $submittedResults = $orderItem->labResults()->count();
        
        if ($submittedResults >= $expectedParameters) {
            $orderItem->update(['status' => 'completed']);
            
            // Check if all items in the order are completed
            $order = $orderItem->labOrder;
            $allCompleted = $order->items()->where('status', '!=', 'completed')->count() === 0;
            
            if ($allCompleted) {
                $order->update(['status' => 'completed']);
            }
        }
    }

    /**
     * Get lab statistics
     */
    public function getStats(int $branchId): array
    {
        return [
            'pending' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'pending')
                ->count(),
            'processing' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'processing')
                ->count(),
            'completed_today' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
            'verified_today' => LabOrder::where('branch_id', $branchId)
                ->where('is_verified', true)
                ->whereDate('verified_at', today())
                ->count(),
        ];
    }
}