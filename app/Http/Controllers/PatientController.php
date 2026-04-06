<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{
    /**
     * Patient Management Page
     */
    public function patientsIndex(Request $request)
    {
        try {
            $totalPatients = Patient::count();
            $todayPatients = Patient::whereDate('created_at', today())->count();
            $waitingPatients = Visit::where('status', 'waiting')->count();
            $activePatients = Patient::whereHas('visits', function ($query) {
                $query->whereDate('created_at', '>=', now()->subDays(30));
            })->count();
            $nhmpPatients = Patient::where('is_nhmp', true)->count();
            $nhmpPercentage = $totalPatients > 0 ? round(($nhmpPatients / $totalPatients) * 100, 1) : 0;

            return view('reception.patients.index', [
                'totalPatients' => $totalPatients,
                'todayPatients' => $todayPatients,
                'waitingPatients' => $waitingPatients,
                'activePatients' => $activePatients,
                'nhmpPatients' => $nhmpPatients,
                'nhmpPercentage' => $nhmpPercentage,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading patient management', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('reception.dashboard')
                ->with('error', 'Unable to load Patient Management. Please try again.');
        }
    }

    /**
     * Get patients list for AJAX requests (JSON)
     */
    public function getPatientsList(Request $request)
    {
        try {
            Log::info('Fetching patients list via AJAX', [
                'user_id' => auth()->id(),
                'filters' => $request->except(['_token', 'page', 'per_page']),
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 10)
            ]);

            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:100',
                'gender' => 'nullable|in:male,female,other',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'is_nhmp' => 'nullable|in:true,false',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'sort_by' => 'nullable|in:name,created_at,updated_at,emrn',
                'sort_order' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
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

            // Date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
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
                    'address' => $patient->address,
                    'is_nhmp' => $patient->is_nhmp,
                    'designation' => $patient->designation?->title,
                    'last_visit_date' => $lastVisit?->created_at?->format('M d, Y'),
                    'last_visit_status' => $lastVisit?->status,
                    'total_visits' => $patient->visits_count,
                    'created_at' => $patient->created_at->format('Y-m-d H:i:s'),
                ];
            });

            Log::debug('Patients list fetched successfully', [
                'total' => $patients->total(),
                'per_page' => $patients->perPage(),
                'current_page' => $patients->currentPage()
            ]);

            return response()->json($patients);

        } catch (\Exception $e) {
            Log::error('Error fetching patients list', [
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
     * Update patient details
     */
    public function updatePatient(Request $request, Patient $patient)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'cnic' => 'nullable|string|max:15',
                'dob' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'address' => 'nullable|string|max:500',
                'allergies' => 'nullable|string|max:1000',
                'chronic_conditions' => 'nullable|string|max:1000',
                'medical_history' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patient->update($validator->validated());

            Log::info('Patient updated', [
                'patient_id' => $patient->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating patient', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating patient. Please try again.'
            ], 500);
        }
    }

    /**
     * Get patient medical history
     */
    public function getMedicalHistory(Patient $patient)
    {
        try {
            $patient->load(['visits.latestVital']);

            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'allergies' => $patient->allergies,
                    'chronic_conditions' => $patient->chronic_conditions,
                    'medical_history' => $patient->medical_history,
                    'visits' => $patient->visits->map(function ($visit) {
                        return [
                            'id' => $visit->id,
                            'date' => $visit->created_at->format('Y-m-d H:i:s'),
                            'vitals' => $visit->latestVital
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching medical history', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading medical history'
            ], 500);
        }
    }

    /**
     * Get patient visit history
     */
    public function getVisitHistory(Patient $patient)
    {
        try {
            $visits = Visit::with('latestVital')
                ->where('patient_id', $patient->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'queue_token' => $visit->queue_token,
                        'visit_type' => $visit->visit_type,
                        'status' => $visit->status,
                        'created_at' => $visit->created_at->format('Y-m-d H:i:s'),
                        'vitals' => $visit->latestVital
                    ];
                });

            return response()->json([
                'success' => true,
                'visits' => $visits
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching visit history', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading visit history'
            ], 500);
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patient_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($handle, [
                'name', 'phone', 'dob', 'gender', 'cnic', 'blood_group',
                'address', 'allergies', 'chronic_conditions', 'medical_history',
                'is_nhmp', 'designation_id', 'office_id', 'rank'
            ]);

            // Example row
            fputcsv($handle, [
                'John Doe', '03001234567', '1990-01-01', 'male', '00000-0000000-0', 'O+',
                '123 Main St, City', 'Penicillin', 'Hypertension', 'No significant history',
                '0', '', '', ''
            ]);

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Bulk upload patients
     */
    public function bulkUpload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file format'
                ], 422);
            }

            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $processed = 0;
            $errors = [];

            if (($handle = fopen($path, 'r')) !== false) {
                // Skip BOM if present
                $bom = fread($handle, 3);
                if ($bom != chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                    rewind($handle);
                }

                // Skip header row
                fgetcsv($handle);

                while (($data = fgetcsv($handle)) !== false) {
                    if (count($data) >= 4) { // At least name, phone, dob, gender
                        try {
                            $patientData = [
                                'name' => $data[0] ?? '',
                                'phone' => $data[1] ?? '',
                                'dob' => $data[2] ?? '',
                                'gender' => $data[3] ?? 'other',
                                'cnic' => $data[4] ?? null,
                                'blood_group' => $data[5] ?? null,
                                'address' => $data[6] ?? null,
                                'allergies' => $data[7] ?? null,
                                'chronic_conditions' => $data[8] ?? null,
                                'medical_history' => $data[9] ?? null,
                                'is_nhmp' => isset($data[10]) && $data[10] == '1',
                                'designation_id' => $data[11] ?? null,
                                'office_id' => $data[12] ?? null,
                                'rank' => $data[13] ?? null,
                            ];

                            // Check for duplicate
                            $existing = Patient::where('phone', $patientData['phone'])->first();
                            if (!$existing) {
                                Patient::create($patientData);
                                $processed++;
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Row error: " . $e->getMessage();
                        }
                    }
                }
                fclose($handle);
            }

            Log::info('Bulk upload completed', [
                'processed' => $processed,
                'errors' => count($errors),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bulk upload completed',
                'processed' => $processed,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bulk upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing file'
            ], 500);
        }
    }

    /**
     * Export patients to CSV
     */
    public function exportPatients(Request $request): StreamedResponse
    {
        try {
            Log::info('Exporting patients to CSV', [
                'user_id' => auth()->id(),
                'export_type' => 'CSV',
                'filters' => $request->except(['_token'])
            ]);

            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:100',
                'gender' => 'nullable|in:male,female,other',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for patient export', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return new StreamedResponse(function () {
                    echo "Error: Invalid export parameters\n";
                }, 400, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="patients-error.csv"',
                ]);
            }

            $query = Patient::with(['designation', 'office']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('emrn', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('blood_group')) {
                $query->where('blood_group', $request->blood_group);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return new StreamedResponse(function () use ($query) {
                // Open output stream
                $handle = fopen('php://output', 'w');

                // Add UTF-8 BOM for Excel compatibility
                fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // CSV headers
                fputcsv($handle, [
                    'EMRN', 'Name', 'Phone', 'CNIC', 'Gender', 'Date of Birth',
                    'Age', 'Blood Group', 'Address', 'Allergies',
                    'Chronic Conditions', 'NHMP Staff', 'Designation',
                    'Office', 'Registration Date'
                ]);

                // Stream data
                $query->chunk(100, function ($patients) use ($handle) {
                    foreach ($patients as $patient) {
                        fputcsv($handle, [
                            $patient->emrn,
                            $patient->name,
                            $patient->phone,
                            $patient->cnic,
                            ucfirst($patient->gender),
                            $patient->dob ? $patient->dob->format('Y-m-d') : '',
                            $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : '',
                            $patient->blood_group,
                            $patient->address,
                            $patient->allergies,
                            $patient->chronic_conditions,
                            $patient->is_nhmp ? 'Yes' : 'No',
                            $patient->designation?->title,
                            $patient->office?->name,
                            $patient->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                });

                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="patients_' . date('Y-m-d_His') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting patients', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return new StreamedResponse(function () use ($e) {
                echo "Error exporting data: " . $e->getMessage() . "\n";
            }, 500, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="export-error.txt"',
            ]);
        }
    }


    /**
     * Show patient details
     */
    public function showPatient(Patient $patient)
    {
        try {
            Log::info('Viewing patient details', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'emrn' => $patient->emrn,
                'user_id' => auth()->id()
            ]);

            $patient->load([
                'visits' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(5);
                },
                'visits.latestVital',
                'designation',
                'office'
            ]);

            $patientData = [
                'id' => $patient->id,
                'name' => $patient->name,
                'emrn' => $patient->emrn,
                'cnic' => $patient->cnic,
                'phone' => $patient->phone,
                'dob' => $patient->dob ? $patient->dob->format('Y-m-d') : null,
                'age' => $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : null,
                'gender' => $patient->gender,
                'blood_group' => $patient->blood_group,
                'address' => $patient->address,
                'allergies' => $patient->allergies,
                'chronic_conditions' => $patient->chronic_conditions,
                'medical_history' => $patient->medical_history,
                'is_nhmp' => $patient->is_nhmp,
                'designation' => $patient->designation?->title,
                'office' => $patient->office?->name,
                'rank' => $patient->rank,
                'created_at' => $patient->created_at->format('Y-m-d H:i:s'),
                'visits' => $patient->visits->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'queue_token' => $visit->queue_token,
                        'status' => $visit->status,
                        'visit_type' => $visit->visit_type,
                        'created_at' => $visit->created_at->format('Y-m-d H:i:s'),
                        'vitals' => $visit->latestVital ? [
                            'temperature' => $visit->latestVital->temperature,
                            'pulse' => $visit->latestVital->pulse,
                            'blood_pressure' => $visit->latestVital->blood_pressure_systolic . '/' . $visit->latestVital->blood_pressure_diastolic,
                            'oxygen_saturation' => $visit->latestVital->oxygen_saturation,
                        ] : null
                    ];
                })
            ];

            Log::debug('Patient details loaded successfully', [
                'patient_id' => $patient->id,
                'visit_count' => $patient->visits->count()
            ]);

            return response()->json([
                'success' => true,
                'patient' => $patientData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching patient details', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load patient details. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a patient
     */
    public function deletePatient(Patient $patient)
    {
        try {
            Log::warning('Attempting to delete patient', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'emrn' => $patient->emrn,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);

            // Check if patient has visits
            if ($patient->visits()->count() > 0) {
                Log::warning('Cannot delete patient with visits', [
                    'patient_id' => $patient->id,
                    'visit_count' => $patient->visits()->count()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete patient with existing visits. Please archive instead.'
                ], 400);
            }

            // Store patient info for logging
            $patientInfo = [
                'id' => $patient->id,
                'name' => $patient->name,
                'emrn' => $patient->emrn,
                'created_at' => $patient->created_at
            ];

            // Perform deletion
            $patient->delete();

            Log::critical('Patient deleted successfully', [
                'deleted_patient' => $patientInfo,
                'user_id' => auth()->id(),
                'deleted_at' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting patient', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting patient. Please try again or contact support.'
            ], 500);
        }
    }


}
