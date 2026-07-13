<?php

// §3.2 RBAC — role middleware enforced on every route group

namespace Tests\Feature\Auth;

use App\Models\User;

class RbacTest extends AuthTestCase
{
    // super_admin cannot access siswa routes
    public function test_super_admin_blocked_from_siswa_routes(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('siswa.dashboard'))
            ->assertForbidden();
    }

    // super_admin cannot access guru routes
    public function test_super_admin_blocked_from_guru_routes(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('guru.dashboard'))
            ->assertForbidden();
    }

    // guru cannot access admin routes
    public function test_guru_blocked_from_admin_routes(): void
    {
        $guru = User::factory()->create(['is_active' => true]);
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    // siswa cannot access admin routes
    public function test_siswa_blocked_from_admin_routes(): void
    {
        $siswa = User::factory()->create(['is_active' => true]);
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    // guest is redirected to login on protected routes
    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('auth.login.show'));
        $this->get(route('guru.dashboard'))->assertRedirect(route('auth.login.show'));
        $this->get(route('siswa.dashboard'))->assertRedirect(route('auth.login.show'));
    }

    // authenticated user hitting guest routes is redirected to their dashboard
    public function test_authenticated_user_redirected_from_login_page(): void
    {
        $siswa = User::factory()->create(['is_active' => true]);
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('auth.login.show'))
            ->assertRedirect(route('siswa.dashboard'));
    }
}
