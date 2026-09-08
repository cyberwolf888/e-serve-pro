<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLkmAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reassign', $this->route('assignment'));
    }

    public function rules(): array
    {
        return [
            'lkm_role_id' => [
                'required',
                'integer',
                Rule::exists('lkm_roles', 'id')->where('lkm_id', $this->route('lkm')->id),
            ],
        ];
    }
}
