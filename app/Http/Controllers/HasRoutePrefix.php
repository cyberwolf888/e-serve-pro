<?php

// FR-SA-03 / FR-GR-02 / §8 — role-aware route prefix used by shared teacher controllers

namespace App\Http\Controllers;

trait HasRoutePrefix
{
    protected function routePrefix(): string
    {
        return auth()->user()?->hasRole('super_admin') ? 'admin' : 'guru';
    }
}
