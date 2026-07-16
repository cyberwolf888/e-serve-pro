<?php

namespace App\Providers;

use App\Models\FinalGrade;
use App\Models\GradeComponent;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use App\Policies\FinalGradePolicy;
use App\Policies\GradeComponentPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\MeetingPolicy;
use App\Policies\QuizPolicy;
use App\Policies\QuizQuestionPolicy;
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
        // M4
        Gate::policy(Material::class, MaterialPolicy::class);
        Gate::policy(Meeting::class, MeetingPolicy::class);

        // M5
        Gate::policy(Quiz::class, QuizPolicy::class);
        Gate::policy(QuizQuestion::class, QuizQuestionPolicy::class);

        // M6
        Gate::policy(GradeComponent::class, GradeComponentPolicy::class);
        Gate::policy(FinalGrade::class, FinalGradePolicy::class);

        // FR-SA-04 / BR-06
        Gate::define(
            'viewLogViewer',
            fn (?User $user): bool => app()->isLocal() || $user?->hasRole('super_admin'),
        );
    }
}
