<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_diagnoses');
    }

    public function rules(): array
    {
        return [
            'visit_id' => 'required|exists:visits,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'required|string',
            'doctor_notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'followup_date' => 'nullable|date|after:today',
            'is_chronic' => 'boolean',
            'is_urgent' => 'boolean',
            'severity' => 'required|in:mild,moderate,severe,critical',
            'has_prescription' => 'boolean',
            'medical_advice' => 'nullable|string',
            'illness_tag_ids' => 'nullable|array',
            'illness_tag_ids.*' => 'exists:illness_tags,id',
            'medical_specialty_ids' => 'nullable|array',
            'medical_specialty_ids.*' => 'exists:medical_specialties,id',
        ];
    }
}