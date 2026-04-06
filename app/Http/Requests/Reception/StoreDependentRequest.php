<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class StoreDependentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('create_patients');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'cnic' => 'nullable|string|size:13',
            'phone' => 'nullable|string|max:20',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'relationship' => 'required|in:father,mother,husband,wife,son, daughter',
        ];
    }
}