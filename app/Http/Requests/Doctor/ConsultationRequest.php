<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'consultation_type' => ['required', 'in:in_person,telemedicine,office_visit'],
            'consultation_notes' => 'nullable|string|max:2000',
            'vitals' => 'nullable|array',
            'vitals.temperature' => 'nullable|numeric|between:95,110',
            'vitals.pulse' => 'nullable|numeric|between:40,200',
            'vitals.blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'vitals.blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'vitals.oxygen_saturation' => 'nullable|numeric|between:70,100',
            'vitals.respiratory_rate' => 'nullable|numeric|between:10,60',
        ];
    }
}