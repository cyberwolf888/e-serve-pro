<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use App\Rules\UniqueLkmRoleName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLkmRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageRoles', $this->route('lkm'));
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                new UniqueLkmRoleName($this->route('lkm')->id, $this->route('role')->id),
            ],
            'instructions' => ['required', 'string'],
            'description' => ['required', 'string'],
            'sop_items' => ['required', 'array', 'min:1'],
            'sop_items.*' => ['required', 'string', 'max:1000'],
        ];
    }
}
