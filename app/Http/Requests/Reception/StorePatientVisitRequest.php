<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'patient_id' => 'nullable|exists:patients,id',

            // Vitals fields
            'temperature' => 'required|numeric|between:95,110',
            'pulse' => 'required|numeric|between:40,200',
            'blood_pressure_systolic' => 'required|integer|between:70,250',
            'blood_pressure_diastolic' => 'required|integer|between:40,150',
            'oxygen_saturation' => 'nullable|numeric|between:70,100',
            'respiratory_rate' => 'nullable|numeric|between:10,60',
            'weight' => 'nullable|numeric|between:1,300',
            'height' => 'nullable|numeric|between:50,250',
            'pain_scale' => 'nullable|integer|between:0,10',
            'blood_glucose' => 'nullable|numeric|between:20,600',
            'notes' => 'nullable|string|max:1000',
        ];

        // Add patient fields validation only if patient_id is not provided
        if (!$this->input('patient_id')) {
            $patientRules = [
                'name' => 'required|string|max:255',
                'cnic' => 'required|string|max:15',
                'phone' => 'required|string|max:20|regex:/^03\d{9}$/',
                'emergency_contact' => 'nullable|string|max:20',
                'dob' => 'required|date|before:today',
                'gender' => 'required|in:male,female,other',
                'address' => 'nullable|string|max:500',
                'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'allergies' => 'nullable|string|max:500',
                'chronic_conditions' => 'nullable|string|max:500',
                'medical_history' => 'nullable|string|max:1000',
                'is_nhmp' => 'nullable|boolean',
                'designation_id' => 'nullable|required_if:is_nhmp,true|exists:designations,id',
                'office_id' => 'nullable|required_if:is_nhmp,true|exists:offices,id',
                'rank' => 'nullable|string|max:100',
            ];

            $rules = array_merge($rules, $patientRules);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be 11 digits starting with 03 (e.g., 03001234567)',
            'dob.before' => 'Date of birth must be in the past',
            'blood_pressure_systolic.required' => 'Systolic blood pressure is required',
            'blood_pressure_diastolic.required' => 'Diastolic blood pressure is required',
            'name.required' => 'Patient name is required',
            'phone.required' => 'Phone number is required',
            'dob.required' => 'Date of birth is required',
            'gender.required' => 'Gender is required',
            'designation_id.required_if' => 'Designation is required for NHMP staff',
            'office_id.required_if' => 'Office is required for NHMP staff',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number before validation
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }

        // Convert checkbox value to boolean
        $this->merge([
            'is_nhmp' => $this->boolean('is_nhmp'),
        ]);

        // Clean CNIC
        if ($this->has('cnic')) {
            $this->merge([
                'cnic' => preg_replace('/[^0-9]/', '', $this->cnic),
            ]);
        }

        // Clean emergency contact
        if ($this->has('emergency_contact')) {
            $this->merge([
                'emergency_contact' => preg_replace('/[^0-9]/', '', $this->emergency_contact),
            ]);
        }
    }
}
