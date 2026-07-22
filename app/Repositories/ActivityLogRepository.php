<?php

// FR-SA-04 / BR-06 / NFR-02

namespace App\Repositories;

use App\Models\ActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogRepository
{
    /** Filtered, paginated activity logs for Super Admin monitoring. */
    public function paginateForSuperAdmin(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('user_id', $id))
            ->when($filters['event_type'] ?? null, fn ($q, $type) => $q->where('event_type', $type))
            ->when(
                ($filters['date_from'] ?? null) && ($filters['date_to'] ?? null),
                fn ($q) => $q->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]),
            )
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
