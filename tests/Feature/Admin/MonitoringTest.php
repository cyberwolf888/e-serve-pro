<?php

// FR-SA-04 / BR-06 / NFR-02 / §11 Monitoring scenario / M7

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    private function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('super_admin');

        return $u;
    }

    private function guru(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('guru');

        return $u;
    }

    private function siswa(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('siswa');

        return $u;
    }

    // ─── Access control ──────────────────────────────────────────────────────

    public function test_super_admin_can_view_monitoring(): void
    {
        $admin = $this->admin();
        ActivityLog::factory()->create(['event_type' => 'login', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('admin.monitoring'));

        $response->assertOk();
        $response->assertSee('Monitoring Aktivitas');
        $response->assertSee('Login');
    }

    public function test_guru_cannot_access_monitoring(): void
    {
        $this->actingAs($this->guru())->get(route('admin.monitoring'))->assertForbidden();
    }

    public function test_siswa_cannot_access_monitoring(): void
    {
        $this->actingAs($this->siswa())->get(route('admin.monitoring'))->assertForbidden();
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('admin.monitoring'))->assertRedirect(route('auth.login.show'));
    }

    // ─── Filtering ───────────────────────────────────────────────────────────

    public function test_filter_by_event_type(): void
    {
        $admin = $this->admin();
        $loginLog = ActivityLog::factory()->create(['event_type' => 'login', 'created_at' => now()->subMinute()]);
        ActivityLog::factory()->create(['event_type' => 'quiz_attempt', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('admin.monitoring', ['event_type' => 'login']));

        $response->assertOk();
        $response->assertSee($loginLog->description ?? 'Login berhasil');
    }

    public function test_filter_by_user(): void
    {
        $admin = $this->admin();
        $guru = $this->guru();
        $log = ActivityLog::factory()->create(['user_id' => $guru->id, 'event_type' => 'logout', 'created_at' => now()]);
        ActivityLog::factory()->create(['user_id' => $admin->id, 'event_type' => 'login', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('admin.monitoring', ['user_id' => $guru->id]));

        $response->assertOk();
        $response->assertSee($log->description ?? 'Logout');
    }

    public function test_filter_by_date_range(): void
    {
        $admin = $this->admin();
        $oldLog = ActivityLog::factory()->create([
            'event_type' => 'attendance',
            'created_at' => now()->subDays(5),
        ]);
        ActivityLog::factory()->create([
            'event_type' => 'attendance',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.monitoring', [
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->subDays(3)->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee($oldLog->description ?? 'Absensi dicatat');
    }

    // ─── Pagination ──────────────────────────────────────────────────────────

    public function test_logs_are_paginated_and_preserve_query_string(): void
    {
        $admin = $this->admin();
        ActivityLog::factory()->count(30)->create(['event_type' => 'login', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get(route('admin.monitoring', ['event_type' => 'login']));

        $response->assertOk();
        $this->assertNotNull($response->viewData('logs')->links());
        $response->assertSee('event_type=login', false);
    }

    // ─── Validation ──────────────────────────────────────────────────────────

    public function test_invalid_event_type_is_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.monitoring', ['event_type' => 'foobar']))
            ->assertSessionHasErrors('event_type');
    }

    public function test_date_to_must_be_after_or_equal_date_from(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.monitoring', [
                'date_from' => now()->toDateString(),
                'date_to' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('date_to');
    }
}
