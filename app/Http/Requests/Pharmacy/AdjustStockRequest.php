<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('update_stock');
    }

    public function rules(): array
    {
        return [
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
        ];
    }
}