<?php

namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nurse\StoreVitalsRequest;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\VitalService;
use Illuminate\Http\Request;

class VitalController extends Controller
{
    protected $vitalService;

    public function __construct(VitalService $vitalService)
    {
        $this->vitalService = $vitalService;
    }

    /**
     * Show form to record vitals
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
        
        return view('nurse.vitals.create', compact('patient', 'visit'));
    }

    /**
     * Store vitals
     */
    public function store(StoreVitalsRequest $request)
    {
        $vital = $this->vitalService->recordVitals(
            $request->patient_id,
            auth()->user()->current_branch_id,
            auth()->id(),
            $request->validated(),
            $request->visit_id
        );
        
        // Check for abnormalities
        $abnormalities = $this->vitalService->isAbnormal($vital);
        
        if (!empty($abnormalities)) {
            // Trigger notification for doctor
            // Notification logic will be added in Phase 5
        }
        
        $message = 'Vitals recorded successfully';
        
        if ($request->visit_id) {
            return redirect()
                ->route('doctor.consultancy.show', $request->visit_id)
                ->with('success', $message);
        }
        
        return redirect()
            ->route('nurse.dashboard')
            ->with('success', $message);
    }

    /**
     * Show patient vitals history
     */
    public function history(Patient $patient)
    {
        $vitals = $this->vitalService->getLatestVitals($patient->id, 20);
        
        return view('nurse.vitals.history', compact('patient', 'vitals'));
    }
}