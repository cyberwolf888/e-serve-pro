<?php

// §8 — Route map / M1 Auth & RBAC

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

// ─── Root redirect ───────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('auth.login.show'))->name('home');

// ─── Guest-only routes ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // FR-AUTH-01
    Route::get('/login', [LoginController::class, 'showForm'])->name('auth.login.show');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');

    // FR-AUTH-02 / FR-AUTH-03 (siswa self-register only)
    Route::get('/register', [RegisterController::class, 'showForm'])->name('auth.register.show');
    Route::post('/register', [RegisterController::class, 'register'])->name('auth.register');

    // FR-AUTH-04 / BR-02
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('auth.forgot.show');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendEmail'])->name('auth.forgot.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('auth.reset.show');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('auth.reset');
});

// Logout (authenticated users only)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// ─── Super Admin ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    // FR-SA-* routes added in M2+
});

// ─── Guru ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', fn () => view('guru.dashboard'))->name('dashboard');
    // FR-GR-* routes added in M3+
});

// ─── Siswa ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', fn () => view('siswa.dashboard'))->name('dashboard');
    // FR-SW-* routes added in M3+
});
