<?php

// FR-GR-15 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use App\Models\Lkm;
use Illuminate\Foundation\Http\FormRequest;

class StoreLkmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Lkm::class, $this->route('class')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*.name' => ['required', 'string', 'max:255', 'distinct:ignore_case'],
            'roles.*.instructions' => ['required', 'string'],
            'roles.*.description' => ['required', 'string'],
            'roles.*.sop_items' => ['required', 'array', 'min:1'],
            'roles.*.sop_items.*' => ['required', 'string', 'max:1000'],
        ];
    }
}
