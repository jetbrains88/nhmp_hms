<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class AddStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('update_stock');
    }

    public function rules(): array
    {
        return [
            'medicine_id' => 'required|exists:medicines,id',
            'batch_number' => 'required|string|max:100',
            'expiry_date' => 'required|date|after:today',
            'unit_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|gte:unit_price',
            'quantity' => 'required|integer|min:1',
            'rc_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }
}