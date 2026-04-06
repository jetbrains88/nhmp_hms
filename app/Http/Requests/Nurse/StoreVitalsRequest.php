<?php

namespace App\Http\Requests\Nurse;

use Illuminate\Foundation\Http\FormRequest;

class StoreVitalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('record_vitals');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'temperature' => 'nullable|numeric|between:90,110',
            'pulse' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'oxygen_saturation' => 'nullable|integer|between:50,100',
            'oxygen_device' => 'nullable|string|in:room_air,nasal_cannula,mask,ventilator',
            'oxygen_flow_rate' => 'nullable|numeric|between:0.5,15',
            'pain_scale' => 'nullable|integer|between:0,10',
            'height' => 'nullable|numeric|between:30,250',
            'weight' => 'nullable|numeric|between:1,300',
            'blood_glucose' => 'nullable|numeric|between:20,600',
            'heart_rate' => 'nullable|integer|between:30,200',
            'notes' => 'nullable|string|max:500',
        ];
    }
}