<?php

// DATA-08..10 / FR-GR-09 / BR-05 / M5

namespace Tests\Feature\Quizzes;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizBuilderTest extends TestCase
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

    private function questionPayload(array $overrides = []): array
    {
        return array_merge([
            'question_text' => 'Ibu kota Indonesia?',
            'options' => ['Jakarta', 'Bandung'],
            'correct_option' => 0,
        ], $overrides);
    }

    public function test_guru_creates_draft_quiz_for_owned_class(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.quizzes.store', $class), [
            'title' => 'Kuis 1',
        ])->assertRedirect();

        $this->assertDatabaseHas('quizzes', ['class_id' => $class->id, 'title' => 'Kuis 1', 'is_published' => false]);
    }

    public function test_other_guru_cannot_manage_quiz(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($owner);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);

        $this->actingAs($otherGuru)->put(route('guru.classes.quizzes.update', [$class, $quiz]), [
            'title' => 'Tidak Boleh',
        ])->assertForbidden();
    }

    public function test_inactive_guru_class_blocks_quiz_writes(): void
    {
        $guru = $this->user('guru', false);
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.quizzes.store', $class), [
            'title' => 'Tidak Boleh',
        ])->assertForbidden();
    }

    public function test_guru_adds_question_with_labeled_options(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);

        $this->actingAs($guru)->post(route('guru.classes.quizzes.questions.store', [$class, $quiz]), $this->questionPayload([
            'options' => ['Jakarta', 'Bandung', 'Surabaya'],
            'correct_option' => 2,
        ]))->assertRedirect(route('guru.classes.quizzes.show', [$class, $quiz]));

        $question = QuizQuestion::where('quiz_id', $quiz->id)->firstOrFail();
        $this->assertDatabaseHas('quiz_options', ['question_id' => $question->id, 'label' => 'A', 'option_text' => 'Jakarta', 'is_correct' => false]);
        $this->assertDatabaseHas('quiz_options', ['question_id' => $question->id, 'label' => 'C', 'option_text' => 'Surabaya', 'is_correct' => true]);
        $this->assertSame(3, $question->options()->count());
    }

    public function test_question_requires_at_least_two_options(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);

        $this->actingAs($guru)->post(route('guru.classes.quizzes.questions.store', [$class, $quiz]), $this->questionPayload([
            'options' => ['Hanya Satu'],
            'correct_option' => 0,
        ]))->assertSessionHasErrors('options');

        $this->assertDatabaseCount('quiz_questions', 0);
    }

    public function test_publish_requires_at_least_one_question(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis Kosong']);

        $this->actingAs($guru)->patch(route('guru.classes.quizzes.publish', [$class, $quiz]))
            ->assertSessionHasErrors('quiz');

        $this->assertFalse($quiz->fresh()->is_published);
    }

    public function test_publish_succeeds_once_a_question_exists(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1']);
        $this->actingAs($guru)->post(route('guru.classes.quizzes.questions.store', [$class, $quiz]), $this->questionPayload());

        $this->actingAs($guru)->patch(route('guru.classes.quizzes.publish', [$class, $quiz]))
            ->assertRedirect(route('guru.classes.quizzes.show', [$class, $quiz]));

        $this->assertTrue($quiz->fresh()->is_published);
    }

    public function test_questions_are_locked_once_quiz_is_published(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1', 'is_published' => true]);
        $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question_text' => 'Q1', 'order' => 1]);
        $question->options()->createMany([
            ['option_text' => 'A', 'is_correct' => true, 'label' => 'A'],
            ['option_text' => 'B', 'is_correct' => false, 'label' => 'B'],
        ]);

        $this->actingAs($guru)->put(route('guru.classes.quizzes.questions.update', [$class, $quiz, $question]), $this->questionPayload())
            ->assertForbidden();

        $this->actingAs($guru)->delete(route('guru.classes.quizzes.questions.destroy', [$class, $quiz, $question]))
            ->assertForbidden();
    }

    public function test_questions_are_locked_forever_once_attempted_even_if_unpublished(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1', 'is_published' => false]);
        $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question_text' => 'Q1', 'order' => 1]);
        $question->options()->createMany([
            ['option_text' => 'A', 'is_correct' => true, 'label' => 'A'],
            ['option_text' => 'B', 'is_correct' => false, 'label' => 'B'],
        ]);
        $quiz->attempts()->create(['student_id' => $student->id, 'started_at' => now(), 'submitted_at' => now(), 'score' => 100]);

        $this->actingAs($guru)->delete(route('guru.classes.quizzes.questions.destroy', [$class, $quiz, $question]))
            ->assertForbidden();
    }

    public function test_quiz_delete_allowed_only_for_untouched_drafts(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);

        $draft = Quiz::create(['class_id' => $class->id, 'title' => 'Draf']);
        $this->actingAs($guru)->delete(route('guru.classes.quizzes.destroy', [$class, $draft]))
            ->assertRedirect(route('guru.classes.quizzes.index', $class));
        $this->assertModelMissing($draft);

        $published = Quiz::create(['class_id' => $class->id, 'title' => 'Terbit', 'is_published' => true]);
        $this->actingAs($guru)->delete(route('guru.classes.quizzes.destroy', [$class, $published]))
            ->assertForbidden();

        $attempted = Quiz::create(['class_id' => $class->id, 'title' => 'Sudah Dikerjakan']);
        $attempted->attempts()->create(['student_id' => $student->id, 'started_at' => now(), 'submitted_at' => now(), 'score' => 50]);
        $this->actingAs($guru)->delete(route('guru.classes.quizzes.destroy', [$class, $attempted]))
            ->assertForbidden();
    }
}
