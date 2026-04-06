<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_prescriptions');
    }

    public function rules(): array
    {
        return [
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string|max:500',
        ];
    }
}