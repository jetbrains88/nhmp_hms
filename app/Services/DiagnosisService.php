<?php

namespace App\Services;

use App\Models\Diagnosis;
use App\Models\Visit;
use Illuminate\Support\Str;

class DiagnosisService
{
    /**
     * Create a diagnosis for a visit
     */
    public function createDiagnosis(int $visitId, int $doctorId, array $data): Diagnosis
    {
        $visit = Visit::findOrFail($visitId);

        $diagnosis = Diagnosis::create([
            'uuid'             => (string) Str::uuid(),
            'branch_id'        => $visit->branch_id,
            'visit_id'         => $visitId,
            'doctor_id'        => $doctorId,
            'symptoms'         => $data['symptoms'] ?? null,
            'diagnosis'        => $data['diagnosis'] ?? null,
            'doctor_notes'     => $data['doctor_notes'] ?? null,
            'recommendations'  => $data['recommendations'] ?? null,
            'medical_advice'   => $data['medical_advice'] ?? null,
            'followup_date'    => $data['followup_date'] ?? null,
            'is_chronic'       => $data['is_chronic'] ?? false,
            'is_urgent'        => $data['is_urgent'] ?? false,
            'severity'         => $data['severity'] ?? 'mild',
            'has_prescription'  => $data['has_prescription'] ?? (!empty($data['prescriptions'])),
        ]);

        // Sync illness tags (multi-select condition tagging)
        if (isset($data['illness_tag_ids']) && is_array($data['illness_tag_ids'])) {
            $diagnosis->illnessTags()->sync($data['illness_tag_ids']);
        }

        // Sync external specialist referrals
        if (isset($data['specialist_ids']) && is_array($data['specialist_ids'])) {
            $specialistSync = [];
            foreach ($data['specialist_ids'] as $specialistId) {
                $specialistSync[$specialistId] = [
                    'referral_notes' => $data['referral_notes'] ?? null,
                ];
            }
            $diagnosis->externalSpecialists()->sync($specialistSync);
        }

        return $diagnosis;
    }

    /**
     * Update diagnosis
     */
    public function updateDiagnosis(Diagnosis $diagnosis, array $data): Diagnosis
    {
        $diagnosis->update([
            'symptoms'        => $data['symptoms'] ?? $diagnosis->symptoms,
            'diagnosis'       => $data['diagnosis'] ?? $diagnosis->diagnosis,
            'doctor_notes'    => $data['doctor_notes'] ?? $diagnosis->doctor_notes,
            'recommendations' => $data['recommendations'] ?? $diagnosis->recommendations,
            'medical_advice'  => $data['medical_advice'] ?? $diagnosis->medical_advice,
            'followup_date'   => $data['followup_date'] ?? $diagnosis->followup_date,
            'is_chronic'      => $data['is_chronic'] ?? $diagnosis->is_chronic,
            'is_urgent'       => $data['is_urgent'] ?? $diagnosis->is_urgent,
            'severity'        => $data['severity'] ?? $diagnosis->severity,
        ]);

        // Re-sync illness tags
        if (isset($data['illness_tag_ids'])) {
            $diagnosis->illnessTags()->sync((array) $data['illness_tag_ids']);
        }

        // Re-sync external specialists
        if (isset($data['specialist_ids'])) {
            $specialistSync = [];
            foreach ((array) $data['specialist_ids'] as $specialistId) {
                $specialistSync[$specialistId] = [
                    'referral_notes' => $data['referral_notes'] ?? null,
                ];
            }
            $diagnosis->externalSpecialists()->sync($specialistSync);
        }

        return $diagnosis;
    }

    /**
     * Get diagnoses for a patient
     */
    public function getPatientDiagnoses(int $patientId)
    {
        return Diagnosis::whereHas('visit', function ($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            })
            ->with(['visit', 'doctor', 'illnessTags', 'externalSpecialists'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get chronic conditions for a patient
     */
    public function getChronicConditions(int $patientId)
    {
        return Diagnosis::whereHas('visit', function ($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            })
            ->where('is_chronic', true)
            ->with(['visit', 'doctor', 'illnessTags'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}