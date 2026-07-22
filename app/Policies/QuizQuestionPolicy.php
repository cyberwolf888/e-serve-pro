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
     * Super Admin bypasses class-active/owner-active guards but NOT integrity locks.
     */
    private function writable(User $user, Quiz $quiz): bool
    {
        $class = $quiz->schoolClass;
        $isOwner = $user->hasRole('guru') && $class->guru_id === $user->id;

        if (! $user->hasRole('super_admin') && ! $isOwner) {
            return false;
        }

        $writable = ! $quiz->is_published
            && ! $quiz->attempts()->exists();

        if ($user->hasRole('super_admin')) {
            return $writable;
        }

        return $writable
            && $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru);
    }
}
