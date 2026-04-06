<?php

namespace App\Http\Requests\Laboratory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LabReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'lab_test_type_id' => 'nullable|exists:lab_test_types,id',
            'doctor_id' => 'required|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            // these fields are not in lab_orders — they are metadata serialized into comments
            'test_name' => 'nullable|string|max:255',
            'test_type' => 'nullable|string|max:100',
            'sample_type' => 'nullable|string|max:100',
            'priority' => 'nullable|in:normal,urgent,emergency',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            // urgent flag not in default schema, removed or handled manually? Service ignores it or maps it.
            // Keeping boolean rules just in case
            'is_urgent' => 'boolean',
            'is_critical' => 'boolean',
        ];

        if ($this->isMethod('post')) {
            $rules['lab_number'] = 'nullable|unique:lab_orders,lab_number';
        } else {
            $rules['lab_number'] = [
                'nullable',
                Rule::unique('lab_orders', 'lab_number')->ignore($this->route('report') ?? $this->route('id')),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'doctor_id.required' => 'Please select a doctor.',
            'doctor_id.exists' => 'The selected doctor does not exist.',
            'test_name.required' => 'Test name is required.',
            'test_type.required' => 'Test type is required.',
            'sample_type.required' => 'Sample type is required.',
            'priority.required' => 'Priority is required.',
            'lab_number.unique' => 'This lab number is already in use.',
        ];
    }
}
