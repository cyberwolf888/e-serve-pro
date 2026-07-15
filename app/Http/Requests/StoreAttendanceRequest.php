<?php

// FR-GR-07 / §9 / M4

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recordAttendance', $this->route('meeting'));
    }

    public function rules(): array
    {
        return [
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alfa'])],
        ];
    }
}
