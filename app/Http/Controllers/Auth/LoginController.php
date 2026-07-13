<?php

// FR-AUTH-01 / FR-AUTH-05 / BR-06

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function showForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // BR-05: block inactive users
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.'])->onlyInput('email');
        }

        // BR-06
        $this->auth->logActivity($user, 'login', $request, 'Login berhasil');

        return $this->redirectByRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // BR-06
            $this->auth->logActivity($user, 'logout', $request, 'Logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login.show');
    }

    private function redirectByRole($user): RedirectResponse
    {
        return match (true) {
            $user->hasRole('super_admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('guru') => redirect()->route('guru.dashboard'),
            default => redirect()->route('siswa.dashboard'),
        };
    }
}
