<?php

// FR-SA-06

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_super_admin_sees_operational_dashboard(): void
    {
        $admin = $this->user('super_admin');
        $this->user('guru', false);
        $guru = $this->user('guru');
        $this->user('siswa', false);
        $siswa = $this->user('siswa');
        $withEverything = $this->schoolClass($guru);
        ClassMember::create(['class_id' => $withEverything->id, 'student_id' => $siswa->id, 'joined_at' => now()]);
        Material::create(['class_id' => $withEverything->id, 'title' => 'Materi', 'type' => 'figma', 'figma_url' => 'https://figma.com/file/test']);
        Quiz::create(['class_id' => $withEverything->id, 'title' => 'Kuis', 'is_published' => true]);
        $this->schoolClass($guru);
        ActivityLog::factory()->create(['user_id' => $siswa->id, 'created_at' => now()->subDays(30)]);
        ActivityLog::factory()->create(['user_id' => $siswa->id, 'event_type' => 'login', 'created_at' => now()->subDays(2)]);
        ActivityLog::factory()->create(['user_id' => $siswa->id, 'event_type' => 'logout', 'created_at' => now()->subDays(2)]);
        ActivityLog::factory()->create(['user_id' => null, 'event_type' => 'other', 'created_at' => now()]);
        ActivityLog::factory()->count(10)->create(['user_id' => $siswa->id, 'created_at' => now()->subMinute()]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboard = $response->viewData('dashboard');

        $response->assertOk()->assertSee('Dashboard Super Admin')->assertSee('Ringkasan operasional 30 hari terakhir');
        $this->assertSame([2, 2, 2, 1, 1, 13], collect($dashboard['kpis'])->pluck('value')->all());
        $this->assertCount(30, $dashboard['chart']['categories']);
        $this->assertCount(30, $dashboard['chart']['data']);
        $this->assertSame(0, $dashboard['chart']['data'][0]);
        $this->assertCount(10, $dashboard['recentActivities']);
        $this->assertSame([
            'Guru nonaktif',
            'Siswa nonaktif',
            'Kelas aktif tanpa siswa',
            'Kelas aktif tanpa materi',
            'Kelas aktif tanpa kuis terbit',
        ], $dashboard['alerts']->pluck('label')->all());
    }

    public function test_non_admin_roles_cannot_view_dashboard(): void
    {
        $this->actingAs($this->user('guru'))->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->user('siswa'))->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('auth.login.show'));
    }

    private function user(string $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru): SchoolClass
    {
        return SchoolClass::create([
            'guru_id' => $guru->id,
            'name' => 'Kelas',
            'class_code' => fake()->unique()->bothify('CLASS###'),
            'is_active' => true,
        ]);
    }
}
