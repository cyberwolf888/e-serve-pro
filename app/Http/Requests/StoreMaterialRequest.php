<?php

// FR-GR-04 / FR-GR-05 / BR-04 / §9 / M4

namespace App\Http\Requests;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Material::class, $this->route('class')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:figma,file'],
            'figma_url' => ['required_if:type,figma', 'url'],
            'file' => ['required_if:type,file', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }
}
