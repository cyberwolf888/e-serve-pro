<?php

// FR-SA-07 / FR-GR-14 / FR-SW-07 / BR-05 / M7.8

namespace App\Policies;

use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class DiscussionCommentPolicy
{
    public function create(User $user, DiscussionTopic $discussion): bool
    {
        $class = $discussion->schoolClass;

        return $user->is_active
            && $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && (($user->hasRole('guru') && $class->guru_id === $user->id)
                || ($user->hasRole('siswa') && $class->members()->where('student_id', $user->id)->exists()));
    }

    public function delete(User $user, DiscussionComment $comment): bool
    {
        $class = $comment->discussionTopic->schoolClass;

        return $user->is_active
            && $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin')
                || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }
}
