<?php

// FR-SA-04 / BR-06

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogPolicy
{
    /** View all logs — super_admin only. */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }

    /** View a single log — super_admin only. */
    public function view(User $authUser, ActivityLog $log): bool
    {
        return $authUser->hasRole('super_admin');
    }

    /** Logs are read-only. */
    public function create(User $authUser): bool
    {
        return false;
    }

    /** Logs are read-only. */
    public function update(User $authUser, ActivityLog $log): bool
    {
        return false;
    }

    /** Logs are read-only. */
    public function delete(User $authUser, ActivityLog $log): bool
    {
        return false;
    }

    /** Logs are read-only. */
    public function restore(User $authUser, ActivityLog $log): bool
    {
        return false;
    }

    /** Logs are read-only. */
    public function forceDelete(User $authUser, ActivityLog $log): bool
    {
        return false;
    }
}
