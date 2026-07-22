<?php

// ADMIN_CLASS_ACCESS_PLAN / FR-SA-03 / FR-GR-02 / FR-GR-03 / FR-GR-04 / FR-GR-05 / FR-GR-06 / FR-GR-07 / FR-GR-08 / FR-GR-09 / FR-GR-10 / FR-GR-11 / FR-GR-12 / BR-04 / BR-05

namespace Tests\Feature\Admin;

use App\Models\ClassMember;
use App\Models\GradeComponent;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassAccessTest extends TestCase
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

    private function schoolClass(User $guru, array $data = []): SchoolClass
    {
        return SchoolClass::create($data + [
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => 'KELAS001',
            'is_active' => true,
        ]);
    }

    public function test_admin_views_another_gurus_class_detail_and_tabs_use_admin_routes(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru, ['class_code' => 'ACCES01']);

        $this->actingAs($admin)->get(route('admin.classes.show', $class))
            ->assertOk()
            ->assertSee($class->name)
            ->assertSee($guru->name)
            ->assertSee(route('admin.classes.materials.index', $class), false)
            ->assertSee(route('admin.classes.meetings.index', $class), false)
            ->assertSee(route('admin.classes.quizzes.index', $class), false)
            ->assertSee(route('admin.classes.grade-components.index', $class), false);
    }

    public function test_admin_manages_materials_of_another_gurus_active_and_inactive_class(): void
    {
        Storage::fake('local');
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $activeClass = $this->schoolClass($guru, ['class_code' => 'ACTMAT']);
        $inactiveClass = $this->schoolClass($guru, ['class_code' => 'INAMAT', 'is_active' => false]);

        $this->actingAs($admin)->post(route('admin.classes.materials.store', $activeClass), [
            'title' => 'Tautan Admin',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/admin',
        ])->assertRedirect(route('admin.classes.materials.index', $activeClass));

        $this->assertDatabaseHas('materials', ['class_id' => $activeClass->id, 'title' => 'Tautan Admin']);

        $file = UploadedFile::fake()->create('admin-materi.pdf', 512, 'application/pdf');
        $this->actingAs($admin)->post(route('admin.classes.materials.store', $inactiveClass), [
            'title' => 'PDF Admin',
            'type' => 'file',
            'figma_url' => '',
            'file' => $file,
        ])->assertRedirect(route('admin.classes.materials.index', $inactiveClass));

        $this->assertDatabaseHas('materials', ['class_id' => $inactiveClass->id, 'title' => 'PDF Admin']);
    }

    public function test_other_guru_is_blocked_from_class_features(): void
    {
        $owner = $this->user('guru');
        $other = $this->user('guru');
        $class = $this->schoolClass($owner);
        $material = Material::create(['class_id' => $class->id, 'title' => 'M', 'type' => 'figma', 'figma_url' => 'https://f']);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Q1']);

        $this->actingAs($other)
            ->post(route('guru.classes.materials.store', $class), ['title' => 'X', 'type' => 'figma', 'figma_url' => 'https://f'])
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('guru.classes.meetings.store', $class), ['title' => 'P', 'scheduled_at' => now()->format('Y-m-d\TH:i')])
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('guru.classes.quizzes.store', $class), ['title' => 'Q'])
            ->assertForbidden();

        $this->actingAs($other)->delete(route('guru.classes.materials.destroy', [$class, $material]))->assertForbidden();
        $this->actingAs($other)->delete(route('guru.classes.meetings.destroy', [$class, $meeting]))->assertForbidden();
        $this->actingAs($other)->delete(route('guru.classes.quizzes.destroy', [$class, $quiz]))->assertForbidden();
    }

    public function test_admin_manages_meetings_and_attendance_for_another_guru(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);

        $this->actingAs($admin)->post(route('admin.classes.meetings.store', $class), [
            'title' => 'Pertemuan Admin',
            'scheduled_at' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('admin.classes.meetings.index', $class));

        $meeting = Meeting::where('title', 'Pertemuan Admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'hadir'],
        ])->assertRedirect(route('admin.classes.meetings.attendance.edit', [$class, $meeting]));

        $this->assertDatabaseHas('attendances', ['meeting_id' => $meeting->id, 'student_id' => $student->id, 'status' => 'hadir']);
    }

    public function test_admin_manages_quizzes_and_questions_for_another_guru(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($admin)->post(route('admin.classes.quizzes.store', $class), ['title' => 'Kuis Admin'])
            ->assertRedirect();

        $quiz = Quiz::where('title', 'Kuis Admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.classes.quizzes.questions.store', [$class, $quiz]), [
            'question_text' => 'Soal Admin',
            'options' => ['A', 'B'],
            'correct_option' => 0,
        ])->assertRedirect(route('admin.classes.quizzes.show', [$class, $quiz]));

        $this->assertDatabaseHas('quiz_questions', ['quiz_id' => $quiz->id, 'question_text' => 'Soal Admin']);

        $this->actingAs($admin)->patch(route('admin.classes.quizzes.publish', [$class, $quiz]))
            ->assertRedirect(route('admin.classes.quizzes.show', [$class, $quiz]));

        $this->assertTrue($quiz->fresh()->is_published);
    }

    public function test_quiz_integrity_locks_still_block_admin_question_edits(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $publishedQuiz = Quiz::create(['class_id' => $class->id, 'title' => 'Terbit', 'is_published' => true]);
        $question = QuizQuestion::create(['quiz_id' => $publishedQuiz->id, 'question_text' => 'Q1', 'order' => 1]);
        $question->options()->createMany([
            ['option_text' => 'A', 'is_correct' => true, 'label' => 'A'],
            ['option_text' => 'B', 'is_correct' => false, 'label' => 'B'],
        ]);

        $attemptedQuiz = Quiz::create(['class_id' => $class->id, 'title' => 'Dikerjakan']);
        $attemptQuestion = QuizQuestion::create(['quiz_id' => $attemptedQuiz->id, 'question_text' => 'Q2', 'order' => 1]);
        $attemptQuestion->options()->createMany([
            ['option_text' => 'A', 'is_correct' => true, 'label' => 'A'],
            ['option_text' => 'B', 'is_correct' => false, 'label' => 'B'],
        ]);
        QuizAttempt::create(['quiz_id' => $attemptedQuiz->id, 'student_id' => $student->id, 'started_at' => now(), 'submitted_at' => now(), 'score' => 100]);

        $this->actingAs($admin)->put(route('admin.classes.quizzes.questions.update', [$class, $publishedQuiz, $question]), [
            'question_text' => 'Ubah',
            'options' => ['A', 'B'],
            'correct_option' => 0,
        ])->assertForbidden();

        $this->actingAs($admin)->delete(route('admin.classes.quizzes.questions.destroy', [$class, $attemptedQuiz, $attemptQuestion]))
            ->assertForbidden();

        $this->actingAs($admin)->delete(route('admin.classes.quizzes.destroy', [$class, $publishedQuiz]))
            ->assertForbidden();
    }

    public function test_admin_adds_student_and_manages_grades_for_another_gurus_inactive_class(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['class_code' => 'INAGRAD', 'is_active' => false]);

        $this->actingAs($admin)->post(route('admin.classes.students.store', $class), ['email' => $student->email])
            ->assertRedirect(route('admin.classes.show', $class));

        $this->assertDatabaseHas('class_members', ['class_id' => $class->id, 'student_id' => $student->id]);

        $this->actingAs($admin)->post(route('admin.classes.grade-components.store', $class), [
            'name' => 'Tugas',
            'weight' => 100,
        ])->assertRedirect(route('admin.classes.grade-components.index', $class));

        $component = GradeComponent::firstOrFail();
        $this->actingAs($admin)->post(route('admin.classes.grade-components.scores.store', [$class, $component]), [
            'scores' => [$student->id => 85],
        ])->assertRedirect(route('admin.classes.grade-components.scores', [$class, $component]));

        $this->assertDatabaseHas('component_scores', ['grade_component_id' => $component->id, 'student_id' => $student->id, 'score' => 85]);

        $this->actingAs($admin)->post(route('admin.classes.grades.calculate', $class))
            ->assertRedirect(route('admin.classes.recap', $class));

        $this->assertDatabaseHas('final_grades', ['class_id' => $class->id, 'student_id' => $student->id, 'final_score' => 85]);
    }
}
