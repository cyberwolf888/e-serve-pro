<?php

// §3.2 / FR-GR-09 / BR-05 / M5

namespace App\Policies;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class QuizQuestionPolicy
{
    public function create(User $user, Quiz $quiz): bool
    {
        return $this->writable($user, $quiz);
    }

    public function update(User $user, QuizQuestion $question): bool
    {
        return $this->writable($user, $question->quiz);
    }

    public function delete(User $user, QuizQuestion $question): bool
    {
        return $this->writable($user, $question->quiz);
    }

    /**
     * Quiz must be unpublished and never attempted to edit its questions/options.
     * Content lock: once any attempt is submitted, questions/options are immutable forever.
     */
    private function writable(User $user, Quiz $quiz): bool
    {
        return ! $quiz->is_published
            && ! $quiz->attempts()->exists()
            && $quiz->schoolClass->is_active
            && ReadOnlyGuard::isOwnerActive($quiz->schoolClass->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $quiz->schoolClass->guru_id === $user->id));
    }
}
