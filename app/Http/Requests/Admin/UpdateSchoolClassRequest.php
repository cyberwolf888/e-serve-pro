<?php

// FR-SA-03 / FR-GR-02 / §9 / BR-05 / M3

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('class'));
    }

    public function rules(): array
    {
        return [
            'guru_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_active', true))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('guru_id') && ! $this->userModel()?->hasRole('guru')) {
                $validator->errors()->add('guru_id', 'Pilih pengguna dengan peran guru.');
            }
        }];
    }

    private function userModel(): ?User
    {
        return User::find($this->integer('guru_id'));
    }
}
