<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class StartVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_visits');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'visit_type' => 'required|in:routine,emergency,followup',
            'complaint' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            
            // Vitals fields
            'temperature' => 'nullable|numeric',
            'pulse' => 'nullable|integer',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'oxygen_saturation' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'pain_scale' => 'nullable|integer',
            'blood_glucose' => 'nullable|numeric',
            'vitals_notes' => 'nullable|string|max:500',
        ];
    }
}