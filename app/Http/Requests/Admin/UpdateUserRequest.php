<?php

// FR-SA-02 / §9 / M2

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // BR-05: active check is in UserPolicy::update (reactivation uses toggleStatus, not this)
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$userId}"],
            // Role is immutable — never accepted from input (ASSUMPTION: per user decision)
            'password' => ['nullable', 'string', Password::min(8), 'confirmed'],
        ];
    }
}
