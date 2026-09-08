<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLkmAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assign', $this->route('lkm'));
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('is_active', true),
                Rule::exists('class_members', 'student_id')->where('class_id', $this->route('class')->id),
            ],
            'lkm_role_id' => [
                'required',
                'integer',
                Rule::exists('lkm_roles', 'id')->where('lkm_id', $this->route('lkm')->id),
            ],
        ];
    }
}
