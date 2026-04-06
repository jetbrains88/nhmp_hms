<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('update_appointment_status');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'cancellation_reason' => 'required_if:status,cancelled|string|max:500',
        ];
    }
}