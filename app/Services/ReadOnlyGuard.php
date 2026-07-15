<?php

// BR-05 / NFR-09 / M2
// Reusable guard: abort 403 if the owner of a record is inactive.
// Wire point M2: protects writes on users themselves.
// Wire point M3+: call ownerMustBeActive($class->guru) before writing to class-owned records.

namespace App\Services;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyGuard
{
    /**
     * Abort 403 if $owner is inactive.
     * Usage: ReadOnlyGuard::ownerMustBeActive($guru) before any write on guru-owned record.
     * BR-05 / NFR-09
     */
    public static function ownerMustBeActive(User $owner): void
    {
        if (! $owner->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Rekaman ini hanya bisa dibaca karena pemiliknya tidak aktif.');
        }
    }

    /**
     * Bool check — use in Policies where you need a return value.
     * BR-05
     */
    public static function isOwnerActive(User $owner): bool
    {
        return (bool) $owner->is_active;
    }
}
