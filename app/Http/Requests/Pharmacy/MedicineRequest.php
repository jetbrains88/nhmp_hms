<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('pharmacy');
    }

    public function rules(): array
    {
        $medicineId = $this->route('medicine') ? $this->route('medicine')->id : null;

        return [
            'code' => 'required|string|max:50|unique:medicines,code,' . $medicineId,
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'form' => 'required|string|max:50',
            'strength' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'category_id' => 'nullable|exists:medicine_categories,id',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'storage_conditions' => 'nullable|string|max:255',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

// Add this method to prepare data
    public function prepareForValidation()
    {
        // Convert string boolean values to actual booleans
        $this->merge([
            'requires_prescription' => filter_var($this->requires_prescription, FILTER_VALIDATE_BOOLEAN),
            'is_active' => filter_var($this->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This medicine code already exists',
            'expiry_date.after' => 'Expiry date must be in the future',
            'selling_price.min' => 'Selling price must be positive',
            'category_id.exists' => 'Selected category does not exist',
        ];
    }
}
