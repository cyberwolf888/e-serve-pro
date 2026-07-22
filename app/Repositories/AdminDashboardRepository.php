<?php

// FR-SA-06

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AdminDashboardRepository
{
    public function countUsersByRole(string $role): int
    {
        return User::role($role)->count();
    }

    public function countActiveClasses(): int
    {
        return SchoolClass::where('is_active', true)->count();
    }

    public function countPublishedQuizzes(): int
    {
        return Quiz::where('is_published', true)->count();
    }

    public function countActiveUsers(CarbonInterface $start, CarbonInterface $end): int
    {
        return ActivityLog::whereBetween('created_at', [$start, $end])->distinct('user_id')->count('user_id');
    }

    public function countActivities(CarbonInterface $start, CarbonInterface $end): int
    {
        return ActivityLog::whereBetween('created_at', [$start, $end])->count();
    }

    public function activitiesByDate(CarbonInterface $start, CarbonInterface $end): Collection
    {
        return ActivityLog::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'date');
    }

    public function alerts(): array
    {
        $activeClasses = SchoolClass::where('is_active', true);

        return [
            'inactive_guru' => User::role('guru')->where('is_active', false)->count(),
            'inactive_siswa' => User::role('siswa')->where('is_active', false)->count(),
            'classes_without_students' => (clone $activeClasses)->doesntHave('members')->count(),
            'classes_without_materials' => (clone $activeClasses)->doesntHave('materials')->count(),
            'classes_without_published_quizzes' => (clone $activeClasses)
                ->whereDoesntHave('quizzes', fn ($query) => $query->where('is_published', true))
                ->count(),
        ];
    }

    public function latestActivities(): Collection
    {
        return ActivityLog::query()
            ->select(['id', 'user_id', 'event_type', 'description', 'created_at'])
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(10)
            ->get();
    }
}
