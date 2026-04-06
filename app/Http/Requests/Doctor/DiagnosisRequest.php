<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'visit_id' => 'required|exists:visits,id',
            'diagnosis' => 'required|string|max:1000',
            'severity' => ['required', Rule::in(['mild', 'moderate', 'severe', 'critical'])],
            'doctor_notes' => 'nullable|string|max:2000',
            'is_chronic' => 'boolean',
            'is_urgent' => 'boolean',
            'has_prescription' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'visit_id.required' => 'Visit ID is required',
            'diagnosis.required' => 'Diagnosis is required',
            'severity.required' => 'Please select severity level',
            'follow_up_date.after' => 'Follow-up date must be in the future',
        ];
    }
}