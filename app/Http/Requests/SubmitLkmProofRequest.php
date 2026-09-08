<?php

// FR-SW-08 / BR-09 / §9 / M7.9

namespace App\Http\Requests;

use App\Rules\AllowedLkmProofUrl;
use Illuminate\Foundation\Http\FormRequest;

class SubmitLkmProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('submitProof', $this->route('lkm'));
    }

    public function rules(): array
    {
        return ['proof_url' => ['required', 'string', 'url', 'max:1024', new AllowedLkmProofUrl]];
    }
}
