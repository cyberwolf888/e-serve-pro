<?php

// FR-SW-05 / BR-06 / DATA-11 / DATA-12 / M5
// ASSUMPTION: score = round((correct / total) * 100, 2, half up) per §13 scoring assumption.

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\QuizAttemptRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizAttemptService
{
    public function __construct(
        private QuizAttemptRepository $repo,
        private AuthService $auth,
        private GradeService $grades,
    ) {}

    /**
     * Submit a full set of answers and auto-score atomically.
     * $answers: [question_id => selected_option_id]. Single attempt is enforced by the
     * unique(quiz_id, student_id) DB constraint — a race is caught and rejected below.
     */
    public function submit(Quiz $quiz, User $student, array $answers, Request $request): QuizAttempt
    {
        return DB::transaction(function () use ($quiz, $student, $answers, $request) {
            $now = now();

            try {
                $attempt = $this->repo->create([
                    'quiz_id' => $quiz->id,
                    'student_id' => $student->id,
                    'started_at' => $now,
                    'submitted_at' => $now,
                ]);
            } catch (QueryException $e) {
                throw ValidationException::withMessages(['quiz' => 'Anda sudah mengerjakan kuis ini.']);
            }

            $questions = $quiz->questions()->with('options')->get();
            $correctCount = 0;

            foreach ($questions as $question) {
                $selectedOptionId = $answers[$question->id];
                $isCorrect = (bool) $question->options->firstWhere('id', $selectedOptionId)?->is_correct;
                $correctCount += $isCorrect ? 1 : 0;

                $this->repo->createAnswer([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect,
                ]);
            }

            $score = round($correctCount / $questions->count() * 100, 2, PHP_ROUND_HALF_UP);
            $this->repo->updateScore($attempt, $score);
            $this->grades->syncQuizAttempt($attempt->fresh('student'));

            $this->auth->logActivity($student, 'quiz_attempt', $request, "Percobaan kuis: {$quiz->title} (skor {$score})", $attempt);

            return $attempt;
        });
    }
}
