<?php

// DATA-11 / DATA-12 / FR-SW-05 / M5

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;

class QuizAttemptRepository
{
    public function create(array $data): QuizAttempt
    {
        return QuizAttempt::create($data);
    }

    public function createAnswer(array $data): QuizAnswer
    {
        return QuizAnswer::create($data);
    }

    public function updateScore(QuizAttempt $attempt, float $score): QuizAttempt
    {
        $attempt->update(['score' => $score]);

        return $attempt;
    }

    public function existsForStudent(Quiz $quiz, User $student): bool
    {
        return $quiz->attempts()->where('student_id', $student->id)->exists();
    }
}
