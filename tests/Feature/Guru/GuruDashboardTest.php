<?php

// FR-GR-13

namespace Tests\Feature\Guru;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_guru_sees_dashboard_for_owned_classes_only(): void
    {
        $guru = $this->user('guru');
        $otherGuru = $this->user('guru');
        $student = $this->user('siswa');
        $otherStudent = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $otherClass = $this->schoolClass($otherGuru);

        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        ClassMember::create(['class_id' => $otherClass->id, 'student_id' => $otherStudent->id, 'joined_at' => now()]);
        Material::create(['class_id' => $class->id, 'title' => 'Materi', 'type' => 'figma', 'figma_url' => 'https://figma.com/file/test']);
        Meeting::create(['class_id' => $class->id, 'title' => 'Akan Datang', 'scheduled_at' => now()->addDay()]);
        $pastMeeting = Meeting::create(['class_id' => $class->id, 'title' => 'Belum Lengkap', 'scheduled_at' => now()->subDay()]);
        Attendance::create(['meeting_id' => $pastMeeting->id, 'student_id' => $student->id, 'status' => 'hadir', 'recorded_at' => now()]);
        Meeting::create(['class_id' => $otherClass->id, 'title' => 'Guru Lain', 'scheduled_at' => now()->addDay()]);
        Quiz::create(['class_id' => $class->id, 'title' => 'Aktif', 'is_published' => true]);
        $closedQuiz = Quiz::create(['class_id' => $class->id, 'title' => 'Selesai', 'is_published' => true, 'closes_at' => now()->subDay()]);
        QuizAttempt::create(['quiz_id' => $closedQuiz->id, 'student_id' => $student->id, 'started_at' => now()->subDays(2)]);
        Quiz::create(['class_id' => $otherClass->id, 'title' => 'Kuis Guru Lain', 'is_published' => true]);
        ActivityLog::factory()->create(['user_id' => $guru->id, 'event_type' => 'login', 'created_at' => now()->subDay()]);
        ActivityLog::factory()->create(['user_id' => $otherGuru->id, 'event_type' => 'logout', 'created_at' => now()->subDay()]);

        $response = $this->actingAs($guru)->get(route('guru.dashboard'));
        $dashboard = $response->viewData('dashboard');

        $response->assertOk()->assertSee('Dashboard Guru')->assertSee('Ringkasan kelas Anda dalam 30 hari terakhir');
        $this->assertSame([1, 1, 1, 0, 1, 1], collect($dashboard['kpis'])->pluck('value')->all());
        $this->assertCount(30, $dashboard['chart']['categories']);
        $this->assertCount(30, $dashboard['chart']['data']);
        $this->assertCount(1, $dashboard['recentActivities']);
        $this->assertSame($guru->id, $dashboard['recentActivities']->first()->user_id);
        $this->assertTrue($dashboard['alerts']->isEmpty());
    }

    public function test_non_guru_roles_cannot_view_dashboard(): void
    {
        $this->actingAs($this->user('super_admin'))->get(route('guru.dashboard'))->assertForbidden();
        $this->actingAs($this->user('siswa'))->get(route('guru.dashboard'))->assertForbidden();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('guru.dashboard'))->assertRedirect(route('auth.login.show'));
    }

    private function user(string $role): User
    {
        $user = User::factory()->create();
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
