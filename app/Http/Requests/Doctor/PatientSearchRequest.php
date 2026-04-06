<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class PatientSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('doctor') || auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:waiting,in_progress,completed,cancelled',
            'date' => 'nullable|date',
            'office_id' => 'nullable|exists:offices,id',
            'is_nhmp' => 'nullable|boolean',
        ];
    }
}