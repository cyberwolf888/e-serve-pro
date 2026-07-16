<?php

// DATA-15 / FR-GR-11 / M6

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComponentScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('grade_component'));
    }

    public function rules(): array
    {
        return [
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }
}
