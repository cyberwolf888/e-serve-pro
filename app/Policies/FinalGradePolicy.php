<?php

// §3.2 / FR-SW-06 / M6

namespace App\Policies;

use App\Models\FinalGrade;
use App\Models\User;

class FinalGradePolicy
{
    public function view(User $user, FinalGrade $grade): bool
    {
        return $user->hasRole('super_admin') || $grade->student_id === $user->id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('siswa');
    }
}
