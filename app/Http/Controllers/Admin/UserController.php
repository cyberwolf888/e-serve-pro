<?php

// FR-SA-02 / BR-05 / §3.2 / §8 / §9 / M2

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\ReadOnlyGuard;
use App\Services\UserAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $repo,
        private UserAdminService $service,
    ) {}

    /** GET /admin/users — FR-SA-02 */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest'],
        ]);
        $users = $this->repo->getAll($filters['status'] ?? null, $filters['sort'] ?? 'newest');

        return view('admin.users.index', compact('users'));
    }

    /** GET /admin/users/create */
    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    /** POST /admin/users — FR-SA-02 / §9 */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        // authorize() already called in StoreUserRequest::authorize()
        $this->service->createUser($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    /** GET /admin/users/{user}/edit */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    /** PUT /admin/users/{user} — FR-SA-02 / BR-05 */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // authorize() called in UpdateUserRequest::authorize() (checks is_active via policy)
        // Extra guard: belt-and-suspenders explicit check for direct calls
        ReadOnlyGuard::ownerMustBeActive($user);

        $this->service->updateUser($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * PATCH /admin/users/{user}/status
     * Deactivates active user or reactivates inactive user. BR-05 / FR-SA-02
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->is_active) {
            $this->authorize('deactivate', $user);
            $this->service->deactivate($user);
            $message = 'Pengguna berhasil dinonaktifkan.';
        } else {
            $this->authorize('reactivate', $user);
            $this->service->reactivate($user);
            $message = 'Pengguna berhasil diaktifkan kembali.';
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}
