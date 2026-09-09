<?php

// DATA-13 / DATA-14 / DATA-15 / DATA-27 / FR-GR-10 / FR-GR-11 / FR-GR-12 / FR-GR-15 / FR-SA-05 / FR-SW-06 / BR-03 / M6

namespace Tests\Feature\Grading;

use App\Models\ClassMember;
use App\Models\ComponentScore;
use App\Models\FinalGrade;
use App\Models\GradeComponent;
use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\LkmRole;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GradeService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $outsider = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);
        QuizAttempt::create(['quiz_id' => $quiz->id, 'student_id' => $student->id, 'score' => 80, 'started_at' => now(), 'submitted_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), ['name' => 'Kuis', 'weight' => 100, 'quiz_id' => $quiz->id]);
        $component = GradeComponent::firstOrFail();
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 80, 'is_manual_override' => false]);
        $this->actingAs($guru)->post(route('guru.classes.grade-components.scores.store', [$class, $component]), [
            'scores' => [$outsider->id => 50],
        ])->assertSessionHasErrors(['scores' => 'Mahasiswa tidak terdaftar di kelas ini.']);

        app(GradeService::class)->recordScores($component, [$student->id => 95]);
        $attempt = $quiz->attempts()->firstOrFail();
        $attempt->update(['score' => 20]);
        app(GradeService::class)->syncQuizAttempt($attempt);
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 95, 'is_manual_override' => true]);
    }

    public function test_lkm_component_validates_source_and_backfills_existing_grade(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $otherClass = $this->schoolClass($guru, 'GRADE002');
        $this->member($class, $student);
        [$lkm, $assignment] = $this->lkm($class, $guru, $student, 84);
        [$otherLkm] = $this->lkm($otherClass, $guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);

        $this->actingAs($guru)->get(route('guru.classes.grade-components.index', $class))
            ->assertOk()->assertSee($lkm->title)->assertDontSee($otherLkm->title);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), [
            'name' => 'LKM', 'weight' => 50, 'lkm_id' => $lkm->id,
        ])->assertRedirect();
        $component = GradeComponent::firstOrFail();
        $this->assertDatabaseHas('component_scores', [
            'grade_component_id' => $component->id,
            'student_id' => $assignment->student_id,
            'score' => 84,
            'is_manual_override' => false,
        ]);

        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), [
            'name' => 'Duplikat', 'weight' => 25, 'lkm_id' => $lkm->id,
        ])->assertSessionHasErrors('lkm_id');
        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), [
            'name' => 'Lintas kelas', 'weight' => 25, 'lkm_id' => $otherLkm->id,
        ])->assertSessionHasErrors('lkm_id');
        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), [
            'name' => 'Dua sumber', 'weight' => 25, 'quiz_id' => $quiz->id, 'lkm_id' => $lkm->id,
        ])->assertSessionHasErrors(['quiz_id', 'lkm_id']);
        $this->actingAs($guru)->post(route('guru.classes.grade-components.store', $class), [
            'name' => 'Sumber rusak', 'weight' => 25, 'lkm_id' => ['invalid'],
        ])->assertSessionHasErrors('lkm_id');

        $this->actingAs($guru)->put(route('guru.classes.grade-components.update', [$class, $component]), [
            'name' => 'Manual sementara', 'weight' => 60, 'lkm_id' => '',
        ])->assertRedirect();
        $this->assertNull($component->fresh()->lkm_id);
        $this->assertDatabaseMissing('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id]);

        $this->actingAs($guru)->put(route('guru.classes.grade-components.update', [$class, $component]), [
            'name' => 'LKM diperbarui', 'weight' => 60, 'lkm_id' => $lkm->id,
        ])->assertRedirect();
        $this->assertSame($lkm->id, $component->fresh()->lkm_id);
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 84]);
    }

    public function test_later_lkm_grade_sync_preserves_manual_override(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        [$lkm, $assignment] = $this->lkm($class, $guru, $student);
        $component = GradeComponent::create(['class_id' => $class->id, 'name' => 'LKM', 'weight' => 100, 'lkm_id' => $lkm->id]);
        $payload = ['proof_url' => $assignment->proof_url, 'sop_checks' => [0], 'score' => 70];

        $this->actingAs($guru)->put(route('guru.classes.lkms.submissions.update', [$class, $lkm, $assignment]), $payload)
            ->assertRedirect();
        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 70, 'is_manual_override' => false]);

        app(GradeService::class)->recordScores($component, [$student->id => 95]);
        $payload['score'] = 60;
        $this->actingAs($guru)->put(route('guru.classes.lkms.submissions.update', [$class, $lkm, $assignment]), $payload)
            ->assertRedirect();
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

    public function test_calculate_mixed_manual_quiz_and_lkm_scores_with_ungraded_lkm_as_zero(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->member($class, $student);
        $manual = GradeComponent::create(['class_id' => $class->id, 'name' => 'Manual', 'weight' => 20]);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis']);
        $quizComponent = GradeComponent::create(['class_id' => $class->id, 'name' => 'Kuis', 'weight' => 30, 'quiz_id' => $quiz->id]);
        [$lkm] = $this->lkm($class, $guru, $student, 80);
        [$ungradedLkm] = $this->lkm($class, $guru, $student);
        $lkmComponent = GradeComponent::create(['class_id' => $class->id, 'name' => 'LKM', 'weight' => 30, 'lkm_id' => $lkm->id]);
        GradeComponent::create(['class_id' => $class->id, 'name' => 'LKM kosong', 'weight' => 20, 'lkm_id' => $ungradedLkm->id]);
        ComponentScore::create(['grade_component_id' => $manual->id, 'student_id' => $student->id, 'score' => 100, 'is_manual_override' => true]);
        ComponentScore::create(['grade_component_id' => $quizComponent->id, 'student_id' => $student->id, 'score' => 60]);
        ComponentScore::create(['grade_component_id' => $lkmComponent->id, 'student_id' => $student->id, 'score' => 80]);

        $this->actingAs($guru)->post(route('guru.classes.grades.calculate', $class))->assertRedirect();
        $this->assertDatabaseHas('final_grades', ['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 62]);
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

        $this->actingAs($guru)->get(route('guru.classes.recap', $class))->assertOk()->assertSee('Mahasiswa')->assertSee($student->name);
        $this->actingAs($student)->get(route('siswa.grades.index'))->assertOk()->assertSee('Dosen')->assertSee('90.00');
        $this->actingAs($student)->get(route('guru.classes.recap', $class))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.recap.index'))->assertOk()->assertSee('Dosen')->assertSee('Mahasiswa')->assertSee($class->name);

        $response = $this->actingAs($admin)->get(route('admin.recap.export'));
        $response->assertOk()->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $path = tempnam(sys_get_temp_dir(), 'recap-');
        file_put_contents($path, $response->streamedContent());

        try {
            $spreadsheet = IOFactory::load($path);
            $this->assertSame('Mahasiswa', $spreadsheet->getActiveSheet()->getCell('B1')->getValue());
            $spreadsheet->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    private function lkm(SchoolClass $class, User $guru, ?User $student = null, ?float $score = null): array
    {
        $lkm = Lkm::create([
            'class_id' => $class->id,
            'created_by' => $guru->id,
            'title' => 'LKM '.Lkm::count(),
            'description' => 'Deskripsi',
        ]);
        $role = LkmRole::create([
            'lkm_id' => $lkm->id,
            'name' => 'Ketua',
            'description' => 'Memimpin.',
            'instructions' => 'Kerjakan.',
            'sop_items' => ['Selesai'],
        ]);
        $assignment = $student ? LkmAssignment::create([
            'lkm_id' => $lkm->id,
            'lkm_role_id' => $role->id,
            'student_id' => $student->id,
            'proof_url' => 'https://youtu.be/proof',
            'proof_submitted_at' => now(),
            'sop_checks' => [0],
            'reflection_submitted_at' => now(),
            'score' => $score,
        ]) : null;

        return [$lkm, $assignment];
    }
}
