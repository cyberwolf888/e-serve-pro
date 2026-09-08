<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use App\Rules\AllowedLkmProofUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateLkmSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('correctSubmission', $this->route('assignment'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('assignment')?->reflection_submitted_at !== null) {
            $this->merge(['sop_checks' => (array) $this->input('sop_checks', [])]);
        }
    }

    public function rules(): array
    {
        $rules = ['proof_url' => ['required', 'string', 'url', 'max:1024', new AllowedLkmProofUrl]];

        if ($this->route('assignment')->reflection_submitted_at !== null) {
            $rules += [
                'sop_checks' => ['present', 'array'],
                'sop_checks.*' => ['integer', 'distinct'],
            ];
        }

        return $rules;
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $assignment = $this->route('assignment')->loadMissing('role');

            if ($assignment->reflection_submitted_at !== null && collect($this->input('sop_checks'))->contains(
                fn ($index) => $index < 0 || $index >= count($assignment->role->sop_items)
            )) {
                $validator->errors()->add('sop_checks', 'Pilihan refleksi tidak valid.');
            }
        }];
    }
}
