<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_patients');
    }

    public function rules(): array
    {
        return [
            'cnic' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('patients')->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            
            // NHMP Employee fields
            'is_nhmp' => 'sometimes|boolean',
            'designation_id' => 'required_if:is_nhmp,true|exists:designations,id',
            'office_id' => 'required_if:is_nhmp,true|exists:offices,id',
            'rank' => 'nullable|string|max:100',
            
            // Dependent fields
            'parent_id' => 'nullable|required_if:category,dependent|exists:patients,id',
            'relationship' => [
                'nullable',
                'required_if:category,dependent',
                'in:self,spouse,child,parent,other'
            ],
            'category' => 'required|in:private,nhmp,dependent',

            // Optional Visit & Vitals fields (for integrated flow)
            'visit_type' => 'sometimes|string|in:routine,emergency,followup',
            'complaint' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'vitals' => 'sometimes|array',
            'vitals.temperature' => 'nullable|numeric',
            'vitals.pulse' => 'nullable|integer',
            'vitals.respiratory_rate' => 'nullable|integer',
            'vitals.blood_pressure_systolic' => 'nullable|integer',
            'vitals.blood_pressure_diastolic' => 'nullable|integer',
            'vitals.oxygen_saturation' => 'nullable|integer',
            'vitals.weight' => 'nullable|numeric',
            'vitals.height' => 'nullable|numeric',
            'vitals.pain_scale' => 'nullable|integer',
            'vitals.blood_glucose' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'cnic.unique' => 'A patient with this CNIC already exists.',
            'cnic.size' => 'CNIC must be exactly 13 digits.',
            'designation_id.required_if' => 'Designation is required for NHMP employees.',
            'office_id.required_if' => 'Office is required for NHMP employees.',
            'relationship.required_with' => 'Relationship is required for dependents.',
        ];
    }
}