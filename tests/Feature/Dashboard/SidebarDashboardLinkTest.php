<?php

// FR-AUTH-01 / §3.2 — sidebar dashboard link targets the correct role-specific dashboard
// and generic /dashboard redirect prevents 404 for any authenticated role.

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarDashboardLinkTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_super_admin_sidebar_links_to_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_guru_sidebar_links_to_guru_dashboard(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee(route('guru.dashboard'), false);
    }

    public function test_siswa_sidebar_links_to_siswa_dashboard(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee(route('siswa.dashboard'), false);
    }

    public function test_generic_dashboard_redirects_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_generic_dashboard_redirects_guru(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get('/dashboard')
            ->assertRedirect(route('guru.dashboard'));
    }

    public function test_generic_dashboard_redirects_siswa(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get('/dashboard')
            ->assertRedirect(route('siswa.dashboard'));
    }

    public function test_guest_cannot_access_dashboard_redirect(): void
    {
        $this->get('/dashboard')->assertRedirect(route('auth.login.show'));
    }
}
