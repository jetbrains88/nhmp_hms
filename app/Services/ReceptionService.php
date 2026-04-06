<?php

namespace App\Services;

use App\Models\Visit;
use App\Repositories\ReceptionRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceptionService
{
    protected ReceptionRepository $repository;

    public function __construct(ReceptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registerPatientVisit(array $data)
    {
        return DB::transaction(function () use ($data) {
            Log::info('Starting patient registration process', [
                'phone' => $data['phone'] ?? null,
                'cnic' => $data['cnic'] ?? null,
                'patient_id' => $data['patient_id'] ?? 'New Patient',
                'has_existing_patient_id' => !empty($data['patient_id'])
            ]);

            $patient = null;
            // If patient_id is provided, use existing patient
            if (!empty($data['patient_id'])) {
                Log::info('Using existing patient', ['patient_id' => $data['patient_id']]);
                $patient = $this->repository->findPatient($data['patient_id']);
                Log::info('Patient Fetched from DB::' . $patient);

                if (!$patient) {
                    throw new \Exception('Patient not found');
                }
            }
            else {
                // Check if patient already exists by CNIC or phone
                $existingPatient = $this->repository->findExistingPatient(
                    $data['cnic'] ?? null,
                    $data['phone'] ?? null
                );

                if ($existingPatient) {
                    Log::info('Patient already exists, using existing record', [
                        'patient_id' => $existingPatient->id,
                        'cnic' => $data['cnic'] ?? null,
                        'phone' => $data['phone'] ?? null
                    ]);

                    // Update existing patient information if needed
                    $patient = $this->repository->updatePatient($existingPatient->id, $data);
                }
                else {
                    Log::info('Creating new patient record');
                    $patient = $this->repository->createPatient($data);
                }
            }

            // Generate queue token
            $queueToken = $this->generateQueueToken();

            Log::info('Generated queue token', ['queue_token' => $queueToken]);

            // Prepare visit data
            $visitData = [
                'patient_id' => $patient->id,
                'queue_token' => $queueToken,
                'status' => 'waiting',
                'visit_type' => $data['visit_type'] ?? 'routine',
            ];

            // Create visit
            $visit = $this->repository->createVisit($patient->id, $queueToken, $visitData);

            Log::info('Visit created', ['visitData' => $visitData]);

            // Create vitals record
            // if (isset($data['temperature'])) {
            try {
                $vitalsData = $this->prepareVitalsData($patient->id, $data);
                $vitals = $this->repository->createVitals($visit->id, $vitalsData);
                Log::info('Vitals record created', ['vitals_id' => $vitals->id]);
            }
            catch (Exception $e) {
                Log::error('VitalsData registration Exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // }

            return [
                'patient' => $patient,
                'visit' => $visit,
                'queue_token' => $queueToken,
            ];
        });
    }

    private function generateQueueToken()
    {
        $today = now()->format('Ymd');
        $prefix = "TKN-{$today}-";

        // Get the last created visit to determine the next sequence number
        // We use lockForUpdate inside the transaction (if this is called within one) 
        // to prevent race conditions as much as possible with single row locking.
        $latestVisit = Visit::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($latestVisit && str_starts_with($latestVisit->queue_token, $prefix)) {
            // Extract the number part
            $currentNumber = (int)str_replace($prefix, '', $latestVisit->queue_token);
            $number = $currentNumber + 1;
        }
        else {
            // Start sequence for today
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function prepareVitalsData($patient_id, array $data): array
    {
        return [
            'patient_id' => $patient_id,
            'temperature' => $data['temperature'] ?? null,
            'pulse' => $data['pulse'] ?? null,
            'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? null,
            'blood_pressure' => ($data['blood_pressure_systolic'] ?? '') . '/' . ($data['blood_pressure_diastolic'] ?? ''),
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'weight' => $data['weight'] ?? null,
            'height' => $data['height'] ?? null,
            'pain_scale' => $data['pain_scale'] ?? null,
            'blood_glucose' => $data['blood_glucose'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    public function updatePatientStatus($visitId, array $data)
    {
        return $this->repository->updateVisitStatus($visitId, $data);
    }

    public function getVisitDetails($visitId): Visit
    {
        return $this->repository->getVisitWithVitals($visitId);
    }

    public function getDashboardData()
    {
        return $this->repository->getDashboardStatistics();
    }

    public function quickSearch($searchTerm)
    {
        return $this->repository->searchPatients($searchTerm);
    }

    public function checkPhone($phone)
    {
        return $this->repository->findPatientByPhone($phone);
    }

    public function registerVisitForExistingPatient(array $data)
    {
        return DB::transaction(function () use ($data) {
            Log::info('Registering visit for existing patient', [
                'patient_id' => $data['patient_id'],
                'visit_type' => $data['visit_type'] ?? 'routine',
                'user_id' => auth()->id(),
                'has_vitals' => isset($data['vitals'])
            ]);

            try {
                // Validate patient exists
                $patient = $this->repository->findPatient($data['patient_id']);

                if (!$patient) {
                    Log::error('Patient not found during existing patient visit registration', [
                        'patient_id' => $data['patient_id'],
                        'user_id' => auth()->id()
                    ]);

                    throw new \Exception('Patient not found. Please refresh and try again.');
                }

                Log::info('Found existing patient', [
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->name,
                    'emrn' => $patient->emrn
                ]);

                // Generate queue token
                $queueToken = $this->generateQueueToken();

                Log::info('Generated queue token for existing patient', [
                    'queue_token' => $queueToken,
                    'patient_id' => $patient->id
                ]);

                // Prepare visit data
                $visitData = [
                    'patient_id' => $patient->id,
                    'queue_token' => $queueToken,
                    'status' => 'waiting',
                    'visit_type' => $data['visit_type'] ?? 'routine',
                    'registered_by' => auth()->id(),
                ];

                // Create visit
                $visit = $this->repository->createVisit($patient->id, $queueToken, $visitData);

                Log::info('Visit created for existing patient', [
                    'visit_id' => $visit->id,
                    'queue_token' => $queueToken,
                    'visit_type' => $visit->visit_type
                ]);

                // Create vitals record if vitals data is provided
                if (isset($data['vitals']) && is_array($data['vitals']) && !empty($data['vitals'])) {
                    try {
                        // Extract vitals data from nested array
                        $vitalsInput = $data['vitals'];

                        $vitalsData = [
                            'patient_id' => $patient->id,
                            'visit_id' => $visit->id,
                            'temperature' => $vitalsInput['temperature'] ?? null,
                            'pulse' => $vitalsInput['pulse'] ?? null,
                            'blood_pressure_systolic' => $vitalsInput['blood_pressure_systolic'] ?? null,
                            'blood_pressure_diastolic' => $vitalsInput['blood_pressure_diastolic'] ?? null,
                            'blood_pressure' => ($vitalsInput['blood_pressure_systolic'] ?? '') . '/' . ($vitalsInput['blood_pressure_diastolic'] ?? ''),
                            'oxygen_saturation' => $vitalsInput['oxygen_saturation'] ?? null,
                            'respiratory_rate' => $vitalsInput['respiratory_rate'] ?? null,
                            'weight' => $vitalsInput['weight'] ?? null,
                            'height' => $vitalsInput['height'] ?? null,
                            'pain_scale' => $vitalsInput['pain_scale'] ?? null,
                            'blood_glucose' => $vitalsInput['blood_glucose'] ?? null,
                            'notes' => $vitalsInput['notes'] ?? null,
                            'recorded_by' => auth()->id(),
                            'recorded_at' => now(),
                        ];

                        // Remove null values to allow database defaults
                        $vitalsData = array_filter($vitalsData, function ($value) {
                                            return $value !== null;
                                        }
                                        );

                                        $vitals = $this->repository->createVitals($visit->id, $vitalsData);

                                        Log::info('Vitals record created for existing patient', [
                                            'vitals_id' => $vitals->id,
                                            'patient_id' => $patient->id,
                                            'visit_id' => $visit->id
                                        ]);
                                    }
                                    catch (Exception $e) {
                                        Log::error('Error creating vitals record for existing patient', [
                                            'error' => $e->getMessage(),
                                            'trace' => $e->getTraceAsString(),
                                            'patient_id' => $patient->id,
                                            'visit_id' => $visit->id
                                        ]);

                                    // Don't throw exception here - vitals are optional for existing patients
                                    // The visit should still be registered successfully
                                    }
                                }
                                else {
                                    Log::info('No vitals data provided for existing patient visit', [
                                        'patient_id' => $patient->id,
                                        'visit_id' => $visit->id
                                    ]);
                                }

                                // Log the successful registration
                                Log::info('Existing patient visit registered successfully', [
                                    'patient_id' => $patient->id,
                                    'patient_name' => $patient->name,
                                    'visit_id' => $visit->id,
                                    'queue_token' => $queueToken,
                                    'visit_type' => $visit->visit_type,
                                    'registration_time' => now()->toDateTimeString(),
                                    'total_visits' => $patient->visits()->count(),
                                ]);

                                return [
                                    'patient' => $patient,
                                    'visit' => $visit,
                                    'queue_token' => $queueToken,
                                ];

                            }
                            catch (\Exception $e) {
                                Log::error('Error registering visit for existing patient', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'patient_id' => $data['patient_id'] ?? 'unknown',
                                    'user_id' => auth()->id(),
                                    'data_snapshot' => [
                                        'has_phone' => isset($data['phone']),
                                        'has_vitals' => isset($data['vitals']),
                                        'visit_type' => $data['visit_type'] ?? 'not_set'
                                    ]
                                ]);

                                throw $e; // Re-throw for controller to handle
                            }
                        });
    }
}
