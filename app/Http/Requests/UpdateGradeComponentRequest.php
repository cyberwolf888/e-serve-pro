<?php

// DATA-27 / FR-GR-11 / FR-GR-12 / FR-GR-15 / BR-03 / §9 / M6

namespace App\Http\Requests;

use App\Models\Lkm;
use App\Models\Quiz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'lkm_id' => [
                'nullable',
                'integer',
                Rule::unique('grade_components')->ignore($this->route('grade_component')),
                'exists:lkms,id',
            ],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->filled('quiz_id') && $this->filled('lkm_id')) {
                $validator->errors()->add('quiz_id', 'Pilih hanya satu sumber nilai.');
                $validator->errors()->add('lkm_id', 'Pilih hanya satu sumber nilai.');
            }

            if (! $validator->errors()->has('quiz_id') && $this->filled('quiz_id') && Quiz::find($this->quiz_id)?->class_id !== $this->route('class')->id) {
                $validator->errors()->add('quiz_id', 'Kuis harus berasal dari kelas ini.');
            }

            if (! $validator->errors()->has('lkm_id') && $this->filled('lkm_id') && Lkm::find($this->lkm_id)?->class_id !== $this->route('class')->id) {
                $validator->errors()->add('lkm_id', 'LKM harus berasal dari kelas ini.');
            }
        }];
    }
}
