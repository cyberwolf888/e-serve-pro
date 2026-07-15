<?php

// §3.2 / FR-SA-02 / BR-05 / M2

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** View user list — super_admin only. §3.2 */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    /** View a single user record. §3.2 */
    public function view(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin');
    }

    /** Create new user (guru or siswa). FR-SA-02 / FR-AUTH-03 */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    /**
     * Edit user — only if target is active (BR-05 read-only guard).
     * Reactivation uses a separate toggleStatus path, not this policy.
     * FR-SA-02 / BR-05
     */
    public function update(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin') && $target->is_active;
    }

    /**
     * Deactivate active user — super_admin only.
     * Guard: block if already inactive (idempotent protection).
     * FR-SA-02 / BR-05
     */
    public function deactivate(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin') && $target->is_active;
    }

    /**
     * Reactivate inactive user — super_admin only.
     * FR-SA-02 / BR-05
     */
    public function reactivate(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin') && ! $target->is_active;
    }

    /**
     * Legacy manage() — kept for backward compatibility (M1 tests).
     * FR-SA-02 / §3.2
     */
    public function manage(User $authUser, User $target): bool
    {
        return $authUser->hasRole('super_admin');
    }
}
