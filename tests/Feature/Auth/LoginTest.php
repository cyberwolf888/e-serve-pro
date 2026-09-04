<?php

// FR-AUTH-01 / BR-06 / BR-05

namespace Tests\Feature\Auth;

use App\Models\User;

class LoginTest extends AuthTestCase
{
    public function test_login_page_describes_the_f_and_b_learning_platform(): void
    {
        $this->get(route('auth.login.show'))
            ->assertOk()
            ->assertSee('E-SERVEPro LEARNING')
            ->assertSee('F&amp;B Service', false)
            ->assertSee('Universitas Pendidikan Ganesha');
    }

    // NFR-03: HTTPS pages must not generate mixed-content asset or form URLs.
    public function test_login_page_generates_https_urls_behind_a_tls_proxy(): void
    {
        $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'bi-pro-smart.test',
            'X-Forwarded-Port' => '443',
        ])->get(route('auth.login.show', absolute: false))
            ->assertOk()
            ->assertSee('https://bi-pro-smart.test/build/assets/', false)
            ->assertSee('https://bi-pro-smart.test/assets/js/core.bundle.js', false)
            ->assertSee('https://bi-pro-smart.test/login', false);
    }

    // Happy path: siswa login redirects to siswa dashboard
    public function test_siswa_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123'), 'is_active' => true]);
        $user->assignRole('siswa');

        $this->post(route('auth.login'), ['email' => $user->email, 'password' => 'secret123'])
            ->assertRedirect(route('siswa.dashboard'));
    }

    // Happy path: login event written to activity_logs (BR-06)
    public function test_login_event_logged(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123'), 'is_active' => true]);
        $user->assignRole('siswa');

        $this->post(route('auth.login'), ['email' => $user->email, 'password' => 'secret123']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'event_type' => 'login',
        ]);
    }

    // Role-based redirect: guru goes to guru dashboard
    public function test_guru_redirected_to_guru_dashboard(): void
    {
        $guru = User::factory()->create(['password' => bcrypt('secret123'), 'is_active' => true]);
        $guru->assignRole('guru');

        $this->post(route('auth.login'), ['email' => $guru->email, 'password' => 'secret123'])
            ->assertRedirect(route('guru.dashboard'));
    }

    // Role-based redirect: super_admin goes to admin dashboard
    public function test_super_admin_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['password' => bcrypt('secret123'), 'is_active' => true]);
        $admin->assignRole('super_admin');

        $this->post(route('auth.login'), ['email' => $admin->email, 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));
    }

    // Failure: wrong password
    public function test_wrong_password_rejected(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct'), 'is_active' => true]);
        $user->assignRole('siswa');

        $this->post(route('auth.login'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }

    // BR-05: inactive user cannot log in
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123'), 'is_active' => false]);
        $user->assignRole('siswa');

        $this->post(route('auth.login'), ['email' => $user->email, 'password' => 'secret123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
