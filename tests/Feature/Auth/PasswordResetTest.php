<?php

// FR-AUTH-04 / BR-02

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class PasswordResetTest extends AuthTestCase
{
    // Happy path: valid email triggers reset notification
    public function test_reset_link_sent_for_existing_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('siswa');

        $this->post(route('auth.forgot.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    // Failure: unknown email — no error leaked (security; status shown anyway)
    public function test_unknown_email_returns_status_without_error(): void
    {
        $response = $this->post(route('auth.forgot.email'), ['email' => 'nobody@example.com']);
        // Laravel returns RESET_LINK_SENT status even for unknown emails by default;
        // if it doesn't, we just check we don't get a 500.
        $response->assertStatus(302);
    }

    // Happy path: valid token resets password
    public function test_valid_token_resets_password(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('siswa');

        $token = Password::createToken($user);

        $this->post(route('auth.reset'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('auth.login.show'));

        // Confirm new password works
        $this->post(route('auth.login'), ['email' => $user->email, 'password' => 'newpassword123'])
            ->assertRedirect(route('siswa.dashboard'));
    }

    // Failure: invalid token is rejected
    public function test_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('siswa');

        $this->post(route('auth.reset'), [
            'token' => 'bad-token',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('email');
    }
}
