<?php

// FR-GR-12 / BR-03 / §9 / M6

namespace App\Http\Requests;

use App\Models\Quiz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateGradeComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('grade_component'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'between:0,100'],
            'quiz_id' => ['nullable', 'integer', 'exists:quizzes,id'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->quiz_id && Quiz::find($this->quiz_id)?->class_id !== $this->route('class')->id) {
                $validator->errors()->add('quiz_id', 'Kuis harus berasal dari kelas ini.');
            }
        }];
    }
}
