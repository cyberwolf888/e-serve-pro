<?php

// FR-SW-05 / §9 / M5

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('take', $this->route('quiz'));
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer'],
        ];
    }

    /** Every current question must have exactly one answer mapped to one of its own options. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $questions = $this->route('quiz')->questions()->with('options')->get();
                $answers = (array) $this->input('answers', []);

                foreach ($questions as $question) {
                    if (! array_key_exists($question->id, $answers)) {
                        $validator->errors()->add('answers', 'Semua soal harus dijawab.');

                        return;
                    }

                    if (! $question->options->contains('id', (int) $answers[$question->id])) {
                        $validator->errors()->add('answers', 'Jawaban tidak valid.');

                        return;
                    }
                }

                if (count($answers) !== $questions->count()) {
                    $validator->errors()->add('answers', 'Semua soal harus dijawab.');
                }
            },
        ];
    }
}
