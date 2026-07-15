<?php

// FR-SA-02 / FR-AUTH-03 / BR-05 / DATA-01 / M2

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserAdminService
{
    public function __construct(private UserRepository $repo) {}

    /**
     * Create a new guru or siswa account.
     * FR-SA-02 / FR-AUTH-03 / DATA-01
     * ASSUMPTION: created_by stored for both roles per user decision.
     */
    public function createUser(array $data): User
    {
        $user = $this->repo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // cast hashes automatically (User model)
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        $user->assignRole($data['role']); // role from validated input: guru|siswa

        return $user;
    }

    /**
     * Edit name/email/password (role immutable). FR-SA-02
     * BR-05: only called on active users (enforced by UserPolicy::update).
     */
    public function updateUser(User $user, array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        return $this->repo->update($user, $payload);
    }

    /**
     * Deactivate user — sets is_active=0. BR-05: no hard-delete.
     * FR-SA-02
     */
    public function deactivate(User $user): User
    {
        return $this->repo->setActive($user, false);
    }

    /**
     * Reactivate user — sets is_active=1. FR-SA-02
     */
    public function reactivate(User $user): User
    {
        return $this->repo->setActive($user, true);
    }
}
