<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    // return $this->user()->hasRole(['pharmacy', 'admin']);
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:add,purchase,remove,adjust',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'unit_cost' => 'nullable|numeric|min:0',
            'batch_id' => 'nullable|exists:medicine_batches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Please select an action',
            'action.in' => 'Invalid action selected',
            'quantity.required' => 'Please enter quantity',
            'quantity.min' => 'Quantity must be at least 1',
            'reason.required' => 'Please provide a reason for the adjustment',
            'expiry_date.after' => 'Expiry date must be in the future',
        ];
    }
}