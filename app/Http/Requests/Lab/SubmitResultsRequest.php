<?php

namespace App\Http\Requests\Lab;

use Illuminate\Foundation\Http\FormRequest;

class SubmitResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('submit_lab_results');
    }

    public function rules(): array
    {
        return [
            'results' => 'required|array',
            'results.*' => 'required', // Value can be any type
        ];
    }
}