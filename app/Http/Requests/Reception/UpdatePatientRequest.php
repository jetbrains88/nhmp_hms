<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('edit_patients');
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')->id;
        
        return [
            'cnic' => [
                'nullable',
                'string',
                'size:13',
                Rule::unique('patients')->ignore($patientId)->whereNull('deleted_at')
            ],
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            
            // NHMP Employee fields
            'is_nhmp' => 'sometimes|boolean',
            'designation_id' => 'nullable|exists:designations,id',
            'office_id' => 'nullable|exists:offices,id',
            'rank' => 'nullable|string|max:100',
        ];
    }
}