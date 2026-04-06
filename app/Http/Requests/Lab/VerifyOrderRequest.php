<?php

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('verify_lab_reports');
    }

    public function rules(): array
    {
        return [
            'verification_notes' => 'nullable|string|max:500',
        ];
    }
}