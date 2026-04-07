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
            'quantity_dispensed' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'medicine_batch_id' => 'nullable|exists:medicine_batches,id',
            'alternative_medicine_id' => 'nullable|exists:medicines,id',
        ];
    }
}