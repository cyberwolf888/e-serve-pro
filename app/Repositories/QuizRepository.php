<?php

// DATA-08 / FR-GR-09 / FR-SW-05 / M5

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class QuizRepository
{
    public function forClass(SchoolClass $class): Collection
    {
        return $class->quizzes()->withCount(['questions', 'attempts'])->latest()->get();
    }

    /** Quizzes a siswa can take right now: published, within window, not yet attempted. FR-SW-05 */
    public function availableForStudent(SchoolClass $class, User $student): Collection
    {
        return $class->quizzes()
            ->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('opens_at')->orWhere('opens_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('closes_at')->orWhere('closes_at', '>=', now()))
            ->whereDoesntHave('attempts', fn ($q) => $q->where('student_id', $student->id))
            ->get();
    }

    public function create(array $data): Quiz
    {
        return Quiz::create($data);
    }

    public function update(Quiz $quiz, array $data): Quiz
    {
        $quiz->update($data);

        return $quiz;
    }

    public function delete(Quiz $quiz): void
    {
        $quiz->delete();
    }
}
