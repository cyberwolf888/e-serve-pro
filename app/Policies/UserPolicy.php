<?php

// §3.2 / FR-SA-02 / BR-05
// Methods filled in M2 — shell registered here for M1 gate.

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only super_admin may manage (create/edit/delete/deactivate) users.
     * FR-SA-02 / §3.2
     */
    public function manage(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin');
    }
}
