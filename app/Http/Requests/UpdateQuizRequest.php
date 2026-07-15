<?php

// FR-GR-09 / §9 / M5

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('quiz'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->opens_at && $this->closes_at
                    && Carbon::parse($this->closes_at)->lte(Carbon::parse($this->opens_at))) {
                    $validator->errors()->add('closes_at', 'Waktu selesai harus setelah waktu mulai.');
                }
            },
        ];
    }
}
