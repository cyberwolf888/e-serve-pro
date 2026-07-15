<?php

// FR-SA-02 / FR-AUTH-03 / §9 / M2
// ASSUMPTION: create scope extended to guru+siswa per user decision (DATA-01 wording only mentions guru).

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:guru,siswa'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ];
    }
}
