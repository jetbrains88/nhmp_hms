<?php

namespace App\Http\Requests\Laboratory;

use Illuminate\Foundation\Http\FormRequest;

class LabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'result_values' => 'required|array',
            'result_values.*' => 'required|string',
            'normal_range' => 'nullable|array',
            'normal_range.*' => 'nullable|string',
            'units' => 'nullable|array',
            'units.*' => 'nullable|string',
            'interpretation' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:1000',
            'is_critical' => 'boolean',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'result_values.required' => 'Test results are required.',
            'result_values.array' => 'Results must be in proper format.',
            'interpretation.required' => 'Interpretation of results is required.',
            'file.mimes' => 'File must be PDF, image, or document.',
            'file.max' => 'File size must not exceed 5MB.',
        ];
    }
}
