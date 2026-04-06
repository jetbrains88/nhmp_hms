<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class StoreExistingPatientVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('reception');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'visit_type' => 'required|in:routine,emergency,followup',
            'vitals.temperature' => 'required|numeric|between:95,110',
            'vitals.pulse' => 'required|numeric|between:40,200',
            'vitals.blood_pressure_systolic' => 'required|integer|between:70,250',
            'vitals.blood_pressure_diastolic' => 'required|integer|between:40,150',
            'vitals.oxygen_saturation' => 'required|numeric|between:70,100',
            'vitals.respiratory_rate' => 'required|numeric|between:10,60',
            'vitals.weight' => 'nullable|numeric|between:1,300',
            'vitals.height' => 'nullable|numeric|between:50,250',
            'vitals.pain_scale' => 'nullable|integer|between:0,10',
            'vitals.blood_glucose' => 'nullable|numeric|between:20,600',
            'vitals.notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'visit_type.required' => 'Visit type is required.',
            'visit_type.in' => 'Invalid visit type selected.',
            'vitals.temperature.required' => 'Temperature is required.',
            'vitals.temperature.between' => 'Temperature must be between 95 and 110 °F.',
            'vitals.pulse.required' => 'Pulse rate is required.',
            'vitals.pulse.between' => 'Pulse rate must be between 40 and 200 BPM.',
            'vitals.blood_pressure_systolic.required' => 'Systolic blood pressure is required.',
            'vitals.blood_pressure_systolic.between' => 'Systolic blood pressure must be between 70 and 250 mmHg.',
            'vitals.blood_pressure_diastolic.required' => 'Diastolic blood pressure is required.',
            'vitals.blood_pressure_diastolic.between' => 'Diastolic blood pressure must be between 40 and 150 mmHg.',
            'vitals.oxygen_saturation.required' => 'Oxygen saturation is required.',
            'vitals.oxygen_saturation.between' => 'Oxygen saturation must be between 70% and 100%.',
        ];
    }
}
