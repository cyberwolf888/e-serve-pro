<?php

// FR-GR-09 / DATA-10 / §9 / M5

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('question'));
    }

    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string'],
            'options' => ['required', 'array', 'min:2', 'max:26'],
            'options.*' => ['required', 'string', 'max:1024'],
            'correct_option' => ['required', 'integer', Rule::in(array_keys((array) $this->input('options', [])))],
        ];
    }
}
