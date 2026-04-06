<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;

class TransferStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('update_stock');
    }

    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:medicine_batches,id',
            'quantity' => 'required|integer|min:1',
            'target_branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string|max:500',
        ];
    }
}