<?php

// FR-AUTH-01 / §3.2 / NFR-08 — Demo 2 navigation targets the correct role dashboard,
// hides links for other roles, and keeps the generic dashboard redirect available.

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationDashboardLinkTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_super_admin_navigation_shows_only_admin_links(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('admin.users.index'), false)
            ->assertSee(route('admin.monitoring'), false)
            ->assertDontSee(route('guru.classes.index'), false)
            ->assertDontSee(route('siswa.classes.index'), false);
    }

    public function test_guru_navigation_shows_only_guru_links(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee(route('guru.dashboard'), false)
            ->assertSee(route('guru.classes.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('siswa.classes.index'), false);
    }

    public function test_siswa_navigation_shows_only_siswa_links(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee(route('siswa.dashboard'), false)
            ->assertSee(route('siswa.classes.index'), false)
            ->assertSee(route('siswa.grades.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('guru.classes.index'), false);
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
