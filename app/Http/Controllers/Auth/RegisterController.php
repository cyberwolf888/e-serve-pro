<?php

// FR-AUTH-02 / FR-AUTH-03 / FR-AUTH-06

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function showForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        // FR-AUTH-03: only siswa may self-register; role is NEVER taken from input
        $user = $this->auth->register($request->validated());

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('siswa.dashboard');
    }
}
