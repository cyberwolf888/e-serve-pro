<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLkmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('lkm'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_published' => ['required', 'boolean'],
        ];
    }
}
