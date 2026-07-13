<?php

// FR-AUTH-02 / BR-06

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Register a new siswa account.
     * FR-AUTH-02 / FR-AUTH-03 — only siswa role; never accept role from input.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // cast hashes automatically
            'is_active' => true,
        ]);

        $user->assignRole('siswa');

        return $user;
    }

    /**
     * Write an activity log entry. BR-06.
     */
    public function logActivity(
        ?User $user,
        string $eventType,
        Request $request,
        ?string $description = null,
        mixed $subject = null,
    ): void {
        ActivityLog::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'description' => $description,
            'ip_address' => $request->ip(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id ?? null,
            'created_at' => now(),
        ]);
    }
}
