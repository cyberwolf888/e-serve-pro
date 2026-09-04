<?php

// FR-PUB-01 — public landing page at `/`: guests see the marketing page,
// authenticated users are redirected to their role dashboard.

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_guest_sees_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Food and Beverage Service')
            ->assertSee('Universitas Pendidikan Ganesha')
            ->assertSee('TKT 6')
            ->assertSee(route('auth.login.show'), false)
            ->assertSee(route('auth.register.show'), false);
    }

    public function test_landing_page_uses_e_serve_pro_branding(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('E-SERVEPro')
            ->assertDontSee('PRO-BI SMART');
    }

    public function test_super_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get('/')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guru_is_redirected_to_guru_dashboard(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)
            ->get('/')
            ->assertRedirect(route('guru.dashboard'));
    }

    public function test_siswa_is_redirected_to_siswa_dashboard(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get('/')
            ->assertRedirect(route('siswa.dashboard'));
    }
}
