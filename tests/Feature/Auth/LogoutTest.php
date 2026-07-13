<?php

// FR-AUTH-05 / BR-06

namespace Tests\Feature\Auth;

use App\Models\User;

class LogoutTest extends AuthTestCase
{
    // Happy path: logout clears session
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('siswa');

        $this->actingAs($user)
            ->post(route('auth.logout'))
            ->assertRedirect(route('auth.login.show'));

        $this->assertGuest();
    }

    // BR-06: logout event logged
    public function test_logout_event_logged(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('siswa');

        $this->actingAs($user)->post(route('auth.logout'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'event_type' => 'logout',
        ]);
    }

    // Failure: guest cannot hit logout
    public function test_guest_cannot_logout(): void
    {
        $this->post(route('auth.logout'))->assertRedirect(route('auth.login.show'));
    }
}
