<?php

// DATA-11 / DATA-12 / FR-SW-05 / BR-05 / BR-06 / M5

namespace Tests\Feature\Quizzes;

use App\Models\ClassMember;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\QuizAttemptService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuizTakingTest extends TestCase
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

    /** Quiz with 2 questions, first correct option is always index 0. */
    private function quizWithTwoQuestions(SchoolClass $class): Quiz
    {
        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1', 'is_published' => true]);

        foreach ([1, 2] as $order) {
            $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question_text' => "Soal {$order}", 'order' => $order]);
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => true, 'label' => 'A'],
                ['option_text' => 'Salah', 'is_correct' => false, 'label' => 'B'],
            ]);
        }

        return $quiz->fresh();
    }

    private function join(SchoolClass $class, User $student): void
    {
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
    }

    public function test_available_quiz_appears_on_joined_class_page(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $quiz = $this->quizWithTwoQuestions($class);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertSee($quiz->title);
    }

    public function test_unavailable_quiz_returns_404(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);

        // unpublished
        $draft = Quiz::create(['class_id' => $class->id, 'title' => 'Draf']);
        $this->actingAs($student)->get(route('siswa.quizzes.show', $draft))->assertNotFound();

        // not a member of this class
        $otherClass = $this->schoolClass($guru, ['class_code' => 'LAIN001']);
        $foreignQuiz = $this->quizWithTwoQuestions($otherClass);
        $this->actingAs($student)->get(route('siswa.quizzes.show', $foreignQuiz))->assertNotFound();

        // not yet open
        $notYetOpen = $this->quizWithTwoQuestions($class);
        $notYetOpen->update(['opens_at' => now()->addDay()]);
        $this->actingAs($student)->get(route('siswa.quizzes.show', $notYetOpen))->assertNotFound();

        // already closed
        $closed = $this->quizWithTwoQuestions($class);
        $closed->update(['closes_at' => now()->subDay()]);
        $this->actingAs($student)->get(route('siswa.quizzes.show', $closed))->assertNotFound();
    }

    public function test_siswa_submits_and_gets_auto_scored_with_activity_log(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $quiz = $this->quizWithTwoQuestions($class);
        $questions = $quiz->questions;

        $answers = [];
        foreach ($questions as $question) {
            $answers[$question->id] = $question->options->firstWhere('is_correct', true)->id;
        }

        $this->actingAs($student)->post(route('siswa.quizzes.submit', $quiz), ['answers' => $answers])
            ->assertRedirect(route('siswa.classes.show', $class));

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->firstOrFail();
        $this->assertSame('100.00', (string) $attempt->score);
        $this->assertNotNull($attempt->submitted_at);
        $this->assertSame($attempt->started_at->timestamp, $attempt->submitted_at->timestamp);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $student->id,
            'event_type' => 'quiz_attempt',
            'subject_type' => QuizAttempt::class,
            'subject_id' => $attempt->id,
        ]);
    }

    public function test_partial_score_rounds_half_up(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);

        $quiz = Quiz::create(['class_id' => $class->id, 'title' => 'Kuis 1', 'is_published' => true]);
        $questions = [];
        foreach (range(1, 3) as $order) {
            $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question_text' => "Soal {$order}", 'order' => $order]);
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => true, 'label' => 'A'],
                ['option_text' => 'Salah', 'is_correct' => false, 'label' => 'B'],
            ]);
            $questions[] = $question->fresh('options');
        }

        // 1 of 3 correct = 33.333... -> rounds to 33.33
        $answers = [
            $questions[0]->id => $questions[0]->options->firstWhere('is_correct', true)->id,
            $questions[1]->id => $questions[1]->options->firstWhere('is_correct', false)->id,
            $questions[2]->id => $questions[2]->options->firstWhere('is_correct', false)->id,
        ];

        $this->actingAs($student)->post(route('siswa.quizzes.submit', $quiz), ['answers' => $answers]);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->firstOrFail();
        $this->assertSame('33.33', (string) $attempt->score);
    }

    public function test_incomplete_answers_are_rejected(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $quiz = $this->quizWithTwoQuestions($class);
        $first = $quiz->questions->first();

        $this->actingAs($student)->post(route('siswa.quizzes.submit', $quiz), [
            'answers' => [$first->id => $first->options->first()->id],
        ])->assertSessionHasErrors('answers');

        $this->assertDatabaseCount('quiz_attempts', 0);
    }

    public function test_inactive_class_blocks_taking_quiz(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['is_active' => false]);
        $this->join($class, $student);
        $quiz = $this->quizWithTwoQuestions($class);

        $this->actingAs($student)->get(route('siswa.quizzes.show', $quiz))->assertNotFound();
    }

    public function test_duplicate_submission_is_rejected_at_the_database_level(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $quiz = $this->quizWithTwoQuestions($class);

        $answers = [];
        foreach ($quiz->questions as $question) {
            $answers[$question->id] = $question->options->first()->id;
        }

        $service = app(QuizAttemptService::class);
        $service->submit($quiz, $student, $answers, Request::create('/'));

        $this->expectException(ValidationException::class);
        $service->submit($quiz, $student, $answers, Request::create('/'));
    }
}
