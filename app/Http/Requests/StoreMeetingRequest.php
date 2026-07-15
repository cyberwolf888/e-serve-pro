<?php

// FR-GR-06 / §9 / M4

namespace App\Http\Requests;

use App\Models\Meeting;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Meeting::class, $this->route('class')]);
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
