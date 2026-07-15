<?php

// FR-GR-06 / §9 / M4

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('meeting'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
