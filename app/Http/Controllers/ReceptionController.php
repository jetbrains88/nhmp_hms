<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reception\StorePatientVisitRequest;
use App\Http\Requests\Reception\UpdateVisitRequest;
use App\Http\Requests\StoreExistingPatientVisitRequest;
use App\Models\Designation;
use App\Models\Office;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\ReceptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReceptionController extends Controller
{
    protected ReceptionService $service;

    public function __construct(ReceptionService $service)
    {
        $this->service = $service;
        $this->middleware(['auth', 'role:reception']);
    }

    public function index()
    {
        try {
            Log::info('Reception dashboard accessed', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'ip_address' => request()->ip()
            ]);

            $data = $this->service->getDashboardData();

            // Add existing patients with pagination
            $existingPatients = Patient::orderBy('created_at', 'desc')->paginate(10);

            return view('reception.index', array_merge($data, [
                'designations' => Designation::orderBy('title')->get(),
                'offices' => Office::where('type', 'Office')
                    ->where('is_active', 1)->orderBy('id')->get(),
                'regions' => Office::where('type', 'Region')
                    ->where('is_active', 1)->orderBy('id')->get(),
                'existingPatients' => $existingPatients, // Add this line
            ]));
        } catch (\Exception $e) {
            Log::error('Error loading Patient Registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Unable to load Patient Registration. Please try again.');
        }
    }

    // Add this method to your ReceptionController.php
    public function getPatients(Request $request)
    {
        try {
            $query = Patient::with(['visits' => function ($q) {
                $q->latest()->limit(1);
            }]);

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('emrn', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%");
                });
            }

            // Filters
            if ($request->has('gender') && $request->gender) {
                $query->where('gender', $request->gender);
            }

            if ($request->has('blood_group') && $request->blood_group) {
                $query->where('blood_group', $request->blood_group);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $patients = $query->paginate($perPage);

            // Transform data for frontend
            $patients->getCollection()->transform(function ($patient) {
                $lastVisit = $patient->visits->first();

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'emrn' => $patient->emrn,
                    'phone' => $patient->phone,
                    'cnic' => $patient->cnic,
                    'gender' => $patient->gender,
                    'age' => $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : null,
                    'blood_group' => $patient->blood_group,
                    'is_nhmp' => $patient->is_nhmp,
                    'designation' => $patient->designation ? $patient->designation->title : null,
                    'last_visit_date' => $lastVisit ? $lastVisit->created_at->format('M d, Y') : null,
                    'last_visit_status' => $lastVisit ? $lastVisit->status : null,
                    'total_visits' => $patient->visits->count(),
                    'created_at' => $patient->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json($patients);

        } catch (\Exception $e) {
            Log::error('Error fetching patients list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'data' => [],
                'current_page' => 1,
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'last_page' => 1
            ], 500);
        }
    }

    /**
     * Get existing patients with advanced filtering and pagination
     */
    public function getExistingPatients(Request $request)
    {
        try {
            Log::info('Fetching existing patients with filters', [
                'user_id' => auth()->id(),
                'filters' => $request->except(['_token', 'page', 'per_page']),
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 10)
            ]);

            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:100',
                'gender' => 'nullable|in:male,female,other',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|in:name,created_at,updated_at,emrn',
                'sort_order' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for existing patients filter', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filter parameters',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Patient::with(['lastVisit', 'designation'])
                ->select('patients.*')
                ->withCount('visits');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('emrn', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%");
                });
            }

            // Gender filter
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            // Blood group filter
            if ($request->filled('blood_group')) {
                $query->where('blood_group', $request->blood_group);
            }

            // NHMP staff filter
            if ($request->filled('is_nhmp')) {
                $query->where('is_nhmp', $request->is_nhmp == 'true');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $patients = $query->paginate($perPage);

            // Transform data for frontend
            $patients->getCollection()->transform(function ($patient) {
                $lastVisit = $patient->lastVisit;

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'emrn' => $patient->emrn,
                    'phone' => $patient->phone,
                    'cnic' => $patient->cnic,
                    'age' => $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : null,
                    'gender' => $patient->gender,
                    'blood_group' => $patient->blood_group,
                    'is_nhmp' => $patient->is_nhmp,
                    'designation' => $patient->designation?->title,
                    'last_visit_date' => $lastVisit?->created_at?->format('M d, Y'),
                    'last_visit_status' => $lastVisit?->status,
                    'total_visits' => $patient->visits_count,
                    'created_at' => $patient->created_at->format('Y-m-d H:i:s'),
                ];
            });

            Log::debug('Existing patients fetched successfully', [
                'total' => $patients->total(),
                'per_page' => $patients->perPage(),
                'current_page' => $patients->currentPage()
            ]);

            return response()->json($patients);

        } catch (\Exception $e) {
            Log::error('Error fetching existing patients', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch patients. Please try again.',
                'data' => [],
                'current_page' => 1,
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'last_page' => 1
            ], 500);
        }
    }

    /**
     * Store visit for existing patient
     */
    public function storeExistingPatientVisit(StoreExistingPatientVisitRequest $request)
    {
        try {
            Log::info('Registering visit for existing patient', [
                'user_id' => auth()->id(),
                'patient_id' => $request->patient_id,
                'visit_type' => $request->visit_type,
                'has_vitals' => !empty($request->vitals)
            ]);

            $validatedData = $request->validated();

            $result = $this->service->registerVisitForExistingPatient($validatedData);

            Log::info('Existing patient visit registered successfully', [
                'patient_id' => $result['patient']->id,
                'visit_id' => $result['visit']->id,
                'queue_token' => $result['queue_token'],
                'emrn' => $result['patient']->emrn,
                'registration_time' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visit registered successfully! Queue Token: ' . $result['queue_token'],
                'queue_token' => $result['queue_token'],
                'patient_name' => $result['patient']->name,
                'emrn' => $result['patient']->emrn,
                'visit_id' => $result['visit']->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during existing patient visit registration', [
                'errors' => $e->errors(),
                'patient_id' => $request->patient_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please fix the validation errors.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Critical error during existing patient visit registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $request->patient_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again or contact support.'
            ], 500);
        }
    }

    public function storeVisit(StorePatientVisitRequest $request)
    {
        try {
            Log::info('Starting patient registration process storeVisit():', [
                'user_id' => auth()->id(),
                'request_data' => $request->except(['_token', 'password']),
                'is_nhmp' => $request->has('is_nhmp'),
                'visit_type' => $request->get('visit_type')
            ]);

            $validatedData = $request->validated();

            Log::debug('Validated registration data', [
                'patient_name' => $validatedData['name'] ?? 'Existing patient',
                'phone' => $validatedData['phone'] ?? null,
                'visit_type' => $validatedData['visit_type'] ?? 'routine'
            ]);

            $result = $this->service->registerPatientVisit($validatedData);

            Log::info('Patient registered successfully', [
                'patient_id' => $result['patient']->id,
                'visit_id' => $result['visit']->id,
                'queue_token' => $result['queue_token'],
                'emrn' => $result['patient']->emrn,
                'registration_time' => now()->toDateTimeString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Patient registered successfully! Queue Token: ' . $result['queue_token'],
                    'queue_token' => $result['queue_token'],
                    'patient_name' => $result['patient']->name,
                    'emrn' => $result['patient']->emrn,
                    'visit_id' => $result['visit']->id,
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Patient registered successfully! Queue Token: ' . $result['queue_token'],
                'queue_token' => $result['queue_token'],
                'patient_name' => $result['patient']->name,
                'emrn' => $result['patient']->emrn,
                'visit_id' => $result['visit']->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during patient visit registration storeVisit()', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['_token', 'password'])
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the validation errors.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        } catch (\Exception $e) {
            Log::error('Critical error during patient visit registration storeVisit()', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', 'password']),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again or contact support if the problem persists.'
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again or contact support if the problem persists.');
        }
    }

    public function getInProgressVisits()
    {
        try {
            $visits = Visit::with(['patient', 'latestVital'])
                ->where('status', 'in_progress')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'name' => $visit->patient->name,
                        'phone' => $visit->patient->phone,
                        'gender' => $visit->patient->gender,
                        'queue_token' => $visit->queue_token,
                        'waiting_time' => $visit->created_at->diffForHumans(),
                        'vitals' => $visit->latestVital
                    ];
                });

            Log::debug('Fetched in-progress visits', [
                'count' => $visits->count()
            ]);

            return response()->json([
                'success' => true,
                'patients' => $visits,
                'count' => $visits->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching in-progress visits', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading in-progress patients',
                'patients' => [],
                'count' => 0
            ], 500);
        }
    }

    public function getWaitingVisits()
    {
        try {
            $waitingPatientsList = Visit::with('patient', 'latestVital')
                ->where('status', 'waiting')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::debug('Fetched waiting visits', [
                'count' => $waitingPatientsList->count()
            ]);

            $html = view('reception.partials.waiting-patients', compact('waitingPatientsList'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $waitingPatientsList->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching waiting visits', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading waiting patients',
                'html' => '<div class="p-8 text-center"><p class="text-gray-500">Error loading waiting patients</p></div>',
                'count' => 0
            ], 500);
        }
    }

    public function updateVisit(UpdateVisitRequest $request, $visitId)
    {
        try {
            Log::info('Updating patient visit status and vitals', [
                'visit_id' => $visitId,
                'user_id' => auth()->id(),
                'status' => $request->get('status'),
                'has_vitals' => $request->hasAny(['temperature', 'pulse', 'blood_pressure_systolic'])
            ]);

            $this->service->updatePatientStatus($visitId, $request->validated());

            Log::info('Visit updated successfully', [
                'visit_id' => $visitId,
                'updated_at' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visit updated successfully',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during visit update', [
                'visit_id' => $visitId,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating visit', [
                'visit_id' => $visitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating visit. Please try again.'
            ], 500);
        }
    }

    public function getVisitVitals($visitId)
    {
        try {
            Log::debug('Fetching visit vitals', [
                'visit_id' => $visitId,
                'user_id' => auth()->id()
            ]);

            $visit = $this->service->getVisitDetails($visitId);

            if (!$visit) {
                Log::warning('Visit not found when fetching vitals', [
                    'visit_id' => $visitId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Visit not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'visit' => [
                    'status' => $visit->status,
                    'patient_name' => $visit->patient->name,
                    'emrn' => $visit->patient->emrn
                ],
                'vitals' => $visit->latestVital ? [
                    'temperature' => $visit->latestVital->temperature,
                    'pulse' => $visit->latestVital->pulse,
                    'blood_pressure_systolic' => $visit->latestVital->blood_pressure_systolic,
                    'blood_pressure_diastolic' => $visit->latestVital->blood_pressure_diastolic,
                    'oxygen_saturation' => $visit->latestVital->oxygen_saturation,
                    'blood_glucose' => $visit->latestVital->blood_glucose,
                    'notes' => $visit->latestVital->notes,
                ] : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching visit vitals', [
                'visit_id' => $visitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching vitals. Please try again.'
            ], 500);
        }
    }

    public function checkPatientExists(Request $request)
    {
        try {
            Log::info('Checking patient existence', [
                'cnic' => $request->get('cnic'),
                'phone' => $request->get('phone'),
                'user_id' => auth()->id()
            ]);

            // More lenient validation
            $validator = validator($request->all(), [
                'cnic' => 'nullable|string|max:15', // Increased max length
                'phone' => 'nullable|string|max:15' // Increased max length
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed during patient existence check', [
                    'errors' => $validator->errors()->toArray(),
                    'cnic' => $request->get('cnic'),
                    'phone' => $request->get('phone')
                ]);

                return response()->json([
                    'exists' => false,
                    'error' => 'Invalid input format',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $cnic = $request->get('cnic');
            $phone = $request->get('phone');

            // Clean the inputs
            $cleanCnic = $cnic ? preg_replace('/[^0-9]/', '', $cnic) : null;
            $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;

            $patient = null;

            if ($cleanCnic && strlen($cleanCnic) >= 5) {
                $patient = Patient::where('cnic', 'like', '%' . $cleanCnic . '%')->first();
                if ($patient) {
                    Log::debug('Found patient by CNIC', [
                        'cnic' => $cleanCnic,
                        'patient_id' => $patient->id
                    ]);
                }
            }

            if (!$patient && $cleanPhone && strlen($cleanPhone) >= 10) {
                $patient = Patient::where('phone', 'like', '%' . $cleanPhone . '%')->first();
                if ($patient) {
                    Log::debug('Found patient by phone', [
                        'phone' => $cleanPhone,
                        'patient_id' => $patient->id
                    ]);
                }
            }

            $response = [
                'exists' => !is_null($patient),
                'patient' => $patient ? [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'emrn' => $patient->emrn,
                    'phone' => $patient->phone,
                    'cnic' => $patient->cnic,
                    'dob' => $patient->dob ? $patient->dob->format('Y-m-d') : null,
                    'gender' => $patient->gender,
                    'blood_group' => $patient->blood_group,
                ] : null
            ];

            Log::debug('Patient existence check result', $response);

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error checking patient existence', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cnic' => $request->get('cnic'),
                'phone' => $request->get('phone')
            ]);

            return response()->json([
                'exists' => false,
                'error' => 'Unable to check patient existence'
            ], 500);
        }
    }

    public function quickSearch(Request $request)
    {
        try {
            $validated = $request->validate([
                'search' => 'required|string|min:2|max:100'
            ]);

            Log::info('Quick search performed', [
                'search_term' => $validated['search'],
                'user_id' => auth()->id()
            ]);

            $patients = $this->service->quickSearch($validated['search']);

            Log::debug('Quick search results', [
                'result_count' => count($patients),
                'search_term' => $validated['search']
            ]);

            return response()->json($patients);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Quick search validation failed', [
                'errors' => $e->errors(),
                'search_term' => $request->get('search')
            ]);

            return response()->json([
                'error' => 'Invalid search term',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in quick search', [
                'search_term' => $request->get('search'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Search service unavailable. Please try again.'
            ], 500);
        }
    }

    public function checkPhone(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|regex:/^03\d{9}$/'
            ]);

            Log::debug('Phone check requested', [
                'phone' => $validated['phone'],
                'user_id' => auth()->id()
            ]);

            $patient = $this->service->checkPhone($validated['phone']);

            $response = [
                'exists' => !is_null($patient),
                'patient' => $patient ? [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'emrn' => $patient->emrn,
                    'dob' => $patient->dob ? $patient->dob->format('Y-m-d') : '',
                    'gender' => $patient->gender,
                    'blood_group' => $patient->blood_group,
                    'allergies' => $patient->allergies,
                    'cnic' => $patient->cnic,
                    'is_nhmp' => $patient->is_nhmp,
                    'designation_id' => $patient->designation_id,
                    'office_id' => $patient->office_id
                ] : null
            ];

            Log::debug('Phone check result', $response);

            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Phone validation failed', [
                'errors' => $e->errors(),
                'phone' => $request->get('phone')
            ]);

            return response()->json([
                'exists' => false,
                'error' => 'Invalid phone format'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error checking phone', [
                'phone' => $request->get('phone'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'exists' => false,
                'error' => 'Unable to check phone number'
            ], 500);
        }
    }
}
