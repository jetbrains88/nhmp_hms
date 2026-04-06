<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Appointment;
use App\Services\VisitService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display reception dashboard.
     */
    public function index()
    {
        $branchId = session('current_branch_id');

        $stats = [
            'patients_today' => Patient::whereDate('created_at', today())->count(),
            'visits_today' => Visit::where('branch_id', $branchId)
                ->whereDate('created_at', today())
                ->count(),
            'appointments_today' => Appointment::where('branch_id', $branchId)
                ->whereDate('scheduled_at', today())
                ->count(),
            'waiting_count' => Visit::where('branch_id', $branchId)
                ->where('status', 'waiting')
                ->count(),
            'in_progress_count' => Visit::where('branch_id', $branchId)
                ->where('status', 'in_progress')
                ->count(),
        ];

        $recentVisits = Visit::with('patient')
            ->where('branch_id', $branchId)
            ->latest()
            ->take(10)
            ->get();

        $upcomingAppointments = Appointment::with('patient')
            ->where('branch_id', $branchId)
            ->whereDate('scheduled_at', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        return view('reception.dashboard', compact('stats', 'recentVisits', 'upcomingAppointments'));
    }

    /**
     * Check if patient exists by CNIC or EMRN.
     */
    public function checkPatientExists(Request $request)
    {
        $request->validate([
            'cnic' => 'nullable|string',
            'emrn' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $query = Patient::where('branch_id', auth()->user()->current_branch_id);

        $query->where(function ($q) use ($request) {
            if ($request->filled('cnic')) {
                $q->orWhere('cnic', $request->cnic);
            }
            if ($request->filled('phone')) {
                $q->orWhere('phone', $request->phone);
            }
            if ($request->filled('emrn')) {
                $q->orWhere('emrn', $request->emrn);
            }
        });

        // If no search criteria provided, return false
        if (!$request->filled('cnic') && !$request->filled('phone') && !$request->filled('emrn')) {
            return response()->json(['exists' => false]);
        }

        $patient = $query->first();

        return response()->json([
            'exists' => (bool) $patient,
            'patient' => $patient ? [
                'id' => $patient->id,
                'name' => $patient->name,
                'cnic' => $patient->cnic,
                'emrn' => $patient->emrn,
                'phone' => $patient->phone,
            ] : null,
        ]);
    }

    /**
     * Quick search for patients.
     */
    public function quickSearch(Request $request)
    {
        $query = $request->get('q') ?? $request->get('search');
        $branchId = session('current_branch_id');
        
        \Log::info('quickSearch: Start', [
            'query' => $query,
            'session_branch_id' => $branchId,
            'auth_user_branch_id' => auth()->user()->current_branch_id,
            'user_id' => auth()->id()
        ]);

        // Explicitly set branch if missing (fallback)
        if (!$branchId) {
            $branchId = auth()->user()->current_branch_id;
            \Log::warning('quickSearch: branchId missing from session, using user fallback', ['branch_id' => $branchId]);
        }

        try {
            $patients = Patient::where('branch_id', $branchId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('cnic', 'LIKE', "%{$query}%")
                        ->orWhere('emrn', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'cnic', 'emrn', 'phone']);

            \Log::info('quickSearch: Results found', [
                'count' => $patients->count(),
                'sql' => Patient::where('branch_id', $branchId)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('cnic', 'LIKE', "%{$query}%")
                            ->orWhere('emrn', 'LIKE', "%{$query}%")
                            ->orWhere('phone', 'LIKE', "%{$query}%");
                    })->toSql(),
                'bindings' => [$branchId, "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"]
            ]);

            return response()->json($patients);
        } catch (\Exception $e) {
            \Log::error('quickSearch: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([]);
        }
    }
}
