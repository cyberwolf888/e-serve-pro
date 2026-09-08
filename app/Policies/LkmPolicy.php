<?php

// FR-SA-08 / FR-GR-15 / FR-SW-08 / BR-05 / BR-09 / M7.9

namespace App\Policies;

use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class LkmPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('guru') && $class->guru_id === $user->id);
    }

    public function view(User $user, Lkm $lkm): bool
    {
        if ($this->viewAny($user, $lkm->schoolClass)) {
            return true;
        }

        return $user->hasRole('siswa')
            && $lkm->is_published
            && $lkm->schoolClass->members()->where('student_id', $user->id)->exists()
            && $lkm->assignments()->where('student_id', $user->id)->exists();
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    public function update(User $user, Lkm $lkm): bool
    {
        return $this->writable($user, $lkm->schoolClass);
    }

    public function manageRoles(User $user, Lkm $lkm): bool
    {
        return $this->update($user, $lkm) && ! $lkm->assignments()->whereNotNull('proof_submitted_at')->exists();
    }

    public function assign(User $user, Lkm $lkm): bool
    {
        return $this->update($user, $lkm);
    }

    public function reassign(User $user, LkmAssignment $assignment): bool
    {
        return $this->update($user, $assignment->lkm)
            && $assignment->student->is_active
            && $assignment->proof_submitted_at === null;
    }

    public function correctSubmission(User $user, LkmAssignment $assignment): bool
    {
        return $this->update($user, $assignment->lkm)
            && $assignment->student->is_active
            && $assignment->proof_submitted_at !== null;
    }

    public function submitProof(User $user, Lkm $lkm): bool
    {
        return $this->studentCanWrite($user, $lkm)
            && $lkm->assignments()->where('student_id', $user->id)->exists();
    }

    public function submitReflection(User $user, Lkm $lkm): bool
    {
        return $this->studentCanWrite($user, $lkm)
            && $lkm->assignments()->where('student_id', $user->id)->exists();
    }

    private function writable(User $user, SchoolClass $class): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && $user->hasRole('guru')
            && $class->guru_id === $user->id;
    }

    private function studentCanWrite(User $user, Lkm $lkm): bool
    {
        return $user->hasRole('siswa')
            && $user->is_active
            && $lkm->is_published
            && $lkm->schoolClass->is_active
            && ReadOnlyGuard::isOwnerActive($lkm->schoolClass->guru)
            && $lkm->schoolClass->members()->where('student_id', $user->id)->exists();
    }
}
