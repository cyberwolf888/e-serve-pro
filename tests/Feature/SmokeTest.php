<?php

// tests/Feature/SmokeTest.php — M0/M1 gate verification
// Confirms: app boots, DB connects, auth routes respond, Metronic layout renders.

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    /** @test — happy path: app root redirects guests to login */
    public function test_app_boots_and_welcome_route_returns_200(): void
    {
        $this->get('/')->assertRedirect(route('auth.login.show'));
    }

    /** @test — happy path: authenticated siswa dashboard renders Metronic layout shell */
    public function test_dashboard_route_renders_metronic_layout(): void
    {
        $siswa = User::factory()->create(['is_active' => true]);
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertStatus(200)
            ->assertSee('kt-sidebar', false)
            ->assertSee('kt-header', false)
            ->assertSee('core.bundle.js', false);
    }

    /** @test — failure path: unknown route returns 404 */
    public function test_unknown_route_returns_404(): void
    {
        $this->get('/this-route-does-not-exist')->assertStatus(404);
    }
}
