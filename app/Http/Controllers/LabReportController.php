<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Laboratory\LabReportFileRequest;
use App\Http\Requests\Laboratory\LabReportRequest;
use App\Http\Requests\Laboratory\LabResultRequest;
use App\Interfaces\LabReportRepositoryInterface;
use App\Models\LabOrder;
use App\Services\Laboratory\LabReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Change this line - remove Facades\
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LabReportController extends Controller
{
    private $labReportRepository;
    private $labReportService;

    public function __construct(
        LabReportRepositoryInterface $labReportRepository,
        LabReportService $labReportService
    ) {
        $this->labReportRepository = $labReportRepository;
        $this->labReportService = $labReportService;
        $this->middleware(['auth', 'role:lab']);
    }

    /**
     * Display laboratory dashboard.
     */
    public function dashboard(): View
    {
        try {
            $stats = $this->labReportService->getDashboardStatistics();
            $pendingQueue = $this->labReportRepository->getPendingReports();
            $recentReports = $this->labReportRepository->getReportsByDateRange(
                now()->subDays(7)->format('Y-m-d'),
                now()->format('Y-m-d')
            );
            $urgentReports = $this->labReportRepository->getUrgentReports();
            $overdueReports = $this->labReportRepository->getOverdueReports();

            return view('laboratory.dashboard', compact(
                'stats',
                'pendingQueue',
                'recentReports',
                'urgentReports',
                'overdueReports'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading lab dashboard: ' . $e->getMessage());
            return view('laboratory.dashboard')->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Display a listing of lab reports.
     */
    // In LabReportController.php, update the index method:
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'priority', 'patient_id', 'doctor_id', 'date_from', 'date_to', 'search']);
            $labReports = $this->labReportRepository->getAll($filters);

            // Get filter options for dropdowns
            $patients = $this->labReportRepository->getEligiblePatients();
            $doctors = $this->labReportRepository->getAllDoctors();
            $testTypes = $this->labReportService->getTestTypes();

            // If it's an AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'data' => $labReports->items(),
                    'links' => $labReports->links()->toArray(),
                    'from' => $labReports->firstItem(),
                    'to' => $labReports->lastItem(),
                    'total' => $labReports->total(),
                    'current_page' => $labReports->currentPage(),
                    'last_page' => $labReports->lastPage(),
                ]);
            }

            // For regular requests, return view
            return view('laboratory.reports.index', compact('labReports', 'patients', 'doctors', 'testTypes', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error fetching lab reports: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Failed to load lab reports.'], 500);
            }

            return view('laboratory.reports.index')->with('error', 'Failed to load lab reports.');
        }
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
            $user = auth()->user();
            if ($user) {
                try {
                    $branchId = $user->current_branch_id;
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error applying branch filter', ['error' => $e->getMessage()]);
                }
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
            Log::error('Error fetching lab reports data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'filters' => $filters ?? []
            ]);

            return response()->json([
                'error' => 'Failed to load lab reports',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = auth()->user();
            $branchId = null;

            // Safely get branch ID
            if ($user) {
                try {
                    $branchId = $user->current_branch_id;
                } catch (\Exception $e) {
                    Log::warning('Error getting current_branch_id', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    // Fallback to first branch
                    $branch = $user->branches()->first();
                    $branchId = $branch?->id;
                }
            }

            // If still no branch ID, use a default or skip branch filtering
            $query = LabOrder::query();
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $stats = [
                'total' => (clone $query)->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'processing' => (clone $query)->where('status', 'processing')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
                'urgent' => (clone $query)->where('priority', 'urgent')->count(),
                'today' => (clone $query)->whereDate('created_at', today())->count(),
                'this_week' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'overdue' => 0,
                'completed_today' => (clone $query)->where('status', 'completed')->whereDate('reporting_date', today())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lab statistics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => 0,
                    'pending' => 0,
                    'processing' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                    'urgent' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'overdue' => 0,
                    'completed_today' => 0
                ]
            ]);
        }
    }
    /**
     * Get lab reports for API (AJAX requests)
     */
    public function getApiLabReports(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'priority', 'patient_id', 'doctor_id', 'date_from', 'date_to', 'search']);
            $labReports = $this->labReportRepository->getAll($filters);

            return response()->json([
                'success' => true,
                'data' => $labReports
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lab reports via API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load lab reports.'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new lab report.
     */
    public function create(): View|\Illuminate\Http\RedirectResponse
    {
        try {
            $patients = $this->labReportRepository->getEligiblePatients();
            $doctors = $this->labReportRepository->getAllDoctors();
            $technicians = $this->labReportRepository->getAllTechnicians();
            $testTypes = $this->labReportService->getTestTypes();
            $sampleTypes = $this->labReportService->getSampleTypes();
            $predefinedTests = $this->labReportService->getPredefinedTests();

            return view('laboratory.reports.create', compact(
                'patients',
                'doctors',
                'technicians',
                'testTypes',
                'sampleTypes',
                'predefinedTests'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading create lab report form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load form.');
        }
    }

    /**
     * Display the specified lab report.
     */
    public function show(int $id): View|\Illuminate\Http\RedirectResponse
    {
        try {
            $labReport = $this->labReportRepository->findWithRelations($id);

            return view('laboratory.reports.show', compact('labReport'));
        } catch (\Exception $e) {
            Log::error('Error fetching lab report: ' . $e->getMessage());
            return back()->with('error', 'Lab report not found.');
        }
    }

    /**
     * Show the form for editing a lab report.
     */
    public function edit(int $id): View|\Illuminate\Http\RedirectResponse
    {
        try {
            $labReport = $this->labReportRepository->find($id);
            $patients = $this->labReportRepository->getEligiblePatients();
            $doctors = $this->labReportRepository->getAllDoctors();
            $technicians = $this->labReportRepository->getAllTechnicians();
            $testTypes = $this->labReportService->getTestTypes();
            $sampleTypes = $this->labReportService->getSampleTypes();
            $predefinedTests = $this->labReportService->getPredefinedTests();

            return view('laboratory.reports.edit', compact(
                'labReport',
                'patients',
                'doctors',
                'technicians',
                'testTypes',
                'sampleTypes',
                'predefinedTests'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading edit lab report form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Remove the specified lab report.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->labReportService->deleteLabReport($id);

            Log::info('Lab report deleted successfully', ['id' => $id, 'user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Lab report deleted successfully!',
                'redirect' => route('lab.reports.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting lab report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lab report. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update lab report status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            $labReport = $this->labReportService->updateStatus($id, $request->status);

            Log::info('Lab report status updated', [
                'id' => $labReport->id,
                'status' => $labReport->status,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'data' => $labReport
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lab report status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Submit test results.
     */
    public function submitResults(LabResultRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $labReport = $this->labReportService->submitResults($id, $validated);

            Log::info('Lab results submitted', ['id' => $labReport->id, 'user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Results submitted successfully!',
                'data' => $labReport,
                'redirect' => route('lab.reports.show', $labReport->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting lab results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit results. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Verify lab report.
     */
    public function verifyReport(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'verification_notes' => 'nullable|string|max:500'
            ]);

            $labReport = $this->labReportService->verifyReport(
                $id,
                auth()->id(),
                $request->verification_notes
            );

            Log::info('Lab report verified', ['id' => $labReport->id, 'verified_by' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Report verified successfully!',
                'data' => $labReport
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying lab report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify report. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Upload file for lab report.
     */
    public function uploadFile(LabReportFileRequest $request, int $id): JsonResponse
    {
        try {
            $labReport = $this->labReportRepository->find($id);

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Delete old file if exists
                if ($labReport->file_path && Storage::exists($labReport->file_path)) {
                    Storage::delete($labReport->file_path);
                }

                // Store new file
                $path = $file->store('lab-reports/' . date('Y/m'), 'public');

                $labReport->update(['file_path' => $path]);

                Log::info('Lab report file uploaded', [
                    'id' => $labReport->id,
                    'file' => $path,
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully!',
                    'file_path' => $path,
                    'file_url' => Storage::url($path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file provided.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error uploading lab report file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created lab report.
     */
    public function store(LabReportRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $labReport = $this->labReportService->createLabReport($validated);

            Log::info('Lab report created successfully', ['id' => $labReport->id, 'user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Lab report created successfully!',
                'data' => $labReport,
                'redirect' => route('lab.reports.show', $labReport->id)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating lab report: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lab report. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified lab report.
     */
    public function update(LabReportRequest $request, int $id): JsonResponse
    {
        try {
            Log::info('Lab report updated Request: ', ['id' => $id, 'request' => @json_encode($request->all())]);
            $validated = $request->validated();
            $labReport = $this->labReportService->updateLabReport($id, $validated);

            Log::info('Lab report updated successfully', ['id' => $labReport->id, 'user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Lab report updated successfully!',
                'data' => $labReport,
                'redirect' => route('lab.reports.show', $labReport->id)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating lab report: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lab report. Please try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating lab report: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lab report. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get lab report for printing.
     */
    public function printReport(int $id)
    {
        try {
            $labReport = $this->labReportRepository->findWithRelations($id);

            return view('laboratory.reports.print', compact('labReport'));
        } catch (\Exception $e) {
            Log::error('Error loading print view: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lab report. Please try again.',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Print queue of lab reports.
     */
    public function printQueue(Request $request)
    {
        try {
            $filters = $request->only(['status', 'priority', 'date_from', 'date_to']);

            // Build query with proper eager loading to avoid N+1 issues
            $query = LabOrder::with([
                'patient',
                'doctor',
                'items.labTestType'
            ]);

            // Apply branch filter
            $user = auth()->user();
            if ($user) {
                try {
                    $branchId = $user->current_branch_id;
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error applying branch filter in print queue', ['error' => $e->getMessage()]);
                }
            }

            // Apply filters
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            // Transform data to match the expected format in the views
            $transformedReports = $reports->map(function ($report) {
                $testType = $report->items->first()?->labTestType;

                return (object)[
                    'id' => $report->id,
                    'lab_number' => $report->lab_number,
                    'test_code' => $report->lab_number,
                    'test_name' => $testType?->name ?? 'Multiple Tests',
                    'test_type' => $testType,
                    'patient' => $report->patient,
                    'doctor' => $report->doctor,
                    'status' => $report->status,
                    'priority' => $report->priority,
                    'created_at' => $report->created_at,
                ];
            });

            if ($request->has('pdf')) {
                $pdf = Pdf::loadView('laboratory.reports.queue-pdf', ['reports' => $transformedReports])
                    ->setOption([
                        'isRemoteEnabled' => true,
                        'isFontSubsettingEnabled' => true,
                        'defaultFont' => 'dejavu sans'
                    ]);
                return $pdf->download('lab-reports-queue-' . date('Y-m-d') . '.pdf');
            }

            return view('laboratory.reports.queue-print', ['reports' => $transformedReports]);
        } catch (\Exception $e) {
            Log::error('Error printing lab reports queue: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to generate print queue: ' . $e->getMessage());
        }
    }
    /**
     * Export lab reports.
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'priority', 'date_from', 'date_to', 'patient_id', 'doctor_id']);
            $reports = $this->labReportRepository->getReportsByDateRange(
                $filters['date_from'] ?? now()->subMonth()->format('Y-m-d'),
                $filters['date_to'] ?? now()->format('Y-m-d')
            );

            $data = $reports->map(function ($report) {
                return [
                    'Test Code' => $report->test_code,
                    'Test Name' => $report->test_name,
                    'Patient Name' => $report->patient->name,
                    'Patient CNIC' => $report->patient->cnic,
                    'Doctor' => $report->doctor->name,
                    'Status' => $report->status,
                    'Priority' => $report->priority,
                    'Sample Type' => $report->sample_type,
                    'Created Date' => $report->created_at->format('Y-m-d H:i:s'),
                    'Result Date' => $report->result_ready_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ];
            });

            Log::info('Lab reports exported', ['count' => $data->count(), 'user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'filename' => 'lab-reports-export-' . date('Y-m-d') . '.xlsx'
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting lab reports: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export reports. Please try again.'
            ], 500);
        }
    }


    /**
     * Get statistics for dashboard.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->labReportService->getDashboardStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lab statistics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Return default stats if there's an error
            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => 0,
                    'processing' => 0,
                    'completed' => 0,
                    'urgent' => 0,
                    'today' => 0,
                    'overdue' => 0,
                    'this_week' => 0,
                    'total' => 0,
                    'completed_today' => 0
                ]
            ]);
        }
    }

    /**
     * Get pending reports for technician dashboard
     */
    public function pendingReports(): JsonResponse
    {
        try {
            $branchId = auth()->user()->current_branch_id;

            $reports = LabOrder::with(['patient', 'doctor', 'items.labTestType'])
                ->where('branch_id', $branchId)
                ->whereIn('status', ['pending', 'processing'])
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'asc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reports->map(function ($report) {
                    $testType = $report->items->first()?->labTestType;

                    return [
                        'id' => $report->id,
                        'test_name' => $testType?->name ?? 'Multiple Tests',
                        'test_code' => $report->lab_number,
                        'patient' => $report->patient ? [
                            'id' => $report->patient->id,
                            'name' => $report->patient->name,
                            'cnic' => $report->patient->cnic
                        ] : null,
                        'doctor' => $report->doctor ? [
                            'id' => $report->doctor->id,
                            'name' => $report->doctor->name
                        ] : null,
                        'status' => $report->status,
                        'priority' => $report->priority,
                        'created_at' => $report->created_at
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pending reports: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Notify doctor about completed report.
     */
    public function notifyDoctor(Request $request, int $id): JsonResponse
    {
        try {
            $labReport = $this->labReportRepository->findWithRelations($id);

            // Here you would implement actual notification logic
            // This could be email, SMS, or in-app notification

            Log::info('Doctor notified about lab report', [
                'id' => $labReport->id,
                'doctor_id' => $labReport->doctor_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor has been notified about the completed report.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error notifying doctor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to notify doctor.'
            ], 500);
        }
    }

    /**
     * Get predefined test templates.
     */
    public function getTestTemplates(): JsonResponse
    {
        try {
            $templates = $this->labReportService->getPredefinedTests();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching test templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load test templates.'
            ], 500);
        }
    }

    /**
     * Download lab report as PDF.
     */
    public function downloadPdf(int $id)
    {
        try {
            $labReport = $this->labReportRepository->findWithRelations($id);

            if (!$labReport) {
                return back()->with('error', 'Lab report not found.');
            }

            $pdf = Pdf::loadView('laboratory.reports.print', compact('labReport'))->setOption([
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'dejavu sans'
            ]);

            // Set paper to A4 as defined in the print view styles
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('lab-report-' . ($labReport->lab_number ?? $id) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error downloading lab report PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
