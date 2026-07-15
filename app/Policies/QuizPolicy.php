<?php

// §3.2 / FR-GR-09 / FR-SW-05 / BR-05 / M5

namespace App\Policies;

use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class QuizPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id);
    }

    public function view(User $user, Quiz $quiz): bool
    {
        return $this->viewAny($user, $quiz->schoolClass);
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    /** Metadata (title/description/opens_at/closes_at) stays editable even after attempts exist. */
    public function update(User $user, Quiz $quiz): bool
    {
        return $this->writable($user, $quiz->schoolClass);
    }

    /** Draft-only deletion — never if published or ever attempted. */
    public function delete(User $user, Quiz $quiz): bool
    {
        return $this->writable($user, $quiz->schoolClass)
            && ! $quiz->is_published
            && ! $quiz->attempts()->exists();
    }

    public function publish(User $user, Quiz $quiz): bool
    {
        return $this->writable($user, $quiz->schoolClass) && ! $quiz->is_published;
    }

    public function unpublish(User $user, Quiz $quiz): bool
    {
        return $this->writable($user, $quiz->schoolClass) && $quiz->is_published;
    }

    /** Siswa may take this quiz right now. FR-SW-05 / BR-05 — frozen if class/owner inactive. */
    public function take(User $user, Quiz $quiz): bool
    {
        return $user->hasRole('siswa')
            && $user->is_active
            && $quiz->is_published
            && $quiz->schoolClass->is_active
            && ReadOnlyGuard::isOwnerActive($quiz->schoolClass->guru)
            && $quiz->schoolClass->members()->where('student_id', $user->id)->exists()
            && $quiz->isWithinWindow()
            && ! $quiz->attempts()->where('student_id', $user->id)->exists();
    }

    /** super_admin all / guru OWN active class of an active guru. §3.2 / BR-05 */
    private function writable(User $user, SchoolClass $class): bool
    {
        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }
}
