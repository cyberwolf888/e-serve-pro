<?php

// FR-GR-09 / DATA-08 / M5

namespace App\Services;

use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Repositories\QuizRepository;
use Illuminate\Validation\ValidationException;

class QuizService
{
    public function __construct(private QuizRepository $repo) {}

    public function create(SchoolClass $class, array $data): Quiz
    {
        return $this->repo->create($data + ['class_id' => $class->id, 'is_published' => false]);
    }

    public function update(Quiz $quiz, array $data): Quiz
    {
        return $this->repo->update($quiz, $data);
    }

    public function delete(Quiz $quiz): void
    {
        $this->repo->delete($quiz);
    }

    /**
     * Publish a quiz. Every stored question already has 2-26 options with exactly one
     * correct answer (enforced at question save time), so the only remaining check is
     * that at least one question exists.
     */
    public function publish(Quiz $quiz): void
    {
        if (! $quiz->questions()->exists()) {
            throw ValidationException::withMessages([
                'quiz' => 'Kuis harus memiliki minimal satu soal sebelum dipublikasikan.',
            ]);
        }

        $this->repo->update($quiz, ['is_published' => true]);
    }

    public function unpublish(Quiz $quiz): void
    {
        $this->repo->update($quiz, ['is_published' => false]);
    }
}
