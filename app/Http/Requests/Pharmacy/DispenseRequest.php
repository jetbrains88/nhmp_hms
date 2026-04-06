<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class DispenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('dispense_prescriptions');
    }

    public function rules(): array
    {
        return [
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'batch_number' => 'nullable|string',
        ];
    }
}