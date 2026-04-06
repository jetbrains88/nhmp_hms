<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('edit_users');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'branches' => 'sometimes|array',
            'branches.*' => 'exists:branches,id',
            'primary_branch' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ];
    }
}
