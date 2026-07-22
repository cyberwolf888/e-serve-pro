<?php

// BR-06 / §9

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'event_type' => ['nullable', 'in:login,logout,quiz_attempt,attendance,other'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }
}
