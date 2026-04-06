<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:waiting,in_progress,completed',
            'temperature' => 'nullable|numeric|between:95,110',
            'pulse' => 'nullable|numeric|between:40,200',
            'blood_pressure_systolic' => 'nullable|integer|between:70,250',
            'blood_pressure_diastolic' => 'nullable|integer|between:40,150',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
