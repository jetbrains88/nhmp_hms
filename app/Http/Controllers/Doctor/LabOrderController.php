<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\LabTestType;
use App\Services\LabService;
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    protected $labService;

    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }

    /**
     * Display a listing of lab orders.
     */
    public function index()
    {
        $doctorId = auth()->id();
        $labOrders = LabOrder::with(['patient', 'items.labTestType'])
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('doctor.lab-orders.index', compact('labOrders'));
    }

    /**
     * Show the form for creating a new lab order.
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

        $testTypes = LabTestType::with('parameters')->orderBy('name')->get();

        return view('doctor.lab-orders.create', compact('patient', 'visit', 'testTypes'));
    }

    /**
     * Store a newly created lab order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type_ids' => 'required|array|min:1',
            'test_type_ids.*' => 'exists:lab_test_types,id',
            'priority' => 'required|in:normal,urgent',
            'comments' => 'nullable|string|max:500',
        ]);

        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;

        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Active branch not found for user.'
            ], 422);
        }

        $labOrder = $this->labService->createLabOrder(
            $validated,
            auth()->id(),
            $branchId
        );

        if ($request->expectsJson()) {
            // Dispatch notification
            $technicians = \App\Models\User::whereHas('roles.permissions', function ($q) {
                $q->where('name', 'manage_lab');
            })->whereHas('branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            })->get();
            
            $notificationService = app(\App\Services\NotificationService::class);
            foreach ($technicians as $tech) {
                $notificationService->newLabOrder($tech, $labOrder);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lab order created successfully',
                'lab_order' => $labOrder->load('items.labTestType')
            ]);
        }

        return redirect()->route('doctor.lab-orders.show', $labOrder)
            ->with('success', 'Lab order created successfully.');
    }

    /**
     * Display the specified lab order.
     */
    public function show(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'items.labTestType.parameters', 'items.labResults']);

        return view('doctor.lab-orders.show', compact('labOrder'));
    }

    /**
     * Display lab order results.
     */
    public function results(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'items.labTestType.parameters', 'items.labResults.labTestParameter']);

        return view('doctor.lab-orders.results', compact('labOrder'));
    }
}
