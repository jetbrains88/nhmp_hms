<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lab\StoreLabOrderRequest;
use App\Http\Requests\Lab\VerifyOrderRequest;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\Patient;
use App\Models\Visit;
use App\Interfaces\LabReportRepositoryInterface;
use App\Services\LabService;
use App\Services\Laboratory\LabReportService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $labService;
    protected $labReportRepository;
    protected $labReportService;

    public function __construct(
        LabService $labService,
        LabReportRepositoryInterface $labReportRepository,
        LabReportService $labReportService
    ) {
        $this->labService = $labService;
        $this->labReportRepository = $labReportRepository;
        $this->labReportService = $labReportService;
    }

    /**
     * Display lab orders
     */
    public function index(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        
        $query = LabOrder::with(['patient', 'doctor', 'items.labTestType'])
            ->where('branch_id', $branchId);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('lab_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function ($patientQuery) use ($search) {
                      $patientQuery->where('name', 'LIKE', "%{$search}%")
                                 ->orWhere('emrn', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        $stats = $this->labService->getStats($branchId);
        
        return view('lab.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show create order form
     */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $visitId = $request->get('visit_id');
        
        $patient = null;
        $visit = null;
        
        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
        }
        
        if ($visitId) {
            $visit = Visit::with('patient')->findOrFail($visitId);
            $patient = $visit->patient;
        }
        
        $testTypes = \App\Models\LabTestType::with('parameters')
            ->orderBy('name')
            ->get();
        
        $doctors = $this->labReportRepository->getAllDoctors();
        $technicians = $this->labReportRepository->getAllTechnicians();
        $predefinedTests = $this->labReportService->getPredefinedTests();
        
        return view('lab.orders.create', compact('patient', 'visit', 'testTypes', 'doctors', 'technicians', 'predefinedTests'));
    }

    /**
     * Store new lab order
     */
    public function store(StoreLabOrderRequest $request)
    {
        $labOrder = $this->labService->createLabOrder(
            $request->validated(),
            auth()->id(),
            auth()->user()->current_branch_id
        );
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab order created successfully. Order #: ' . $labOrder->lab_number,
                'redirect' => route('lab.orders.show', $labOrder)
            ]);
        }

        return redirect()
            ->route('lab.orders.show', $labOrder)
            ->with('success', 'Lab order created successfully. Order #: ' . $labOrder->lab_number);
    }

    /**
     * Show lab order details
     */
    public function show(LabOrder $order)
    {
        $order->load([
            'patient',
            'doctor',
            'verifiedBy',
            'items' => function ($q) {
                $q->with(['labTestType.parameters', 'labResults', 'technician']);
            }
        ]);
        
        return view('lab.orders.show', ['labOrder' => $order]);
    }

    /**
     * Start processing an order item
     */
    public function startItem(\Illuminate\Http\Request $request, LabOrderItem $item)
    {
        $item->update([
            'status' => 'processing',
            'technician_id' => auth()->id(),
        ]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Started processing test: ' . $item->labTestType->name
            ]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Started processing test: ' . $item->labTestType->name);
    }

    public function verify(VerifyOrderRequest $request, LabOrder $labOrder)
    {
        try {
            $labOrder = $this->labService->verifyOrder($labOrder, auth()->id());
            
            return redirect()
                ->route('lab.orders.show', $labOrder)
                ->with('success', 'Lab order verified successfully');
                
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show edit form for lab order
     */
    public function edit(LabOrder $order)
    {
        $order->load(['patient', 'items.labTestType']);
        
        $testTypes = \App\Models\LabTestType::with('parameters')
            ->orderBy('name')
            ->get();
            
        $doctors = $this->labReportRepository->getAllDoctors();
        $technicians = $this->labReportRepository->getAllTechnicians();
        $predefinedTests = $this->labReportService->getPredefinedTests();
        
        return view('lab.orders.edit', ['labOrder' => $order, 'testTypes' => $testTypes, 'doctors' => $doctors, 'technicians' => $technicians, 'predefinedTests' => $predefinedTests]);
    }

    /**
     * Update lab order
     */
    public function update(Request $request, LabOrder $order)
    {
        // Simple update logic for now, can be expanded
        $order->update($request->only([
        'priority', 'device_name', 'comments', 'reporting_date',
        'lab_test_type_id', 'doctor_id', 'lab_number', 'status'
    ]));
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab order updated successfully',
                'redirect' => route('lab.orders.show', $order)
            ]);
        }

        return redirect()
            ->route('lab.orders.show', $order)
            ->with('success', 'Lab order updated successfully');
    }

    /**
     * Delete lab order
     */
    public function destroy(LabOrder $order)
    {
        $order->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lab order deleted successfully',
                'redirect' => route('lab.orders.index')
            ]);
        }

        return redirect()
            ->route('lab.orders.index')
            ->with('success', 'Lab order deleted successfully');
    }


    /**
     * Print lab report
     */
    public function print(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'doctor',
            'verifiedBy',
            'items' => function ($q) {
                $q->with(['labTestType', 'labResults.labTestParameter']);
            }
        ]);
        
        return view('lab.reports.print', ['labOrder' => $labOrder]);
    }

    /**
     * Get pending items for AJAX dashboard
     */
    public function pending()
    {
        $branchId = auth()->user()->current_branch_id;
        
        $pendingItems = \App\Models\LabOrderItem::with(['labOrder.patient', 'labOrder.doctor', 'labTestType'])
            ->whereHas('labOrder', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'test_name' => collect([$item->labTestType->name])->implode(', '),
                    'patient' => [
                        'name' => $item->labOrder->patient->name ?? 'N/A'
                    ],
                    'doctor' => [
                        'name' => $item->labOrder->doctor->name ?? 'N/A'
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pendingItems
        ]);
    }
}