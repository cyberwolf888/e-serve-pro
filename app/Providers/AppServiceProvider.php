<?php

namespace App\Providers;

use App\Models\SchoolClass;
use App\Models\User;
use App\Policies\SchoolClassPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // §3.2 — UserPolicy
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);

        // FR-SA-04 / BR-06
        Gate::define(
            'viewLogViewer',
            fn (?User $user): bool => app()->isLocal() || $user?->hasRole('super_admin'),
        );
    }
}
