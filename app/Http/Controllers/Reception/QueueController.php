<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Services\VisitService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display live queue
     */
    public function index(Request $request, $branchId = null)
    {
        $branchId = $branchId ?? auth()->user()->current_branch_id;
        
        // Fetch all relevant visits for today to provide a unified collection for the view
        $visits = Visit::with(['patient', 'doctor'])
            ->where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'in_progress', 'completed'])
            ->orderBy('created_at', 'asc')
            ->get();

        $waitingQueue = $visits->where('status', 'waiting');
        $inProgress = $visits->where('status', 'in_progress');
        
        // For public view, don't show sensitive patient info
        if (!$request->user()) {
            $visits->makeHidden(['patient.cnic', 'patient.phone', 'patient.address']);
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'waiting' => $waitingQueue->values(),
                'in_progress' => $inProgress->values(),
                'updated_at' => now()->toIso8601String(),
            ]);
        }
        
        return view('reception.queue.live', compact('visits', 'waitingQueue', 'inProgress', 'branchId'));
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
            
            // Broadcast to queue display
            broadcast(new \App\Events\QueueUpdated($nextPatient->branch_id));
            
            return response()->json([
                'success' => true,
                'patient' => $nextPatient->patient->name,
                'token' => $nextPatient->queue_token,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No patients in queue',
        ]);
    }

    /**
     * Get current queue status (API)
     */
    public function status(Request $request, $branchId)
    {
        $waitingCount = Visit::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->count();
        
        $estimatedWaitTime = $waitingCount * 15; // 15 minutes per patient estimate
        
        return response()->json([
            'waiting_count' => $waitingCount,
            'estimated_wait_minutes' => $estimatedWaitTime,
            'current_token' => Visit::where('branch_id', $branchId)
                ->where('status', 'in_progress')
                ->value('queue_token'),
        ]);
    }
}