<?php

namespace App\Services\Laboratory;

use App\Interfaces\LabReportRepositoryInterface;
use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\LabTestParameter;
use App\Models\LabSampleInfo;
use App\Models\User;
use App\Notifications\LabResultReady;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabReportService
{
    private LabReportRepositoryInterface $labReportRepository;

    public function __construct(LabReportRepositoryInterface $labReportRepository)
    {
        $this->labReportRepository = $labReportRepository;
    }

    public function createLabReport(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Remove virtual fields that don't exist in database
            $data = $this->removeVirtualFields($data);

            // Set default values
            $data['status'] = $data['status'] ?? 'pending';
            $data['priority'] = $data['priority'] ?? 'normal';
            $data['is_verified'] = false;

            // Ensure lab_test_type_id is set
            if (empty($data['lab_test_type_id']) && !empty($data['test_type_id'])) {
                $data['lab_test_type_id'] = $data['test_type_id'];
            }

            // Set collection date if not provided
            if (empty($data['collection_date'])) {
                $data['collection_date'] = now();
            }

            // Set technician_id to current user if not provided
            if (empty($data['technician_id'])) {
                $data['technician_id'] = auth()->id();
            }

            // Create the lab report with sample info
            $labOrder = $this->labReportRepository->create($data);

            Log::info('Lab report created', [
                'id' => $labOrder->id,
                'lab_number' => $labOrder->lab_number,
                'user_id' => auth()->id()
            ]);

            return $labOrder;
        });
    }

    public function updateLabReport(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            // Remove virtual fields that don't exist in database
            $data = $this->removeVirtualFields($data);


            // Preserve verification status if not explicitly changed
            if (!isset($data['is_verified'])) {
                unset($data['is_verified']);
                unset($data['verified_by_user_id']);
                unset($data['verified_at']);
            }

            $labOrder = $this->labReportRepository->update($id, $data);

            Log::info('Lab report updated', [
                'id' => $labOrder->id,
                'lab_number' => $labOrder->lab_number,
                'user_id' => auth()->id()
            ]);

            return $labOrder;
        });
    }

    /**
     * Remove virtual fields that don't exist in the database table
     */
    private function removeVirtualFields(array $data): array
    {
        $virtualFields = ['test_name', 'test_type', 'sample_type'];

        foreach ($virtualFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    public function deleteLabReport($id)
    {
        $labOrder = LabOrder::findOrFail($id);

        // Delete related records in correct order
        foreach ($labOrder->items as $item) {
            // Delete sample info first
            $item->sampleInfo()->delete();
            // Delete results
            $item->labResults()->delete();
            // Delete the item
            $item->delete();
        }

        // Finally delete the order
        $labOrder->delete();

        return true;
    }

    public function updateStatus(int $id, string $status)
    {
        return DB::transaction(function () use ($id, $status) {
            $updateData = ['status' => $status];

            if ($status === 'completed') {
                $updateData['reporting_date'] = now();
            }

            if ($status === 'processing' && empty($labOrder->collection_date)) {
                $updateData['collection_date'] = now();
            }

            $labOrder = $this->labReportRepository->update($id, $updateData);

            Log::info('Lab report status updated', [
                'id' => $labOrder->id,
                'status' => $status,
                'user_id' => auth()->id()
            ]);

            return $labOrder;
        });
    }

    public function submitResults(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $labOrder = $this->labReportRepository->find($id);

            // Handle result values
            if (isset($data['result_values']) && is_array($data['result_values'])) {
                foreach ($data['result_values'] as $parameterId => $value) {
                    $parameter = LabTestParameter::find($parameterId);

                    if ($parameter) {
                        // Find the corresponding LabOrderItem that contains this parameter
                        $orderItem = $labOrder->items()->whereHas('labTestType.parameters', function ($query) use ($parameterId) {
                            $query->where('id', $parameterId);
                        })->first();

                        if ($orderItem) {
                            $isAbnormal = $this->checkIfAbnormal($parameter, $value);

                            LabResult::updateOrCreate(
                                [
                                    'lab_order_item_id' => $orderItem->id,
                                    'lab_test_parameter_id' => $parameterId
                                ],
                                [
                                    'numeric_value' => is_numeric($value) ? $value : null,
                                    'text_value' => !is_numeric($value) ? $value : null,
                                    'value_type' => is_numeric($value) ? 'numeric' : 'text',
                                    'is_abnormal' => $isAbnormal,
                                    'remarks' => $data['remarks'][$parameterId] ?? null
                                ]
                            );

                            // Update item status to processing or completed
                            $orderItem->update(['status' => 'processing']);
                        }
                    }
                }
            }

            // Update report fields
            $updateData = [
                'status' => 'completed',
                'reporting_date' => now(),
            ];

            if (!empty($data['interpretation'])) {
                $updateData['interpretation'] = $data['interpretation'];
            }

            if (!empty($data['recommendations'])) {
                $updateData['recommendations'] = $data['recommendations'];
            }

            // Handle critical results
            if (isset($data['is_critical']) && $data['is_critical']) {
                $existingComments = $labOrder->comments ?? '';
                $updateData['comments'] = trim($existingComments . ' | CRITICAL RESULT - ' . now()->format('Y-m-d H:i'));
            }

            $labOrder->update($updateData);

            Log::info('Lab results submitted', [
                'id' => $labOrder->id,
                'results_count' => count($data['result_values'] ?? []),
                'user_id' => auth()->id()
            ]);

            return $labOrder->load('results.parameter');
        });
    }

    protected function checkIfAbnormal($parameter, $value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $numericValue = (float) $value;

        if ($parameter->min_range !== null && $numericValue < $parameter->min_range) {
            return true;
        }

        if ($parameter->max_range !== null && $numericValue > $parameter->max_range) {
            return true;
        }

        return false;
    }

    public function verifyReport(int $id, int $verifiedById, ?string $notes = null)
    {
        return DB::transaction(function () use ($id, $verifiedById, $notes) {
            $labOrder = $this->labReportRepository->find($id);

            $updateData = [
                'is_verified' => true,
                'verified_by_user_id' => $verifiedById,
                'verified_at' => now(),
            ];

            if ($notes) {
                $existing = $labOrder->comments ?? '';
                $updateData['comments'] = trim($existing . ' | Verified: ' . $notes);
            }

            $labOrder->update($updateData);
            $labOrder = $labOrder->fresh(['verifiedBy']);

            // Notify the doctor
            try {
                if ($labOrder->doctor) {
                    $verifierName = User::find($verifiedById)?->name ?? 'Lab Technician';
                    $labOrder->doctor->notify(new LabResultReady($labOrder, $verifierName));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send verification notification: ' . $e->getMessage());
            }

            Log::info('Lab report verified', [
                'id' => $labOrder->id,
                'verified_by' => $verifiedById,
                'user_id' => auth()->id()
            ]);

            return $labOrder;
        });
    }

    public function updateSampleInfo(int $id, array $sampleData)
    {
        return DB::transaction(function () use ($id, $sampleData) {
            $labOrder = $this->labReportRepository->find($id);

            $sampleInfo = $labOrder->sampleInfo()->updateOrCreate(
                ['lab_order_id' => $labOrder->id],
                $sampleData
            );

            Log::info('Sample info updated', [
                'id' => $labOrder->id,
                'sample_id' => $sampleInfo->sample_id,
                'user_id' => auth()->id()
            ]);

            return $sampleInfo;
        });
    }

    public function attachReportFile(int $id, $file)
    {
        // This requires a file_path column in lab_orders table
        // You need to add this migration first
        throw new \Exception('File upload functionality requires schema update. Please add file_path column to lab_orders table.');
    }

    public function getTestTypes()
    {
        return \App\Models\LabTestType::with('parameters')
            ->orderBy('name')
            ->get();
    }

    public function getSampleTypes(): array
    {
        return [
            'Blood' => 'Whole Blood',
            'Serum' => 'Serum',
            'Plasma' => 'Plasma',
            'Urine' => 'Urine',
            'Stool' => 'Stool',
            'CSF' => 'Cerebrospinal Fluid',
            'Swab' => 'Swab',
            'Sputum' => 'Sputum',
            'Tissue' => 'Tissue',
            'Other' => 'Other',
        ];
    }

    public function getDashboardStatistics(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        return [
            'total' => LabOrder::count(),
            'pending' => LabOrder::where('status', 'pending')->count(),
            'processing' => LabOrder::where('status', 'processing')->count(),
            'completed' => LabOrder::where('status', 'completed')->count(),
            'cancelled' => LabOrder::where('status', 'cancelled')->count(),
            'urgent' => LabOrder::whereIn('priority', ['urgent', 'emergency'])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'overdue' => LabOrder::whereNotIn('status', ['completed', 'cancelled'])
                ->where('created_at', '<', now()->subHours(24))
                ->count(),
            'today' => LabOrder::whereDate('created_at', $today)->count(),
            'this_week' => LabOrder::where('created_at', '>=', $weekStart)->count(),
            'pending_verification' => LabOrder::where('status', 'completed')
                ->where('is_verified', false)
                ->count(),
            'verified_today' => LabOrder::whereDate('verified_at', $today)->count(),
        ];
    }

    public function getPredefinedTests(): array
    {
        // Get actual test types from database
        $testTypes = \App\Models\LabTestType::all();

        $predefinedTests = [];

        foreach ($testTypes as $testType) {
            $predefinedTests[] = [
                'id' => $testType->id,
                'name' => $testType->name,
                'test_type' => $testType->department ?? 'General',
                'sample_type' => $testType->sample_type ?? 'Blood',
            ];
        }

        return $predefinedTests;
    }

    public function generateLabNumber(): string
    {
        $date = now()->format('Ymd');
        $count = LabOrder::whereDate('created_at', today())->count() + 1;
        return 'LAB-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function generateSampleId(): string
    {
        $date = now();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return "SMP-{$year}{$month}{$day}-{$random}";
    }
}
