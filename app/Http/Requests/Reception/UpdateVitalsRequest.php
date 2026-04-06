<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVitalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'temperature' => 'nullable|numeric|between:95,110',
            'pulse' => 'nullable|numeric|between:40,200',
            'respiratory_rate' => 'nullable|numeric|between:10,60',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'oxygen_saturation' => 'nullable|numeric|between:70,100',
            'pain_scale' => 'nullable|integer|between:0,10',
            'height' => 'nullable|numeric|between:50,250',
            'weight' => 'nullable|numeric|between:1,300',
            'blood_glucose' => 'nullable|numeric|between:20,600',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
