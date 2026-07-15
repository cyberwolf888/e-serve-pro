<?php

// FR-GR-09 / DATA-10 / §9 / M5

namespace App\Http\Requests;

use App\Models\QuizQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [QuizQuestion::class, $this->route('quiz')]);
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
