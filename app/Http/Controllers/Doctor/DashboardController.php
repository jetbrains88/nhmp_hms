<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Diagnosis;
use App\Models\Prescription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display doctor dashboard
     */
    public function index()
    {
        $doctorId = auth()->id();
        $branchId = session('current_branch_id');

        $stats = [
            'total_patients_today' => Visit::where('doctor_id', $doctorId)
                ->whereDate('created_at', today())
                ->count(),
            'waiting_patients' => Visit::where('doctor_id', $doctorId)
                ->where('status', 'waiting')
                ->whereDate('created_at', today())
                ->count(),
            'in_progress_patients' => Visit::where('doctor_id', $doctorId)
                ->where('status', 'in_progress')
                ->whereDate('created_at', today())
                ->count(),
            'average_consultation_time' => 15, // Placeholder or calculate if data exists
            'prescriptions_today' => Prescription::where('prescribed_by', $doctorId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        $today_visits = Visit::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('created_at', today())
            ->get();

        $recent_patients = Patient::where('branch_id', $branchId)
            ->latest()
            ->take(10)
            ->get();

        return view('doctor.dashboard', compact('stats', 'today_visits', 'recent_patients'));
    }

    /**
     * Search patients (AJAX)
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('q');
        $branchId = session('current_branch_id');

        $patients = Patient::where('branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('cnic', 'LIKE', "%{$query}%")
                    ->orWhere('emrn', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'cnic', 'emrn']);

        return response()->json($patients);
    }

    /**
     * Get patients by office (for NHMP)
     */
    public function getOfficePatients(Request $request, $officeId)
    {
        $patients = Patient::whereHas('employeeDetail', function ($q) use ($officeId) {
            $q->where('office_id', $officeId);
        })->get(['id', 'name', 'cnic', 'emrn']);

        return response()->json($patients);
    }

    /**
     * View patient history
     */
    public function patientHistory($patientId)
    {
        $patient = Patient::with(['visits.diagnoses', 'visits.vitals'])
            ->findOrFail($patientId);

        return view('doctor.patients.history', compact('patient'));
    }
}
