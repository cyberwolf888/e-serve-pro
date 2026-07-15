<?php

// FR-SW-03 / BR-01 / §9 / M3

namespace App\Http\Requests;

use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JoinSchoolClassRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['class_code' => strtoupper((string) $this->input('class_code'))]);
    }

    public function authorize(): bool
    {
        return $this->user()->can('join', SchoolClass::class);
    }

    public function rules(): array
    {
        return ['class_code' => ['required', 'string', Rule::exists('classes', 'class_code')->where('is_active', true)]];
    }
}
