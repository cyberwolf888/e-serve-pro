<?php

// DATA-27 / FR-SA-08 / FR-GR-11 / FR-GR-15 / §9 / M7.9

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeLkmSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('correctSubmission', $this->route('assignment'));
    }

    public function rules(): array
    {
        return [
            'score' => ['required', 'numeric', 'between:0,100'],
        ];
    }
}
