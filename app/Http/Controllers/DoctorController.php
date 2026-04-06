<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\ConsultationRequest;
use App\Http\Requests\Doctor\DiagnosisRequest;
use App\Http\Requests\Doctor\PatientSearchRequest;
use App\Http\Requests\Doctor\PrescriptionRequest;
use App\Models\Diagnosis;
use App\Models\Medicine;
use App\Models\Office;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\Doctor\ConsultationService;
use App\Services\Doctor\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    protected DoctorService $doctorService;
    protected ConsultationService $consultationService;

    public function __construct(DoctorService $doctorService, ConsultationService $consultationService)
    {
        $this->middleware(['auth', 'role:doctor']);
        $this->doctorService = $doctorService;
        $this->consultationService = $consultationService;
    }

    public function dashboard()
    {
        $data = $this->doctorService->getDashboardData();
        $totalWaiting = Visit::waiting()->count();

        return view('doctor.dashboard', array_merge($data, [
            'totalWaiting' => $totalWaiting,
            'pageTitle' => 'Doctor Dashboard',
            'pageDescription' => 'Overview of your medical practice',
        ]));
    }

    public function consultancy(Request $request)
    {
        Log::info("consultancy request: ", $request->all());
        $filters = $request->only(['status', 'search', 'date', 'is_nhmp']);
        $doctorId = auth()->id();

        $visits = $this->doctorService->getDoctorRepository()->getDoctorVisits($doctorId, $filters);
        $totalWaiting = Visit::waiting()->count();
        Log::info('consultancy totalWaiting: ' . $totalWaiting);
        Log::info('consultancy visits: ', ['Visits_List' => $visits->items()]);

        return view('doctor.consultancy', [
            'visits' => $visits,
            'filters' => $filters,
            'totalWaiting' => $totalWaiting,
            'pageTitle' => 'Patient Consultations',
            'pageDescription' => 'Manage patient consultations and diagnoses',
        ]);
    }
    // Add these methods to DoctorController.php

    public function getConsultationsData(Request $request)
    {
        $filters = $request->only(['status', 'search', 'is_nhmp', 'date', 'end_date']);
        $doctorId = auth()->id();

        $visits = $this->doctorService->getDoctorRepository()->getDoctorVisits($doctorId, $filters, $request->per_page ?? 10);

        return response()->json($visits);
    }

    public function getConsultationStats()
    {
        $doctorId = auth()->id();

        $stats = [
            'total' => Visit::where('doctor_id', $doctorId)->whereNot('status', 'cancelled')->count(),
            'waiting' => Visit::where('doctor_id', $doctorId)->where('status', 'waiting')->count(),
            'in_progress' => Visit::where('doctor_id', $doctorId)->where('status', 'in_progress')->count(),
            'completed' => Visit::where('doctor_id', $doctorId)->where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }

    public function eConsultancy()
    {
        $offices = Office::where('is_active', true)->get();
        // Get NHMP patients with their latest vitals using a custom subquery
        $recentPatients = Patient::where('is_nhmp', true)
            ->with(['vitals' => function ($query) {
                $query->latest()->limit(1); // Get only the most recent vitals record
            }])
            ->with(['designation', 'office'])
            ->latest()
            ->limit(10)
            ->get();

        return view('doctor.e-consultancy', [
            'offices' => $offices,
            'recentPatients' => $recentPatients,
            'pageTitle' => 'E-Consultation',
            'pageDescription' => 'Remote consultations and telemedicine',
        ]);
    }

    public function startConsultation(Request $request, $visitId)
    {
        $result = $this->doctorService->startConsultation($visitId);

        Log::info('startConsultation: ', $result);
        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('doctor.consultation.view', $visitId)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function viewConsultation($visitId)
    {
        $visit = $this->doctorService->getDoctorRepository()->getVisitDetails($visitId);

        Log::info('View-Consultation: ' . $visit); //        if (!$visit || $visit->doctor_id !== auth()->id()) {
        //            abort(403, 'Unauthorized access to this consultation');
        //        }

        $medicines = Medicine::active()->inStock()->get();

        return view('doctor.consultation-view', [
            'visit' => $visit,
            'medicines' => $medicines,
            'pageTitle' => 'Consultation - ' . $visit->patient->name,
            'pageDescription' => 'Patient consultation and diagnosis',
        ]);
    }

    public function storeDiagnosis(DiagnosisRequest $request)
    {
        try {
            $diagnosis = $this->doctorService->createDiagnosis($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Diagnosis saved successfully',
                'diagnosis' => $diagnosis,
                'html' => view('doctor.partials.diagnosis-item', compact('diagnosis'))->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save diagnosis: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storePrescription(PrescriptionRequest $request)
    {
        try {
            $prescription = $this->doctorService->createPrescription($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Prescription added successfully',
                'prescription' => $prescription->load('medicine'),
                'html' => view('doctor.partials.prescription-item', compact('prescription'))->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add prescription: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function completeConsultation($visitId)
    {
        $visit = Visit::findOrFail($visitId);
        //        if ($visit->doctor_id !== auth()->id()) {
        //            abort(403, 'Unauthorized');
        //        }

        if ($visit->markAsCompleted()) {
            return response()->json([
                'success' => true,
                'message' => 'Consultation completed successfully',
                'redirect' => route('doctor.consultancy'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cannot complete consultation. Please add at least one diagnosis.',
        ], 400);
    }

    public function searchPatientsAjax(PatientSearchRequest $request)
    {
        $result = $this->doctorService->searchPatients($request->input('search', ''));

        return response()->json($result);
    }

    public function getOfficePatientsAjax(Request $request, $officeId)
    {
        $patients = $this->doctorService->getOfficePatients($officeId);

        return response()->json($patients);
    }

    public function startTeleconsultation(ConsultationRequest $request)
    {
        try {
            $visit = $this->consultationService->createTelemedicineVisit($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Teleconsultation started successfully',
                'visit' => $visit,
                'redirect' => route('doctor.consultation.view', $visit->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start teleconsultation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function patientMedicalHistory($patientId)
    {
        $history = $this->consultationService->getPatientMedicalHistory($patientId);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    public function reports()
    {
        $doctorId = auth()->id();

        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStatistics($doctorId);

        return view('doctor.reports', [
            'monthlyStats' => $monthlyStats,
            'pageTitle' => 'Medical Reports',
            'pageDescription' => 'Analytics and reports of your medical practice',
        ]);
    }

    private function getMonthlyStatistics(int $doctorId): array
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $visits = Visit::where('doctor_id', $doctorId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month,
                        COUNT(*) as total_visits,
                        SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_visits,
                        AVG(CASE WHEN status = "completed" THEN TIMESTAMPDIFF(MINUTE, created_at, updated_at) END) as avg_time')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $diagnoses = Diagnosis::where('doctor_id', $doctorId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month,
                    COUNT(*) as total_diagnoses')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'visits' => $visits,
            'diagnoses' => $diagnoses,
        ];
    }

    public function downloadReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:monthly,patient,prescription',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Generate report based on type
        $reportData = $this->generateReportData(
            $request->report_type,
            $request->start_date,
            $request->end_date
        );

        // In a real application, you would generate PDF/Excel here
        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully',
            'data' => $reportData,
        ]);
    }

    private function generateReportData(string $type, string $startDate, string $endDate): array
    {
        $doctorId = auth()->id();

        switch ($type) {
            case 'monthly':
                return $this->generateMonthlyReport($doctorId, $startDate, $endDate);
            case 'patient':
                return $this->generatePatientReport($doctorId, $startDate, $endDate);
            case 'prescription':
                return $this->generatePrescriptionReport($doctorId, $startDate, $endDate);
            default:
                return [];
        }
    }

    private function generateMonthlyReport(int $doctorId, string $startDate, string $endDate): array
    {
        Log::info(
            [
                'doctorId: ' => $doctorId,
                'startDate: ' => $startDate,
                'endDate: ' => $endDate,
            ]
        );
        return [
            'summary' => $this->doctorService->getDoctorRepository()->getDoctorStats($doctorId),
            'visits' => Visit::where('doctor_id', $doctorId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['patient', 'diagnoses'])
                ->get(),
        ];
    }

    public function cancelConsultation(Request $request, $visitId)
    {
        Log::info('cancelConsultation visitId: ' . $visitId);
        $request->validate(['reason' => 'nullable|string|max:500']);
        $reason = $request->reason ?? 'cancelled';

        $success = $this->doctorService->getVisitRepository()->cancelConsultation($visitId, $reason);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Consultation cancelled successfully' : 'Unable to cancel consultation',
        ]);
    }

    public function recordVitals(Request $request)
    {
        $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'required|exists:patients,id',
            'temperature' => 'nullable|numeric|between:95,110',
            'pulse' => 'nullable|numeric|between:40,200',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'respiratory_rate' => 'nullable|numeric|between:10,60',
            'oxygen_saturation' => 'nullable|numeric|between:70,100',
            'pain_scale' => 'nullable|integer|between:0,10',
            'height' => 'nullable|numeric|between:50,250',
            'weight' => 'nullable|numeric|between:1,300',
            'bmi' => 'nullable|numeric|between:10,60',
            'blood_glucose' => 'nullable|numeric|between:20,600',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $vital = $this->consultationService->recordVitals(
                $request->visit_id,
                $request->patient_id,
                $request->only([
                    'temperature',
                    'pulse',
                    'blood_pressure_systolic',
                    'blood_pressure_diastolic',
                    'respiratory_rate',
                    'oxygen_saturation',
                    'pain_scale',
                    'height',
                    'weight',
                    'bmi',
                    'blood_glucose',
                    'notes'
                ])
            );

            return response()->json([
                'success' => true,
                'message' => 'Vitals recorded successfully',
                'vital' => $vital,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record vitals: ' . $e->getMessage(),
            ], 500);
        }
    }
}
