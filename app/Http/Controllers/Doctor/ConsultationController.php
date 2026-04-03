<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\LabTestType;
use App\Models\Visit;
use App\Models\IllnessTag;
use App\Models\ExternalSpecialist;
use App\Models\PrescriptionAbbreviation;
use App\Services\VisitService;
use App\Services\VitalService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    protected $visitService;
    protected $vitalService;

    public function __construct(VisitService $visitService, VitalService $vitalService)
    {
        $this->visitService = $visitService;
        $this->vitalService = $vitalService;
    }

    /**
     * Display doctor's dashboard with queue
     */
    public function index(Request $request)
    {
        $totalWaiting = $this->visitService->getWaitingCountForDoctor(auth()->id());
        
        $myQueue = Visit::with(['patient', 'latestVital'])
            ->where('doctor_id', auth()->id())
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $recentCompleted = Visit::with(['patient', 'diagnoses'])
            ->where('doctor_id', auth()->id())
            ->where('status', 'completed')
            ->latest()
            ->limit(10)
            ->get();
        
        $filters = $request->only(['status', 'search', 'is_nhmp', 'date']);
        
        return view('doctor.consultations.index', compact('totalWaiting', 'myQueue', 'recentCompleted', 'filters'));
    }

    /**
     * Get consultations data for AJAX
     */
    public function data(Request $request)
    {
        $query = Visit::with(['patient', 'latestVital'])
            ->where('doctor_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['waiting', 'in_progress', 'completed']);
        }

        if ($request->filled('is_nhmp')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('is_nhmp', $request->is_nhmp === '1');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $perPage = $request->input('per_page', 10);
        $consultations = $query->latest()->paginate($perPage);

        return response()->json($consultations);
    }

    /**
     * Get statistics for doctor
     */
    public function stats()
    {
        $doctorId = auth()->id();
        
        $stats = [
            'total' => Visit::where('doctor_id', $doctorId)->count(),
            'waiting' => Visit::where('doctor_id', $doctorId)->where('status', 'waiting')->count(),
            'in_progress' => Visit::where('doctor_id', $doctorId)->where('status', 'in_progress')->count(),
            'completed' => Visit::where('doctor_id', $doctorId)->where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Start a consultation
     */
    public function start(Visit $visit)
    {
        if ($visit->doctor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($visit->status !== 'waiting') {
            return response()->json(['success' => false, 'message' => 'Visit is not in waiting status'], 400);
        }

        $this->visitService->updateStatus($visit, 'in_progress');

        return response()->json(['success' => true]);
    }

    /**
     * Cancel a consultation
     */
    public function cancel(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->visitService->updateStatus($visit, 'cancelled');

        if ($request->filled('reason')) {
            $visit->update(['notes' => $visit->notes . "\nCancellation Reason: " . $request->reason]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show consultation form for a visit.
     * Scoped to the authenticated doctor's branch (multi-tenant).
     */
    public function show(Request $request, Visit $visit)
    {
        // Authorization: visit must belong to current doctor
        if ($visit->doctor_id !== auth()->id()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        // Multi-tenant branch scoping: visit must belong to the doctor's active branch
        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;
        if ($branchId && (int) $visit->branch_id !== (int) $branchId) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Visit does not belong to your current branch'], 403);
            }
            abort(403, 'Visit does not belong to your current branch.');
        }

        // Eager-load all required relations including lab orders
        $visit->load([
            'patient',
            'latestVital',
            'diagnoses' => function ($query) {
                $query->with(['prescriptions.medicine'])->latest();
            },
            'labOrders' => function ($query) {
                $query->with(['items.labTestType'])->latest();
            },
        ]);

        // Auto-start visit if it is still in waiting status
        if ($visit->status === 'waiting') {
            $this->visitService->updateStatus($visit, 'in_progress');
        }

        $medicines     = Medicine::active()->get();
        $labTestTypes  = LabTestType::orderBy('name')->get();
        $illnessTags   = IllnessTag::active()->orderBy('name')->get();
        $externalSpecialists = ExternalSpecialist::where('branch_id', $branchId)->active()->orderBy('name')->get();
        $prescriptionAbbreviations = PrescriptionAbbreviation::orderBy('abbreviation')->get();

        $waitingQueue = Visit::with(['patient', 'latestVital'])
            ->where('doctor_id', auth()->id())
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->limit(7)->get();

        $inProgressQueue = Visit::with(['patient', 'latestVital'])
            ->where('doctor_id', auth()->id())
            ->where('status', 'in_progress')
            ->orderBy('created_at', 'asc')
            ->limit(7)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'visit'            => $visit,       // includes diagnoses.prescriptions.medicine + labOrders.items.labTestType
                'waitingQueue'     => $waitingQueue,
                'inProgressQueue'  => $inProgressQueue,
            ]);
        }

        return view('doctor.consultations.show', compact(
            'visit', 'medicines', 'labTestTypes', 'waitingQueue', 'inProgressQueue',
            'illnessTags', 'externalSpecialists', 'prescriptionAbbreviations'
        ));
    }

    /**
     * Complete consultation
     */
    public function complete(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->id()) {
            abort(403);
        }
        
        $this->visitService->updateStatus($visit, 'completed');
        
        if ($request->wantsJson()) {
            // Check if there are prescriptions to print
            $prescription = \App\Models\Prescription::whereHas('diagnosis', function($q) use ($visit) {
                $q->where('visit_id', $visit->id);
            })->first();

            $printUrl = null;
            if ($prescription) {
                $printUrl = route('print.prescription', $prescription->id);
            }

            // Dispatch notification to Pharmacy
            $branchId = $visit->branch_id;
            $pharmacists = \App\Models\User::whereHas('roles.permissions', function ($q) {
                $q->where('name', 'dispense_medicine');
            })->whereHas('branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            })->get();
            
            $notificationService = app(\App\Services\NotificationService::class);
            foreach ($pharmacists as $pharmacist) {
                $notificationService->visitCompleted($pharmacist, $visit);
            }

            return response()->json([
                'success' => true,
                'message' => 'Consultation completed successfully',
                'print_url' => $printUrl
            ]);
        }
        
        return redirect()
            ->route('doctor.consultancy')
            ->with('success', 'Consultation completed successfully');
    }

    /**
     * Get patient medical history as JSON
     */
    public function patientHistoryJson(int $patientId)
    {
        $patient = \App\Models\Patient::findOrFail($patientId);
        
        // Fetch visits with main diagnosis
        $visits = \App\Models\Visit::where('patient_id', $patientId)
            ->with(['diagnoses' => function($q) {
                $q->with(['illnessTags', 'externalSpecialists'])->latest()->limit(1); // Get the primary/latest diagnosis
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all prescriptions across visits using relation
        $prescriptions = \App\Models\Prescription::whereHas('diagnosis.visit', function($q) use ($patientId) {
                $q->where('patient_id', $patientId);
            })
            ->with(['medicine', 'dispensations.alternativeMedicine'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all lab orders
        $labs = \App\Models\LabOrder::where('patient_id', $patientId)
            ->with(['items.labTestType', 'items.labResults'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => [
                    'chronic_conditions' => $patient->chronic_conditions,
                    'allergies' => $patient->allergies,
                ],
                'visits' => $visits,
                'prescriptions' => $prescriptions,
                'labs' => $labs
            ]
        ]);
    }

    /**
     * Record vitals for a patient from consultation view
     */
    public function recordVitals(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'required|exists:patients,id',
            'temperature' => 'nullable|numeric|between:95,110',
            'pulse' => 'nullable|integer|between:20,300',
            'blood_pressure_systolic' => 'nullable|integer|between:40,300',
            'blood_pressure_diastolic' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:5,100',
            'oxygen_saturation' => 'nullable|numeric|between:0,100',
            'oxygen_device' => 'nullable|string|max:255',
            'oxygen_flow_rate' => 'nullable|numeric|between:0,15',
            'pain_scale' => 'nullable|integer|between:0,10',
            'height' => 'nullable|numeric|between:30,250',
            'weight' => 'nullable|numeric|between:1,300',
            'bmi' => 'nullable|numeric',
            'blood_glucose' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        // Remove null values so DB defaults are used correctly (e.g. pain_scale = 0)
        $attributes = array_filter($validated, fn($val) => !is_null($val));
        
        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;
        
        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Active branch not found for user.'
            ], 422);
        }

        $vital = \App\Models\Vital::create($attributes + [
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'branch_id' => $branchId,
            'recorded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'vital' => $vital
        ]);
    }

    /**
     * Start teleconsultation
     */
    public function eConsultancy()
    {
        // This will be implemented with video conferencing integration
        return view('doctor.consultations.e-consultancy');
    }
}