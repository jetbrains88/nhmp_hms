<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reception\StartVisitRequest;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\User;
use App\Services\VisitService;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display queue for the current branch
     */
    public function queue()
    {
        $waitingQueue = $this->visitService->getQueue(
            auth()->user()->current_branch_id,
            'waiting'
        );
        
        $inProgress = $this->visitService->getQueue(
            auth()->user()->current_branch_id,
            'in_progress'
        );
        
        return view('reception.queue', compact('waitingQueue', 'inProgress'));
    }

    /**
     * Show form to start a new visit
     */
    public function create(Patient $patient = null)
    {
        $doctors = User::whereHas('roles', function ($query) {
            $query->where('name', 'doctor');
        })->get();
        
        return view('reception.visits.create', compact('patient', 'doctors'));
    }

    /**
     * Store a newly created visit
     */
    public function store(StartVisitRequest $request)
    {
        $visit = $this->visitService->startVisit(
            $request->patient_id,
            auth()->user()->current_branch_id,
            $request->doctor_id,
            $request->only(['visit_type', 'complaint', 'notes'])
        );
        
        // Record vitals if provided
        if ($request->has('temperature') || $request->has('pulse')) {
            \App\Models\Vital::create([
                'patient_id' => $visit->patient_id,
                'visit_id' => $visit->id,
                'recorded_by' => auth()->id(),
                'branch_id' => auth()->user()->current_branch_id,
                'recorded_at' => now(),
                'temperature' => $request->temperature,
                'pulse' => $request->pulse,
                'blood_pressure_systolic' => $request->blood_pressure_systolic,
                'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
                'oxygen_saturation' => $request->oxygen_saturation,
                'respiratory_rate' => $request->respiratory_rate,
                'weight' => $request->weight,
                'height' => $request->height,
                'pain_scale' => $request->pain_scale,
                'blood_glucose' => $request->blood_glucose,
                'notes' => $request->vitals_notes ?? $request->notes,
            ]);
        }
        
        // Trigger notification for doctor if assigned
        if ($visit->doctor_id) {
            // Notification logic will be added in Phase 5
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Visit started successfully. Token: ' . $visit->queue_token,
                'visit' => $visit
            ]);
        }

        return redirect()
            ->route('reception.queue')
            ->with('success', 'Visit started successfully. Token: ' . $visit->queue_token);
    }

    /**
     * Display the specified visit
     */
    public function show(Visit $visit)
    {
        \Log::info('Reception\VisitController@show: Entry', [
            'visit_id' => $visit->id,
            'user_id' => auth()->id(),
            'branch_id' => $visit->branch_id,
            'session_branch_id' => session('current_branch_id'),
        ]);

        try {
            $this->authorize('view', $visit);
            \Log::info('Reception\VisitController@show: Authorized successfully');
        } catch (\Exception $e) {
            \Log::error('Reception\VisitController@show: Authorization failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        $visit->load(['patient', 'doctor', 'vitals', 'diagnoses.prescriptions']);
        
        return view('reception.visits.show', compact('visit'));
    }

    /**
     * Update visit status and record vitals
     */
    public function updateStatus(Request $request, Visit $visit)
    {
        $request->validate([
            'status' => 'required|in:waiting,in_progress,completed,cancelled'
        ]);
        
        $this->authorize('update', $visit);
        
        $visit = $this->visitService->updateStatus($visit, $request->status);

        // Record vitals if provided (often sent from receptionist modal)
        if ($request->has('temperature') || $request->has('pulse')) {
            $vitals = $request->only([
                'temperature', 'pulse', 'respiratory_rate', 
                'blood_pressure_systolic', 'blood_pressure_diastolic', 
                'oxygen_saturation', 'blood_glucose', 'notes'
            ]);
            
            $vitals['patient_id'] = $visit->patient_id;
            $vitals['visit_id'] = $visit->id;
            $vitals['recorded_by'] = auth()->id();
            $vitals['branch_id'] = auth()->user()->current_branch_id;
            $vitals['recorded_at'] = now();

            \App\Models\Vital::create($vitals);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Visit updated successfully'
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Visit status updated successfully');
    }

    /**
     * Call next patient
     */
    public function callNext()
    {
        $nextPatient = Visit::where('branch_id', auth()->user()->current_branch_id)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();
        
        if ($nextPatient) {
            $this->visitService->updateStatus($nextPatient, 'in_progress');
            
            // Trigger notification for doctor
            // Notification logic will be added in Phase 5
            
            return redirect()
                ->back()
                ->with('success', 'Called patient: ' . $nextPatient->patient->name);
        }
        
        return redirect()
            ->back()
            ->with('info', 'No patients in queue');
    }

    /**
     * AJAX: Get waiting visits for current branch
     */
    public function getWaitingVisits()
    {
        $visits = Visit::with(['patient', 'doctor', 'latestVital'])
            ->where('branch_id', auth()->user()->current_branch_id)
            ->where('status', 'waiting')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'patients' => $visits,
            'count' => $visits->count()
        ]);
    }

    /**
     * AJAX: Get in-progress visits for current branch
     */
    public function getInProgressVisits()
    {
        $visits = Visit::with(['patient', 'doctor', 'latestVital'])
            ->where('branch_id', auth()->user()->current_branch_id)
            ->where('status', 'in_progress')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'patients' => $visits,
            'count' => $visits->count()
        ]);
    }

    /**
     * AJAX: Get vitals for a specific visit
     */
    public function getVitals(Visit $visit)
    {
        $visit->load(['patient', 'vitals']);

        return response()->json([
            'success' => true,
            'visit' => [
                'id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'patient_name' => $visit->patient->name,
                'status' => $visit->status,
                'queue_token' => $visit->queue_token
            ],
            'vitals' => $visit->vitals->last() // Get latest vitals
        ]);
    }
}