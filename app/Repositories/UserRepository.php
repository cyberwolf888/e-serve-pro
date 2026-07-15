<?php

// FR-SA-02 / DATA-01 / M2

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /** Paginated list of non-super_admin users. FR-SA-02 */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with(['roles', 'createdBy'])
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))
            ->latest()
            ->paginate($perPage);
    }

    /** Full (unpaginated) filtered list for client-side datatable. FR-SA-02 */
    public function getAll(?string $status = null, string $sort = 'newest'): Collection
    {
        // ponytail: loads all non-admin users at once; switch to paginate() + AJAX if >5k users
        return User::with(['roles', 'createdBy'])
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))
            ->when($status, fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /** Toggle is_active. BR-05 — never hard-delete. */
    public function setActive(User $user, bool $active): User
    {
        $user->update(['is_active' => $active]);

        return $user;
    }
}
