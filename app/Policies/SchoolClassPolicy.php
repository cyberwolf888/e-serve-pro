<?php

// §3.2 / FR-GR-02 / FR-GR-03 / FR-SW-03 / BR-05 / M3

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class SchoolClassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'guru', 'siswa']);
    }

    public function view(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('guru') && $class->guru_id === $user->id)
            || ($user->hasRole('siswa') && $class->members()->where('student_id', $user->id)->exists());
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->hasAnyRole(['super_admin', 'guru']);
    }

    public function update(User $user, SchoolClass $class): bool
    {
        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }

    public function deactivate(User $user, SchoolClass $class): bool
    {
        return $this->update($user, $class);
    }

    public function activate(User $user, SchoolClass $class): bool
    {
        return ! $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }

    public function addStudent(User $user, SchoolClass $class): bool
    {
        return $this->update($user, $class);
    }

    public function join(User $user): bool
    {
        return $user->hasRole('siswa') && $user->is_active;
    }
}
