<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDiagnosisRequest;
use App\Models\Diagnosis;
use App\Models\Visit;
use App\Services\DiagnosisService;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    protected $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    /**
     * Store a newly created diagnosis
     */
    public function store(StoreDiagnosisRequest $request)
    {
        $diagnosis = $this->diagnosisService->createDiagnosis(
            $request->visit_id,
            auth()->id(),
            $request->validated()
        );
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Diagnosis saved successfully',
                'diagnosis' => $diagnosis->load('visit.patient', 'doctor'),
                'html' => view('doctor.partials.diagnosis-item', ['diagnosis' => $diagnosis])->render()
            ]);
        }

        return redirect()
            ->route('doctor.consultancy.show', $request->visit_id)
            ->with('success', 'Diagnosis saved successfully');
    }

    /**
     * Show diagnosis details
     */
    public function show(Diagnosis $diagnosis)
    {
        $this->authorize('view', $diagnosis);
        
        $diagnosis->load(['visit.patient', 'doctor', 'prescriptions']);
        
        return view('doctor.diagnoses.show', compact('diagnosis'));
    }

    /**
     * Update diagnosis
     */
    public function update(StoreDiagnosisRequest $request, Diagnosis $diagnosis)
    {
        $this->authorize('update', $diagnosis);
        
        $diagnosis = $this->diagnosisService->updateDiagnosis($diagnosis, $request->validated());
        
        return redirect()
            ->route('doctor.diagnoses.show', $diagnosis)
            ->with('success', 'Diagnosis updated successfully');
    }

    /**
     * Get patient history
     */
    public function patientHistory(Visit $visit)
    {
        $patient = $visit->patient;
        
        $diagnoses = $this->diagnosisService->getPatientDiagnoses($patient->id);
        $chronic = $this->diagnosisService->getChronicConditions($patient->id);
        
        return view('doctor.diagnoses.history', compact('patient', 'diagnoses', 'chronic'));
    }
}