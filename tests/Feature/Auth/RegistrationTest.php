<?php

// FR-AUTH-02 / FR-AUTH-03 / FR-AUTH-06

namespace Tests\Feature\Auth;

use App\Models\User;

class RegistrationTest extends AuthTestCase
{
    // Happy path: siswa can self-register
    public function test_siswa_can_register(): void
    {
        $response = $this->post(route('auth.register'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('siswa.dashboard'));

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertTrue((bool) $user->is_active);
    }

    // Failure: duplicate email rejected (FR-AUTH-06)
    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $this->post(route('auth.register'), [
            'name' => 'Budi Lain',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    // Failure: password too short
    public function test_short_password_is_rejected(): void
    {
        $this->post(route('auth.register'), [
            'name' => 'Budi',
            'email' => 'budi2@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    // FR-AUTH-03: guru/admin cannot be created via public register route
    // (the register endpoint only ever assigns siswa — so even if someone
    //  posts a role field, it is silently ignored)
    public function test_registered_user_is_always_siswa(): void
    {
        $this->post(route('auth.register'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'super_admin', // injected, must be ignored
        ]);

        $user = User::where('email', 'hacker@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('super_admin'));
    }
}
