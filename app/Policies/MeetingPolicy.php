<?php

// §3.2 / FR-GR-06 / FR-GR-08 / FR-SW-04 / BR-05 / M4

namespace App\Policies;

use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class MeetingPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('guru') && $class->guru_id === $user->id)
            || ($user->hasRole('siswa') && $class->members()->where('student_id', $user->id)->exists());
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return $this->viewAny($user, $meeting->schoolClass);
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $this->writable($user, $meeting->schoolClass);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $this->writable($user, $meeting->schoolClass);
    }

    /** Share materials to this meeting — same write rule. FR-GR-08 */
    public function share(User $user, Meeting $meeting): bool
    {
        return $this->writable($user, $meeting->schoolClass);
    }

    /** Record/edit attendance for this meeting — same write rule. FR-GR-07 */
    public function recordAttendance(User $user, Meeting $meeting): bool
    {
        return $this->writable($user, $meeting->schoolClass);
    }

    /** super_admin all / guru OWN active class of an active guru. §3.2 / BR-05 */
    private function writable(User $user, SchoolClass $class): bool
    {
        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }
}
