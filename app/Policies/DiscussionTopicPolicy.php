<?php

// FR-SA-07 / FR-GR-14 / FR-SW-07 / BR-05 / M7.8

namespace App\Policies;

use App\Models\DiscussionTopic;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class DiscussionTopicPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('guru') && $class->guru_id === $user->id)
            || ($user->hasRole('siswa') && $class->members()->where('student_id', $user->id)->exists());
    }

    public function view(User $user, DiscussionTopic $discussion): bool
    {
        return $this->viewAny($user, $discussion->schoolClass);
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $user->is_active
            && $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && $user->hasRole('guru')
            && $class->guru_id === $user->id;
    }
}
