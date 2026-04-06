<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Diagnosis;
use App\Models\Medicine;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Display a listing of prescriptions.
     */
    public function index()
    {
        $doctorId = auth()->id();
        $prescriptions = Prescription::with(['diagnosis.visit.patient', 'medicine'])
            ->where('prescribed_by', $doctorId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

    /**
     * Show the form for creating a new prescription.
     */
    public function create(Request $request)
    {
        $diagnosisId = $request->get('diagnosis_id');
        $diagnosis = Diagnosis::with('visit.patient')->findOrFail($diagnosisId);

        $medicines = Medicine::where(function ($q) {
            $q->where('branch_id', session('current_branch_id'))
                ->orWhere('is_global', true);
        })->get();

        return view('doctor.prescriptions.create', compact('diagnosis', 'medicines'));
    }

    /**
     * Store a newly created prescription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'nullable|integer',
            'morning' => 'nullable|integer|min:0',
            'evening' => 'nullable|integer|min:0',
            'night' => 'nullable|integer|min:0',
            'days' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string|max:500',
            'refills_allowed' => 'nullable|integer|min:0',
        ]);

        $branchId = session('current_branch_id') ?? auth()->user()->current_branch_id;

        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Active branch not found for user.'
            ], 422);
        }

        $prescription = $this->prescriptionService->createPrescription(
            $validated,
            auth()->id(),
            $branchId
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Prescription added successfully',
                'prescription' => $prescription->load('medicine'),
                'html' => view('doctor.partials.prescription-item', ['prescription' => $prescription])->render()
            ]);
        }

        return redirect()->route('doctor.diagnoses.show', $validated['diagnosis_id'])
            ->with('success', 'Prescription added successfully.');
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription)
    {
        $this->authorize('view', $prescription);

        $prescription->load(['diagnosis.visit.patient', 'medicine', 'prescribedBy', 'dispensations.dispensedBy']);

        return view('doctor.prescriptions.show', compact('prescription'));
    }
}
