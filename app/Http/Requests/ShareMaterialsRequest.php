<?php

// FR-GR-08 / §9 / M4

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShareMaterialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('share', $this->route('meeting'));
    }

    public function rules(): array
    {
        $classId = $this->route('meeting')->class_id;

        return [
            'material_ids' => ['array'],
            'material_ids.*' => [Rule::exists('materials', 'id')->where('class_id', $classId)],
        ];
    }
}
