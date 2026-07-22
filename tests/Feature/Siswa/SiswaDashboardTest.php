<?php

// FR-SW-06

namespace Tests\Feature\Siswa;

use App\Models\ActivityLog;
use App\Models\ClassMember;
use App\Models\FinalGrade;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_siswa_sees_learning_dashboard_for_enrolled_classes_only(): void
    {
        $student = $this->user('siswa');
        $otherStudent = $this->user('siswa');
        $guru = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $otherClass = $this->schoolClass($otherGuru);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        ClassMember::create(['class_id' => $otherClass->id, 'student_id' => $otherStudent->id, 'joined_at' => now()]);
        Meeting::create(['class_id' => $class->id, 'title' => 'Akan Datang', 'scheduled_at' => now()->addDay()]);
        Meeting::create(['class_id' => $otherClass->id, 'title' => 'Kelas Lain', 'scheduled_at' => now()->addDay()]);
        $unattemptedQuiz = Quiz::create(['class_id' => $class->id, 'title' => 'Belum Dikerjakan', 'is_published' => true]);
        $attemptedQuiz = Quiz::create(['class_id' => $class->id, 'title' => 'Sudah Dikerjakan', 'is_published' => true]);
        QuizAttempt::create(['quiz_id' => $attemptedQuiz->id, 'student_id' => $student->id, 'started_at' => now()]);
        Quiz::create(['class_id' => $otherClass->id, 'title' => 'Kuis Kelas Lain', 'is_published' => true]);
        FinalGrade::create(['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 90, 'calculated_at' => now()]);
        FinalGrade::create(['class_id' => $otherClass->id, 'student_id' => $otherStudent->id, 'final_score' => 80, 'calculated_at' => now()]);
        ActivityLog::factory()->create(['user_id' => $student->id, 'event_type' => 'quiz_attempt', 'created_at' => now()->subDay()]);
        ActivityLog::factory()->create(['user_id' => $otherStudent->id, 'event_type' => 'login', 'created_at' => now()->subDay()]);

        $response = $this->actingAs($student)->get(route('siswa.dashboard'));
        $dashboard = $response->viewData('dashboard');

        $response->assertOk()
            ->assertSee('Dashboard Siswa')
            ->assertSee('Ringkasan pembelajaran Anda dalam 30 hari terakhir');
        $this->assertSame([1, 1, 2, 1], collect($dashboard['kpis'])->pluck('value')->all());
        $this->assertCount(30, $dashboard['chart']['categories']);
        $this->assertCount(30, $dashboard['chart']['data']);
        $this->assertSame(1, array_sum($dashboard['chart']['data']));
        $this->assertSame(['Kuis belum dikerjakan'], $dashboard['alerts']->pluck('label')->all());
        $this->assertSame(1, $dashboard['alerts']->first()['count']);
        $this->assertCount(1, $dashboard['recentActivities']);
        $this->assertSame($student->id, $dashboard['recentActivities']->first()->user_id);
    }

    public function test_siswa_without_classes_sees_empty_learning_alert(): void
    {
        $student = $this->user('siswa');

        $response = $this->actingAs($student)->get(route('siswa.dashboard'));
        $dashboard = $response->viewData('dashboard');

        $response->assertOk()->assertSee('Belum bergabung ke kelas')->assertSee('Tidak ada log aktivitas.');
        $this->assertSame([0, 0, 0, 0], collect($dashboard['kpis'])->pluck('value')->all());
        $this->assertSame(['Belum bergabung ke kelas'], $dashboard['alerts']->pluck('label')->all());
    }

    public function test_non_siswa_roles_cannot_view_dashboard(): void
    {
        $this->actingAs($this->user('guru'))->get(route('siswa.dashboard'))->assertForbidden();
        $this->actingAs($this->user('super_admin'))->get(route('siswa.dashboard'))->assertForbidden();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('siswa.dashboard'))->assertRedirect(route('auth.login.show'));
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
