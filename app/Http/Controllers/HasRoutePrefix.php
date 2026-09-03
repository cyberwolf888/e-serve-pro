<?php

// FR-SA-03 / FR-GR-02 / FR-SW-07 / §8 — role-aware prefix used by shared controllers

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasRoutePrefix
{
    protected function routePrefix(): string
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('super_admin')
            ? 'admin'
            : ($user?->hasRole('siswa') ? 'siswa' : 'guru');
    }
}
