<?php

// FR-SA-02 / DATA-01 / M2

namespace App\Repositories;

use App\Models\User;
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
