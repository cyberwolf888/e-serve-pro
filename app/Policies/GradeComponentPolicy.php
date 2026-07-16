<?php

// §3.2 / FR-GR-10 / FR-GR-11 / FR-GR-12 / BR-05 / M6

namespace App\Policies;

use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ReadOnlyGuard;

class GradeComponentPolicy
{
    public function viewAny(User $user, SchoolClass $class): bool
    {
        return $this->readable($user, $class);
    }

    public function view(User $user, GradeComponent $component): bool
    {
        return $this->readable($user, $component->schoolClass);
    }

    public function create(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    public function update(User $user, GradeComponent $component): bool
    {
        return $this->writable($user, $component->schoolClass);
    }

    public function delete(User $user, GradeComponent $component): bool
    {
        return $this->update($user, $component);
    }

    public function calculate(User $user, SchoolClass $class): bool
    {
        return $this->writable($user, $class);
    }

    public function export(User $user, SchoolClass $class): bool
    {
        return $this->readable($user, $class);
    }

    private function readable(User $user, SchoolClass $class): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('guru') && $class->guru_id === $user->id);
    }

    private function writable(User $user, SchoolClass $class): bool
    {
        return $class->is_active
            && ReadOnlyGuard::isOwnerActive($class->guru)
            && $this->readable($user, $class);
    }
}
