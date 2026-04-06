<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Patient selection or creation
            'patient_id' => 'nullable|exists:patients,id',

            // New patient fields (required if patient_id not provided)
            'name' => 'required_without:patient_id|string|max:255',
            'phone' => [
                'required_without:patient_id',
                'regex:/^03\d{9}$/',
                function ($attribute, $value, $fail) {
                    // Remove any spaces, dashes, parentheses
                    $cleanPhone = preg_replace('/[^0-9]/', '', $value);

                    // Ensure it starts with 03 and is 11 digits
                    if (!preg_match('/^03\d{9}$/', $cleanPhone)) {
                        $fail('Phone must be 11 digits starting with 03 (e.g., 03001234567)');
                    }
                }
            ],
            'dob' => 'required_without:patient_id|date|before:today',
            'allergies' => 'nullable|string|max:500',

            // Vitals fields
            'temperature' => [
                'required',
                'numeric',
                'min:35',
                'max:42',
                function ($attribute, $value, $fail) {
                    // Validate temperature is reasonable
                    if ($value < 97 || $value > 107.6) {
                        $fail('Temperature must be between 97°F and 107.6°F');
                    }
                }
            ],
            'pulse' => [
                'required',
                'integer',
                'min:30',
                'max:200',
                function ($attribute, $value, $fail) {
                    // Validate pulse is clinically reasonable
                    if ($value < 40 || $value > 180) {
                        $fail('Pulse rate must be between 40 and 180 BPM');
                    }
                }
            ],
            'respiratory_rate' => [
                'nullable',
                'integer',
                'min:8',
                'max:40'
            ],
            'blood_pressure_systolic' => [
                'required',
                'integer',
                'min:70',
                'max:250'
            ],
            'blood_pressure_diastolic' => [
                'required',
                'integer',
                'min:40',
                'max:150'
            ],
            'oxygen_saturation' => [
                'nullable',
                'integer',
                'min:70',
                'max:100'
            ],
            'pain_scale' => [
                'nullable',
                'integer',
                'min:0',
                'max:10'
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:1',
                'max:300',
                function ($attribute, $value, $fail) {
                    if ($value && ($value < 1 || $value > 300)) {
                        $fail('Weight must be between 1kg and 300kg');
                    }
                }
            ],
            'height' => [
                'nullable',
                'numeric',
                'min:30',
                'max:250',
                function ($attribute, $value, $fail) {
                    if ($value && ($value < 30 || $value > 250)) {
                        $fail('Height must be between 30cm and 250cm');
                    }
                }
            ],
            'blood_glucose' => [
                'nullable',
                'numeric',
                'min:30',
                'max:500'
            ],
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be 11 digits starting with 03 (e.g., 03001234567)',
            'blood_pressure_systolic.min' => 'Systolic BP must be at least 70 mmHg',
            'blood_pressure_systolic.max' => 'Systolic BP must not exceed 250 mmHg',
            'blood_pressure_diastolic.min' => 'Diastolic BP must be at least 40 mmHg',
            'blood_pressure_diastolic.max' => 'Diastolic BP must not exceed 150 mmHg',
            'oxygen_saturation.min' => 'Oxygen saturation must be at least 70%',
            'oxygen_saturation.max' => 'Oxygen saturation cannot exceed 100%',
            'temperature.min' => 'Temperature must be at least 97°C',
            'temperature.max' => 'Temperature must not exceed 107.6°C',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean and format phone number
        if ($this->has('phone')) {
            $phone = preg_replace('/[^0-9]/', '', $this->phone);
            $this->merge(['phone' => $phone]);
        }

        // Ensure blood pressure values are integers
        if ($this->has('blood_pressure_systolic')) {
            $this->merge(['blood_pressure_systolic' => (int)$this->blood_pressure_systolic]);
        }

        if ($this->has('blood_pressure_diastolic')) {
            $this->merge(['blood_pressure_diastolic' => (int)$this->blood_pressure_diastolic]);
        }

        // Calculate BMI if height and weight provided
        if ($this->filled('weight') && $this->filled('height') && $this->height > 0) {
            $heightInMeters = $this->height / 100;
            $bmi = round($this->weight / ($heightInMeters * $heightInMeters), 1);
            $this->merge(['bmi' => $bmi]);
        }
    }
}
