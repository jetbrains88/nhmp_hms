<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:50',
            'instructions' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1|max:1000',
            'refills_allowed' => 'integer|min:0|max:10',
            'priority' => ['nullable', Rule::in(['normal', 'urgent', 'emergency'])],
        ];
    }

    public function messages(): array
    {
        return [
            'medicine_id.required' => 'Please select a medicine',
            'dosage.required' => 'Dosage is required',
            'frequency.required' => 'Frequency is required',
            'duration.required' => 'Duration is required',
            'quantity.min' => 'Quantity must be at least 1',
        ];
    }
}