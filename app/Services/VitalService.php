<?php

namespace App\Services;

use App\Models\Vital;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Str;

class VitalService
{
    /**
     * Record vitals for a patient
     */
    public function recordVitals(int $patientId, int $branchId, int $recordedBy, array $data, ?int $visitId = null): Vital
    {
        // Calculate BMI if height and weight provided
        $bmi = null;
        if (!empty($data['height']) && !empty($data['weight'])) {
            $heightInMeters = $data['height'] / 100;
            $bmi = $data['weight'] / ($heightInMeters * $heightInMeters);
            $bmi = round($bmi, 1);
        }
        
        $vital = Vital::create([
            'uuid' => (string) Str::uuid(),
            'branch_id' => $branchId,
            'patient_id' => $patientId,
            'visit_id' => $visitId,
            'recorded_by' => $recordedBy,
            'recorded_at' => now(),
            'temperature' => $data['temperature'] ?? null,
            'pulse' => $data['pulse'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? null,
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'oxygen_device' => $data['oxygen_device'] ?? null,
            'oxygen_flow_rate' => $data['oxygen_flow_rate'] ?? null,
            'pain_scale' => $data['pain_scale'] ?? 0,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
            'bmi' => $bmi,
            'blood_glucose' => $data['blood_glucose'] ?? null,
            'heart_rate' => $data['heart_rate'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        
        return $vital;
    }
    
    /**
     * Get latest vitals for a patient
     */
    public function getLatestVitals(int $patientId, int $limit = 5)
    {
        return Vital::where('patient_id', $patientId)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get vitals for a specific visit
     */
    public function getVitalsForVisit(int $visitId)
    {
        return Vital::where('visit_id', $visitId)
            ->orderBy('recorded_at', 'desc')
            ->get();
    }
    
    /**
     * Check if vitals are abnormal based on clinical guidelines
     */
    public function isAbnormal(Vital $vital): array
    {
        $abnormalities = [];
        
        // Temperature (normal: 97-99°F / 36.1-37.2°C)
        if ($vital->temperature && ($vital->temperature < 97 || $vital->temperature > 99)) {
            $abnormalities[] = 'temperature';
        }
        
        // Pulse (normal: 60-100 bpm)
        if ($vital->pulse && ($vital->pulse < 60 || $vital->pulse > 100)) {
            $abnormalities[] = 'pulse';
        }
        
        // Respiratory rate (normal: 12-20 breaths/min)
        if ($vital->respiratory_rate && ($vital->respiratory_rate < 12 || $vital->respiratory_rate > 20)) {
            $abnormalities[] = 'respiratory_rate';
        }
        
        // Blood pressure (normal: <120/80)
        if ($vital->blood_pressure_systolic && $vital->blood_pressure_systolic > 120) {
            $abnormalities[] = 'blood_pressure_systolic';
        }
        if ($vital->blood_pressure_diastolic && $vital->blood_pressure_diastolic > 80) {
            $abnormalities[] = 'blood_pressure_diastolic';
        }
        
        // Oxygen saturation (normal: 95-100%)
        if ($vital->oxygen_saturation && $vital->oxygen_saturation < 95) {
            $abnormalities[] = 'oxygen_saturation';
        }
        
        // BMI (normal: 18.5-24.9)
        if ($vital->bmi && ($vital->bmi < 18.5 || $vital->bmi > 24.9)) {
            $abnormalities[] = 'bmi';
        }
        
        return $abnormalities;
    }
}
