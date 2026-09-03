<?php

// tests/Feature/SmokeTest.php — M0/M1 gate verification
// Confirms: app boots, DB connects, auth routes respond, Metronic layout renders.

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    /** @test — happy path: app root renders the public landing page (FR-PUB-01) */
    public function test_app_boots_and_welcome_route_returns_200(): void
    {
        $this->get('/')->assertOk()->assertSee('Kurikulum Merdeka');
    }

    /** @test — happy path: authenticated siswa dashboard renders the Metronic Demo 2 shell */
    public function test_dashboard_route_renders_metronic_layout(): void
    {
        $siswa = User::factory()->create(['is_active' => true]);
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertStatus(200)
            ->assertSee('data-kt-sticky-offset="200px"', false)
            ->assertSee('id="primary_navigation"', false)
            ->assertSee('kt-scrollable-x-auto', false)
            ->assertDontSee('kt-sidebar', false)
            ->assertSee('core.bundle.js', false);
    }

    /** @test — failure path: unknown route renders the custom 404 page */
    public function test_unknown_route_returns_404(): void
    {
        $this->get('/this-route-does-not-exist')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('illustrations/19.svg', false);
    }

    /** @test — failure path: server errors render the custom 500 page */
    public function test_server_error_renders_custom_500_page(): void
    {
        Route::get('/test-server-error', fn () => abort(500));

        $this->get('/test-server-error')
            ->assertStatus(500)
            ->assertSee('Terjadi kesalahan pada server')
            ->assertSee('illustrations/20.svg', false);
    }
}
