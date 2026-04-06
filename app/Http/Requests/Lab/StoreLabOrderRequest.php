<?php

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_lab_reports');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type_ids' => 'required|array|min:1',
            'test_type_ids.*' => 'exists:lab_test_types,id',
            'priority' => 'required|in:normal,urgent,emergency',
            'comments' => 'nullable|string|max:500',
        ];
    }
}