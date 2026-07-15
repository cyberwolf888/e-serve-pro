<?php

// FR-GR-03 / §9 / M3

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddClassStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('addStudent', $this->route('class'));
    }

    public function rules(): array
    {
        return ['email' => ['required', 'email']];
    }
}
