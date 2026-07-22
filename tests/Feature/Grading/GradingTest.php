<?php

// DATA-13 / DATA-14 / DATA-15 / FR-GR-10 / FR-GR-11 / FR-GR-12 / FR-SA-05 / FR-SW-06 / BR-03 / M6

namespace Tests\Feature\Grading;

use App\Models\ClassMember;
use App\Models\ComponentScore;
use App\Models\FinalGrade;
use App\Models\GradeComponent;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GradeService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    private function user(string $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru, string $code = 'GRADE001'): SchoolClass
    {
        return SchoolClass::create(['guru_id' => $guru->id, 'name' => 'Kelas Nilai', 'class_code' => $code, 'is_active' => true]);
    }

    private function member(SchoolClass $class, User $student): void
    {
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
    }

    public function test_guru_creates_component_warned_when_weights_do_not_total_100(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), ['name' => 'Tugas', 'weight' => 40])
            ->assertRedirect();
        $this->actingAs($guru)->get(route('guru.classes.grade-components.index', $class))
            ->assertOk()->assertSee('Total bobot 40%');

        $this->assertDatabaseHas('grade_components', ['class_id' => $class->id, 'name' => 'Tugas', 'weight' => 40]);
    }

    public function test_invalid_weight_and_other_guru_are_rejected(): void
    {
        $guru = $this->user('guru');
        $other = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), ['name' => 'Tugas', 'weight' => 101])
            ->assertSessionHasErrors('weight');
        $this->actingAs($other)->get(route('guru.classes.grade-components.index', $class))->assertForbidden();
    }

    public function test_guru_can_delete_component_and_other_guru_cannot(): void
    {
        $guru = $this->user('guru');
        $other = $this->user('guru');
        $class = $this->schoolClass($guru);
        $component = GradeComponent::create(['class_id' => $class->id, 'name' => 'Tugas', 'weight' => 25]);

        $this->actingAs($other)->delete(route('guru.classes.grade-components.destroy', [$class, $component]))->assertForbidden();
        $this->actingAs($guru)->delete(route('guru.classes.grade-components.destroy', [$class, $component]))->assertRedirect();
        $this->assertModelMissing($component);
    }

    public function test_quiz_component_backfills_attempt_and_keeps_manual_override(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);
        QuizAttempt::create(['quiz_id' => $quiz->id, 'student_id' => $student->id, 'score' => 80, 'started_at' => now(), 'submitted_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), ['name' => 'Kuis', 'weight' => 100, 'quiz_id' => $quiz->id]);
        $component = GradeComponent::firstOrFail();
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 80, 'is_manual_override' => false]);

        app(GradeService::class)->recordScores($component, [$student->id => 95]);
        $attempt = $quiz->attempts()->firstOrFail();
        $attempt->update(['score' => 20]);
        app(GradeService::class)->syncQuizAttempt($attempt);
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 95, 'is_manual_override' => true]);
    }

    public function test_calculate_normalizes_weights_and_missing_score_is_zero(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        $one = GradeComponent::create(['class_id' => $class->id, 'name' => 'A', 'weight' => 20]);
        GradeComponent::create(['class_id' => $class->id, 'name' => 'B', 'weight' => 20]);
        ComponentScore::create(['grade_component_id' => $one->id, 'student_id' => $student->id, 'score' => 80]);

        $this->actingAs($guru)->post(route('guru.classes.grades.calculate', $class))->assertRedirect();
        $this->assertDatabaseHas('final_grades', ['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 40]);
    }

    public function test_zero_total_weight_rejected_and_inactive_student_is_not_recalculated(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa', false);
        $class = $this->schoolClass($guru);
        $this->member($class, $student);

        $this->actingAs($guru)->post(route('guru.classes.grades.calculate', $class))->assertSessionHasErrors('grades');
        GradeComponent::create(['class_id' => $class->id, 'name' => 'A', 'weight' => 100]);
        FinalGrade::create(['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 77, 'calculated_at' => now()->subDay()]);
        $this->actingAs($guru)->post(route('guru.classes.grades.calculate', $class));
        $this->assertSame('77.00', (string) FinalGrade::firstOrFail()->final_score);
    }

    public function test_recap_roles_and_xlsx_export_are_scoped(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $admin = $this->user('super_admin');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        FinalGrade::create(['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 90, 'calculated_at' => now()]);

        $this->actingAs($guru)->get(route('guru.classes.recap', $class))->assertOk()->assertSee($student->name);
        $this->actingAs($student)->get(route('siswa.grades.index'))->assertOk()->assertSee('90.00');
        $this->actingAs($student)->get(route('guru.classes.recap', $class))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.recap.index'))->assertOk()->assertSee($class->name);
        $this->actingAs($admin)->get(route('admin.recap.export'))->assertOk()->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
