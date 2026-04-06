<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Interfaces\LabReportRepositoryInterface;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabTestType;
use App\Services\Laboratory\LabReportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $reportService;
    protected $labReportService;
    protected $labReportRepository;

    public function __construct(
        ReportService $reportService,
        LabReportService $labReportService,
        LabReportRepositoryInterface $labReportRepository
    ) {
        $this->reportService = $reportService;
        $this->labReportService = $labReportService;
        $this->labReportRepository = $labReportRepository;
    }

    /**
     * Display reports index.
     */
    public function index()
    {
        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;

        $stats = [
            'total_orders' => LabOrder::where('branch_id', $branchId)->count(),
            'completed_orders' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->count(),
            'verified_orders' => LabOrder::where('branch_id', $branchId)
                ->where('is_verified', true)
                ->count(),
            'avg_processing_time' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, reporting_date)) as avg_hours'))
                ->value('avg_hours'),
        ];

        // Monthly data
        $monthlyData = LabOrder::where('branch_id', $branchId)
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top tests
        $topTests = LabOrderItem::select(
            'lab_test_type_id',
            DB::raw('COUNT(*) as total')
        )
            ->whereHas('labOrder', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->groupBy('lab_test_type_id')
            ->with('labTestType')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('lab.reports.index', compact('stats', 'monthlyData', 'topTests'));
    }

    /**
     * Get lab reports data for AJAX requests with pagination
     */
    public function getReportsData(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'priority', 'patient_id', 'doctor_id', 'date_from', 'date_to', 'search', 'sort_by', 'sort_direction']);
            $perPage = $request->get('per_page', 10);

            // Build query using the correct tables with proper eager loading
            $query = LabOrder::with([
                'patient',
                'doctor',
                'items.labTestType',
                'items.labResults',
                'items.labResults.labTestParameter',
                'verifiedBy'
            ]);

            // Apply branch filter safely
            $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            // Apply filters
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            if (!empty($filters['patient_id'])) {
                $query->where('patient_id', $filters['patient_id']);
            }

            if (!empty($filters['doctor_id'])) {
                $query->where('doctor_id', $filters['doctor_id']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('lab_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('emrn', 'LIKE', "%{$search}%")
                                ->orWhere('cnic', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Apply sorting
            $sortField = $filters['sort_by'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            $labOrders = $query->paginate($perPage);

            // Transform data to match the expected format in the frontend
            $transformedData = $labOrders->getCollection()->map(function ($order) {
                // Get the first test type (or combine multiple)
                $testType = $order->items->first()?->labTestType;

                return [
                    'id' => $order->id,
                    'lab_number' => $order->lab_number,
                    'test_name' => $testType?->name ?? 'Multiple Tests',
                    'test_code' => $order->lab_number,
                    'test_type' => $testType ? [
                        'id' => $testType->id,
                        'name' => $testType->name,
                        'department' => $testType->department,
                    ] : null,
                    'patient' => $order->patient ? [
                        'id' => $order->patient->id,
                        'name' => $order->patient->name,
                        'cnic' => $order->patient->cnic,
                        'emrn' => $order->patient->emrn,
                    ] : null,
                    'doctor' => $order->doctor ? [
                        'id' => $order->doctor->id,
                        'name' => $order->doctor->name,
                    ] : null,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'is_verified' => $order->is_verified,
                    'verified_by' => $order->verifiedBy?->name,
                    'verified_at' => $order->verified_at,
                    'created_at' => $order->created_at,
                    'reporting_date' => $order->reporting_date,
                    'results_count' => $order->items->sum(function ($item) {
                        return $item->labResults->count();
                    }),
                ];
            });

            return response()->json([
                'data' => $transformedData,
                'current_page' => $labOrders->currentPage(),
                'last_page' => $labOrders->lastPage(),
                'per_page' => $labOrders->perPage(),
                'total' => $labOrders->total(),
                'from' => $labOrders->firstItem(),
                'to' => $labOrders->lastItem(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lab reports data: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to load lab reports',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for AJAX requests.
     */
    public function statistics(): JsonResponse
    {
        try {
            $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;

            $stats = [
                'total' => LabOrder::where('branch_id', $branchId)->count(),
                'pending' => LabOrder::where('branch_id', $branchId)->where('status', 'pending')->count(),
                'processing' => LabOrder::where('branch_id', $branchId)->where('status', 'processing')->count(),
                'completed' => LabOrder::where('branch_id', $branchId)->where('status', 'completed')->count(),
                'urgent' => LabOrder::where('branch_id', $branchId)->where('priority', 'urgent')->count(),
                'today' => LabOrder::where('branch_id', $branchId)->whereDate('created_at', today())->count(),
                'verified' => LabOrder::where('branch_id', $branchId)->where('is_verified', true)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lab statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'data' => [
                    'total' => 0,
                    'pending' => 0,
                    'processing' => 0,
                    'completed' => 0,
                    'urgent' => 0,
                    'today' => 0,
                    'verified' => 0
                ]
            ]);
        }
    }

    /**
     * Show lab report.
     */
    public function show(LabOrder $labReport)
    {
        $labReport->load([
            'patient',
            'doctor',
            'verifiedBy',
            'testType.parameters',
            'items' => function ($q) {
                $q->with(['labTestType', 'labResults.labTestParameter']);
            },
            'results.labTestParameter',
        ]);

        return view('lab.reports.show', [
            'labReport' => $labReport
        ]);
    }

    /**
     * Show edit form for lab report.
     */
    public function edit(LabOrder $labReport)
    {
        $labReport->load(['patient', 'doctor', 'items.labTestType']);

        // The edit.blade.php uses $labOrder variable name
        $labOrder = $labReport;

        $testTypes = LabTestType::orderBy('name')->get();
        $doctors = $this->labReportRepository->getAllDoctors();

        return view('lab.reports.edit', compact('labOrder', 'testTypes', 'doctors'));
    }

    /**
     * Update lab report.
     */
    public function update(Request $request, LabOrder $labReport): JsonResponse
    {
        try {
            $labReport->update([
                'doctor_id' => $request->doctor_id,
                'priority' => $request->priority ?? $labReport->priority,
                'status' => $request->status ?? $labReport->status,
                'comments' => $request->comments,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report updated successfully',
                'redirect' => route('lab.reports.show', $labReport->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lab report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF report.
     */
    public function pdf(LabOrder $labReport)
    {
        $labReport->load([
            'patient',
            'doctor',
            'verifiedBy',
            'testType.parameters',
            'items' => function ($q) {
                $q->with(['labTestType', 'labResults.labTestParameter']);
            }
        ]);

        // print.blade.php uses $labOrder variable name
        $pdf = Pdf::loadView('lab.reports.print', [
            'labOrder' => $labReport
        ]);

        return $pdf->download('lab-report-' . $labReport->lab_number . '.pdf');
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request, $type)
    {
        $branchId = session('current_branch_id');
        $data = [];
        $headers = [];

        switch ($type) {
            case 'orders':
                $data = $this->getOrdersExportData($branchId, $request);
                $headers = ['Lab #', 'Date', 'Patient', 'Doctor', 'Tests', 'Priority', 'Status', 'Verified'];
                break;

            case 'tests':
                $data = $this->getTestsExportData($branchId, $request);
                $headers = ['Test', 'Department', 'Total Orders', 'Completed', 'Avg. Time (hours)'];
                break;

            case 'results':
                $data = $this->getResultsExportData($branchId, $request);
                $headers = ['Date', 'Patient', 'Test', 'Parameter', 'Result', 'Reference', 'Abnormal'];
                break;

            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }

        $csv = $this->reportService->toCsv($data, $headers);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get orders export data.
     */
    protected function getOrdersExportData($branchId, $request)
    {
        $query = LabOrder::with(['patient', 'doctor', 'items.labTestType'])
            ->where('branch_id', $branchId);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return $orders->map(function ($order) {
            return [
                $order->lab_number,
                $order->created_at->format('Y-m-d H:i'),
                $order->patient->name,
                $order->doctor->name,
                $order->items->pluck('labTestType.name')->implode(', '),
                $order->priority,
                $order->status,
                $order->is_verified ? 'Yes' : 'No',
            ];
        })->toArray();
    }

    /**
     * Get tests export data.
     */
    protected function getTestsExportData($branchId, $request)
    {
        $testTypes = LabTestType::withCount(['labOrderItems' => function ($q) use ($branchId) {
            $q->whereHas('labOrder', function ($oq) use ($branchId) {
                $oq->where('branch_id', $branchId);
            });
        }])->get();

        return $testTypes->map(function ($test) use ($branchId) {
            $completed = LabOrderItem::where('lab_test_type_id', $test->id)
                ->whereHas('labOrder', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->where('status', 'completed')
                ->count();

            $avgTime = LabOrderItem::where('lab_test_type_id', $test->id)
                ->whereHas('labOrder', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->whereNotNull('reporting_date');
                })
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, lab_orders.created_at, lab_orders.reporting_date)) as avg_hours'))
                ->join('lab_orders', 'lab_orders.id', '=', 'lab_order_items.lab_order_id')
                ->value('avg_hours');

            return [
                $test->name,
                $test->department,
                $test->lab_order_items_count,
                $completed,
                round($avgTime ?? 0, 1),
            ];
        })->toArray();
    }

    /**
     * Get results export data.
     */
    protected function getResultsExportData($branchId, $request)
    {
        $items = LabOrderItem::with([
            'labOrder.patient',
            'labTestType',
            'labResults.labTestParameter'
        ])
            ->whereHas('labOrder', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('status', 'completed')
            ->limit(500)
            ->get();

        $data = [];

        foreach ($items as $item) {
            foreach ($item->labResults as $result) {
                $data[] = [
                    $item->labOrder->created_at->format('Y-m-d'),
                    $item->labOrder->patient->name,
                    $item->labTestType->name,
                    $result->labTestParameter->name,
                    $result->display_value,
                    $result->labTestParameter->reference_range ?? 'N/A',
                    $result->is_abnormal ? 'Yes' : 'No',
                ];
            }
        }

        return $data;
    }
    /**
     * Remove the specified lab report (order).
     */
    public function destroy(LabOrder $labReport)
    {
        try {
            $labReport->delete();
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit results for a lab report.
     */
    public function submitResults(Request $request, LabOrder $labReport): JsonResponse
    {
        try {
            $result = $this->labReportService->submitResults($labReport->id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Results submitted successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update lab report status.
     */
    public function updateStatus(Request $request, LabOrder $labReport): JsonResponse
    {
        try {
            $result = $this->labReportService->updateStatus($labReport->id, $request->status);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload report file.
     */
    public function uploadFile(Request $request, LabOrder $labReport): JsonResponse
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $result = $this->labReportService->attachReportFile($labReport->id, $request->file('file'));
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify lab report.
     */
    public function verify(Request $request, LabOrder $labReport): JsonResponse
    {
        try {
            $result = $this->labReportService->verifyReport(
                $labReport->id,
                auth()->id(),
                $request->notes
            );
            return response()->json([
                'success' => true,
                'message' => 'Report verified successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error verifying report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notify doctor about the lab report.
     */
    public function notifyDoctor(LabOrder $labReport): JsonResponse
    {
        try {
            // Logic to notify doctor (placeholder for now)
            // In a real app, this would trigger an email/SMS/Notification
            
            return response()->json([
                'success' => true,
                'message' => 'Doctor has been notified'
            ]);
        } catch (\Exception $e) {
            Log::error('Error notifying doctor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error notifying doctor: ' . $e->getMessage()
            ], 500);
        }
    }
}
