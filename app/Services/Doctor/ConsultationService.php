<?php

namespace App\Services\Doctor;

use App\Models\Visit;
use App\Models\Vital;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class ConsultationService
{
    public function createTelemedicineVisit(array $data): Visit
    {
        $visit = Visit::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => Auth::id(),
            'visit_type' => 'telemedicine',
            'status' => 'in_progress',
            'notes' => $data['consultation_notes'] ?? null,
        ]);

        if (isset($data['vitals'])) {
            $this->recordVitals($visit->id, $data['patient_id'], $data['vitals']);
        }

        return $visit->load('patient');
    }

    public function recordVitals(int $visitId, int $patientId, array $vitalsData): Vital
    {
        return Vital::create(array_merge($vitalsData, [
            'visit_id' => $visitId,
            'patient_id' => $patientId,
            'recorded_by' => Auth::id(),
            'recorded_at' => now(),
        ]));
    }

    public function getPatientMedicalHistory(int $patientId): array
    {
        $patient = Patient::with([
            'visits' => function ($query) {
            $query->with(['diagnoses.prescriptions.medicine', 'latestVital'])
                ->orderByDesc('created_at')
                ->limit(10);
        },
            'labReports' => function ($query) {
            $query->orderByDesc('created_at')->limit(10);
        },
            // 'allergies',
            // 'chronicConditions',
        ])->findOrFail($patientId);

        return [
            'patient' => $patient,
            'statistics' => [
                'total_visits' => $patient->visits->count(),
                'total_prescriptions' => $patient->visits->count(),
                // 'chronic_conditions' => $patient->chronic_conditions ? count(json_decode($patient->chronic_conditions, true)) : 0,
                // 'allergies' => $patient->allergies ? count(json_decode($patient->allergies, true)) : 0,
            ],
        ];
    }
}