<?php

// FR-PUB-01

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            return match (true) {
                $user->hasRole('super_admin') => redirect()->route('admin.dashboard'),
                $user->hasRole('guru') => redirect()->route('guru.dashboard'),
                default => redirect()->route('siswa.dashboard'),
            };
        }

        return view('home');
    }
}
