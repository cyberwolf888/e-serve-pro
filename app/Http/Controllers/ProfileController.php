<?php

// FR-AUTH-01 / FR-AUTH-05 / FR-AUTH-06 / BR-05
// Profile management for all authenticated roles (super_admin, guru, siswa).

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Display the authenticated user's profile. FR-AUTH-01 */
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('profile.show', [
            'user' => $user,
            'roleLabel' => $this->roleLabel($user),
        ]);
    }

    /** Update the user's name, email, and optionally password. FR-AUTH-01 / FR-AUTH-05 / FR-AUTH-06 */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // BR-05: deactivated users cannot make any changes to their own account
        if (! $user->is_active) {
            abort(403, 'Akun dinonaktifkan.');
        }

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.show')->with('status', 'Profil berhasil diperbarui.');
    }

    /** Bahasa Indonesia display label for the primary role. */
    private function roleLabel(User $user): string
    {
        return match (true) {
            $user->hasRole('super_admin') => 'Super Admin',
            $user->hasRole('guru') => 'Guru',
            default => 'Siswa',
        };
    }
}
