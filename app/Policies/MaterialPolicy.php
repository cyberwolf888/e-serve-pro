<?php

// §3.2 / FR-GR-04 / FR-GR-05 / FR-SW-04 / BR-04 / BR-05 / M4

namespace App\Policies;

use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class MaterialPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin')
            || ($user->hasRole('guru') && $class->guru_id === $user->id)
            || ($user->hasRole('siswa') && $class->members()->where('student_id', $user->id)->exists());
    }

    public function view(User $user, Material $material): bool
    {
        return $this->viewAny($user, $material->schoolClass);
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    public function update(User $user, Material $material): bool
    {
        return $this->writable($user, $material->schoolClass);
    }

    public function delete(User $user, Material $material): bool
    {
        return $this->writable($user, $material->schoolClass);
    }

    /** super_admin all / guru OWN active class of an active guru. §3.2 / BR-05 */
    private function writable(User $user, SchoolClass $class): bool
    {
        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && ($user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id));
    }
}
