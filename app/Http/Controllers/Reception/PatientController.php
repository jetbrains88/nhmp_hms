<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reception\StorePatientRequest;
use App\Http\Requests\Reception\UpdatePatientRequest;
use App\Http\Requests\Reception\StoreDependentRequest;
use App\Models\Patient;
use App\Models\Designation;
use App\Models\Office;
use App\Models\Visit;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        $query = $request->get('search');

        if ($query) {
            $patients = $this->patientService->searchPatients($query, $branchId);
        } else {
            $patients = Patient::with(['latestVisit.latestVital', 'employeeDetail.designation', 'employeeDetail.office'])
                ->where('branch_id', $branchId)
                ->latest()
                ->paginate(15);
        }

        // Stats for the cards
        $hasNhmpCol      = Schema::hasColumn('patients', 'is_nhmp');
        $baseQuery       = Patient::where('branch_id', $branchId);
        $totalPatients   = (clone $baseQuery)->count();
        $activePatients  = (clone $baseQuery)->where('is_active', true)->count();
        $nhmpPatients    = $hasNhmpCol ? (clone $baseQuery)->where('is_nhmp', true)->count() : 0;
        $todayPatients   = (clone $baseQuery)->whereDate('created_at', today())->count();
        $waitingPatients = \App\Models\Visit::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->whereDate('created_at', today())
            ->count();

        return view('reception.patients.index', compact(
            'patients',
            'totalPatients',
            'activePatients',
            'nhmpPatients',
            'todayPatients',
            'waitingPatients'
        ));
    }


    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        $branchId = auth()->user()->current_branch_id;
        
        $designations = Designation::orderBy('bps')->get();
        $offices = Office::where('is_active', true)->get();
        $regions = Office::where('type', 'Region')->where('is_active', true)->get();

        // Stats for the cards
        $totalPatients = Patient::where('branch_id', $branchId)->count();
        $todayPatients = Patient::where('branch_id', $branchId)->whereDate('created_at', today())->count();
        $waitingPatients = Visit::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->whereDate('created_at', today())
            ->count();
            
        $waitingPatientsList = Visit::with(['patient', 'doctor', 'latestVital'])
            ->where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('reception.patients.create', compact(
            'designations', 
            'offices', 
            'regions',
            'totalPatients', 
            'todayPatients', 
            'waitingPatients',
            'waitingPatientsList'
        ));
    }

    public function store(StorePatientRequest $request)
    {
        $patient = $this->patientService->registerPatient(
            $request->validated(),
            auth()->user()->current_branch_id
        );

        // Automatically start a visit if vitals/visit_type provided (common in reception flow)
        if ($request->has('visit_type')) {
            $visitService = app(\App\Services\VisitService::class);
            $visit = $visitService->startVisit(
                $patient->id,
                auth()->user()->current_branch_id,
                null, // No doctor assigned at registration
                $request->only(['visit_type', 'complaint', 'notes'])
            );

            // Record vitals if provided
            if ($request->has('vitals')) {
                $vitalData = $request->vitals;
                $vitalData['patient_id'] = $patient->id;
                $vitalData['visit_id'] = $visit->id;
                $vitalData['recorded_by'] = auth()->id();
                $vitalData['branch_id'] = auth()->user()->current_branch_id;
                $vitalData['recorded_at'] = now();
                
                \App\Models\Vital::create($vitalData);
            }
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patient registered' . (isset($visit) ? ' and visit started' : '') . ' successfully.',
                'patient' => $patient,
                'visit' => $visit ?? null,
                'redirect' => route('reception.patients.show', $patient)
            ]);
        }

        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', 'Patient registered successfully with OPD: ' . $patient->opd_number);
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load([
            'employeeDetail.designation',
            'employeeDetail.office',
            'children',
            'visits' => function ($query) {
                $query->latest()->limit(5);
            },
            'appointments' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);

        // Return JSON for AJAX requests (viewPatientDetails modal)
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'patient' => $patient,
            ]);
        }

        return view('reception.patients.show', compact('patient'));
    }

    /**
     * AJAX: Return patient medical history (allergies, chronic conditions, etc.)
     */
    public function medicalHistory(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load([
            'employeeDetail.designation',
            'employeeDetail.office',
        ]);

        return response()->json([
            'success' => true,
            'patient' => $patient,
        ]);
    }

    /**
     * AJAX: Return patient visit history
     */
    public function visitHistory(Patient $patient)
    {
        $this->authorize('view', $patient);

        $visits = $patient->visits()
            ->with(['doctor'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'patient' => $patient,
            'visits' => $visits,
        ]);
    }

    /**
     * Get patient history (medical history, visits, etc.)
     */
    public function history(Patient $patient)
    {
        $this->authorize('view', $patient);

        $visits = $patient->visits()
            ->with(['doctor', 'diagnoses', 'vitals'])
            ->latest()
            ->paginate(20);

        return view('reception.patients.history', compact('patient', 'visits'));
    }
    public function edit(Patient $patient)
    {
        $this->authorize('update', $patient);
        
        $designations = Designation::orderBy('bps')->get();
        $offices = Office::where('is_active', true)->get();
        
        return view('reception.patients.edit', compact('patient', 'designations', 'offices'));
    }

    /**
     * Update the specified patient
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);
        
        $patient = $this->patientService->updatePatient($patient, $request->validated());
        
        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', 'Patient updated successfully');
    }

    /**
     * Show form to add dependent
     */
    public function createDependent(Patient $patient)
    {
        $this->authorize('view', $patient);
        
        return view('reception.patients.dependents.create', compact('patient'));
    }

    /**
     * Store a dependent for the patient
     */
    public function storeDependent(StoreDependentRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);
        
        $dependent = $this->patientService->registerDependent(
            $patient->id,
            $request->validated(),
            auth()->user()->current_branch_id
        );
        
        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', 'Dependent added successfully with OPD: ' . $dependent->opd_number);
    }

    /**
     * Export patients to CSV
     */
    public function export(Request $request)
    {
        $query = Patient::with(['employeeDetail.designation', 'employeeDetail.office'])
            ->where('branch_id', auth()->user()->current_branch_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $patients = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patients_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($patients) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['OPD Number', 'Name', 'Phone', 'CNIC', 'Gender', 'Blood Group', 'Type', 'Designation', 'Office', 'Registered On']);

            foreach ($patients as $patient) {
                $type = $patient->is_nhmp ? 'NHMP' : 'Private';
                $designation = $patient->is_nhmp ? optional(optional($patient->employeeDetail)->designation)->name : 'N/A';
                $office = $patient->is_nhmp ? optional(optional($patient->employeeDetail)->office)->name : 'N/A';

                fputcsv($handle, [
                    $patient->opd_number,
                    $patient->name,
                    $patient->phone,
                    $patient->cnic,
                    ucfirst($patient->gender),
                    $patient->blood_group,
                    $type,
                    $designation,
                    $office,
                    $patient->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * AJAX: Return paginated patient list (used by patients/index.blade.php fetch)
     */
    public function list(Request $request)
    {
        $query = Patient::with(['employeeDetail.designation', 'employeeDetail.office'])
            ->where('branch_id', auth()->user()->current_branch_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%");
            });
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }
        $hasNhmpCol = Schema::hasColumn('patients', 'is_nhmp');
        $isNhmp = $request->get('is_nhmp');
        if ($hasNhmpCol) {
            if ($isNhmp === 'true' || $isNhmp === '1') {
                $query->where('is_nhmp', true);
            } elseif ($isNhmp === 'false' || $isNhmp === '0') {
                $query->where('is_nhmp', false);
            }
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sortField = in_array($request->get('sort_by'), ['name', 'created_at', 'phone'])
            ? $request->get('sort_by') : 'created_at';
        $sortDir = $request->get('sort_order') === 'asc' ? 'asc' : 'desc';

        $patients = $query->orderBy($sortField, $sortDir)
            ->paginate($request->get('per_page', 15));

        return response()->json($patients);
    }

    /**
     * AJAX: Search patients for typeahead/dropdowns
     */
    public function apiSearch(Request $request)
    {
        $search = $request->get('q') ?: $request->get('search');
        
        $query = Patient::where('branch_id', auth()->user()->current_branch_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()
            ->limit(20)
            ->get(['id', 'name', 'emrn', 'phone', 'gender']);

        return response()->json($patients);
    }

    /**
     * Delete a patient record
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        $branchId = auth()->user()->current_branch_id;
        $patient->delete();

        $hasNhmpCol = Schema::hasColumn('patients', 'is_nhmp');
        $stats = [
            'total'   => Patient::where('branch_id', $branchId)->count(),
            'active'  => Patient::where('branch_id', $branchId)->where('is_active', true)->count(),
            'nhmp'    => $hasNhmpCol ? Patient::where('branch_id', $branchId)->where('is_nhmp', true)->count() : 0,
            'today'   => Patient::where('branch_id', $branchId)->whereDate('created_at', today())->count(),
            'waiting' => \App\Models\Visit::where('branch_id', $branchId)
                ->where('status', 'waiting')
                ->whereDate('created_at', today())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Patient deleted successfully.',
            'stats'   => $stats,
        ]);
    }

    /**
     * Download CSV template for bulk patient upload
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patient_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, [
                'name', 'phone', 'dob', 'gender', 'cnic', 'blood_group',
                'address', 'allergies', 'chronic_conditions', 'medical_history',
                'is_nhmp', 'designation_id', 'office_id', 'rank'
            ]);
            fputcsv($handle, [
                'John Doe', '03001234567', '1990-01-01', 'male', '00000-0000000-0', 'O+',
                '123 Main St', 'Penicillin', 'Hypertension', 'No significant history',
                '0', '', '', ''
            ]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk upload patients from CSV
     */
    public function bulkUpload(\Illuminate\Http\Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:10240']);

        $file = $request->file('csv_file');
        $processed = 0;
        $errors = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $bom = fread($handle, 3);
            if ($bom != chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                rewind($handle);
            }
            fgetcsv($handle); // skip header
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) >= 4) {
                    try {
                        $existing = Patient::where('phone', $data[1] ?? '')->first();
                        if (!$existing) {
                            Patient::create([
                                'name' => $data[0] ?? '',
                                'phone' => $data[1] ?? '',
                                'dob' => $data[2] ?? null,
                                'gender' => $data[3] ?? 'other',
                                'cnic' => $data[4] ?? null,
                                'blood_group' => $data[5] ?? null,
                                'address' => $data[6] ?? null,
                                'allergies' => $data[7] ?? null,
                                'chronic_conditions' => $data[8] ?? null,
                                'medical_history' => $data[9] ?? null,
                                'is_nhmp' => isset($data[10]) && $data[10] == '1',
                                'branch_id' => auth()->user()->current_branch_id,
                            ]);
                            $processed++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Row error: " . $e->getMessage();
                    }
                }
            }
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk upload completed. {$processed} patients imported.",
            'processed' => $processed,
            'errors' => $errors,
        ]);
    }
}