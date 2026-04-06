<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user->hasRole('super_admin') || $user->hasPermission('create_branches');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:CMO,RMO',
            'location' => 'nullable|string|max:255',
            'office_id' => 'nullable|exists:offices,id',
            'is_active' => 'boolean',
        ];
    }
}
