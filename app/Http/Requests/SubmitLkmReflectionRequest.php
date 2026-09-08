<?php

// FR-SW-08 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitLkmReflectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('submitReflection', $this->route('lkm'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['sop_checks' => (array) $this->input('sop_checks', [])]);
    }

    public function rules(): array
    {
        return [
            'sop_checks' => ['present', 'array'],
            'sop_checks.*' => ['integer', 'distinct'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $assignment = $this->route('lkm')->assignments()
                ->where('student_id', $this->user()->id)
                ->with('role')
                ->first();

            if ($assignment && collect($this->input('sop_checks'))->contains(
                fn ($index) => $index < 0 || $index >= count($assignment->role->sop_items)
            )) {
                $validator->errors()->add('sop_checks', 'Pilihan refleksi tidak valid.');
            }
        }];
    }
}
