<?php

// FR-SW-08 / BR-09 / §9 / M7.9

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedLkmProofUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $parts = is_string($value) ? parse_url($value) : false;
        $hosts = ['drive.google.com', 'docs.google.com', 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'];

        if (! is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || ! in_array(strtolower($parts['host'] ?? ''), $hosts, true)) {
            $fail('Tautan bukti harus memakai HTTPS dari Google Drive, Google Docs, atau YouTube.');
        }
    }
}
