<?php

// FR-GR-15 / DATA-26 / §9 / M7.9

namespace App\Rules;

use App\Models\LkmRole;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class UniqueLkmRoleName implements ValidationRule
{
    public function __construct(
        private int $lkmId,
        private ?int $ignoreId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = LkmRole::where('lkm_id', $this->lkmId)
            ->whereRaw('LOWER(name) = ?', [Str::lower((string) $value)])
            ->when($this->ignoreId, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Nama peran sudah digunakan pada LKM ini.');
        }
    }
}
