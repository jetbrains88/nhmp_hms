<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_medicines');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'form_id' => 'nullable|exists:medicine_forms,id',
            'strength_value' => 'nullable|numeric|min:0',
            'strength_unit' => 'nullable|string|max:20',
            'unit' => 'required|string|max:50',
            'category_id' => 'nullable|exists:medicine_categories,id',
            'description' => 'nullable|string',
            'reorder_level' => 'required|integer|min:0',
            'requires_prescription' => 'boolean',
            'is_global' => 'boolean',
        ];
    }
}