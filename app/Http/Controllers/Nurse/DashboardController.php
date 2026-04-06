<?php

namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Vital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display nurse dashboard.
     */
    public function index()
    {
        $branchId = session('current_branch_id');

        // Today's stats
        $stats = [
            'patients_today' => Visit::where('branch_id', $branchId)
                ->whereDate('created_at', today())
                ->count(),
            'vitals_today' => Vital::where('branch_id', $branchId)
                ->whereDate('recorded_at', today())
                ->count(),
            'waiting_patients' => Visit::where('branch_id', $branchId)
                ->where('status', 'waiting')
                ->count(),
            'in_progress' => Visit::where('branch_id', $branchId)
                ->where('status', 'in_progress')
                ->count(),
        ];

        // Patients waiting for vitals
        $waitingForVitals = Visit::with(['patient'])
            ->where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->whereDoesntHave('vitals', function ($q) {
                $q->whereDate('created_at', today());
            })
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // Recent vitals recorded
        $recentVitals = Vital::with(['patient', 'recordedBy'])
            ->where('branch_id', $branchId)
            ->latest('recorded_at')
            ->take(10)
            ->get();

        // Patients with abnormal vitals today
        $abnormalVitals = Vital::with(['patient', 'visit'])
            ->where('branch_id', $branchId)
            ->whereDate('recorded_at', today())
            ->where(function ($q) {
                $q->where('temperature', '>', 99)
                    ->orWhere('temperature', '<', 97)
                    ->orWhere('pulse', '>', 100)
                    ->orWhere('pulse', '<', 60)
                    ->orWhere('blood_pressure_systolic', '>', 140)
                    ->orWhere('blood_pressure_diastolic', '>', 90)
                    ->orWhere('oxygen_saturation', '<', 95);
            })
            ->orderBy('recorded_at', 'desc')
            ->take(10)
            ->get();

        return view('nurse.dashboard', compact('stats', 'waitingForVitals', 'recentVitals', 'abnormalVitals'));
    }

    /**
     * Display patients list.
     */
    public function patients(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = Patient::where('branch_id', $branchId);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('cnic', 'LIKE', "%{$search}%")
                    ->orWhere('emrn', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')->paginate(20);

        return view('nurse.patients.index', compact('patients'));
    }

    /**
     * Display patient details.
     */
    public function patientDetail(Patient $patient)
    {
        $patient->load(['vitals' => function ($q) {
            $q->latest('recorded_at')->take(20);
        }, 'visits' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('nurse.patients.show', compact('patient'));
    }
}
