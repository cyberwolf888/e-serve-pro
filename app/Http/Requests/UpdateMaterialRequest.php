<?php

// FR-GR-04 / FR-GR-05 / BR-04 / §9 / M4

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('material'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:figma,file'],
            'figma_url' => ['required_if:type,figma', 'url'],
            // file optional on update — keeps existing file if not replaced
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }
}
