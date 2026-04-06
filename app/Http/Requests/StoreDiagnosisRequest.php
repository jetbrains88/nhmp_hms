<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'doctor_notes' => 'required|string|min:10',
            'recommendations' => 'nullable|string',
            'followup_date' => 'nullable|date|after:today',
            'severity' => 'required|in:mild,moderate,severe,critical',
            'prescriptions' => 'nullable|array',
            'prescriptions.*.medicine_id' => 'required_with:prescriptions|exists:medicines,id',
            'prescriptions.*.dosage' => 'required_with:prescriptions.*.medicine_id|string|max:100',
            'prescriptions.*.frequency' => 'required_with:prescriptions.*.medicine_id|integer|min:1',
            'prescriptions.*.duration' => 'required_with:prescriptions.*.medicine_id|string|max:50',
            'prescriptions.*.quantity' => 'required_with:prescriptions.*.medicine_id|integer|min:1',
            'prescriptions.*.instructions' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_notes.required' => 'Clinical notes are required for diagnosis',
            'prescriptions.*.medicine_id.exists' => 'Selected medicine is invalid',
            'prescriptions.*.quantity.min' => 'Quantity must be at least 1',
        ];
    }
}