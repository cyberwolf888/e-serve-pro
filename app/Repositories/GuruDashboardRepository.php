<?php

// FR-GR-13

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\ClassMember;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class GuruDashboardRepository
{
    public function countActiveClasses(User $guru): int
    {
        return $this->classes($guru)->where('is_active', true)->count();
    }

    public function countStudents(User $guru): int
    {
        return ClassMember::whereIn('class_id', $this->classes($guru)->select('id'))
            ->distinct('student_id')
            ->count('student_id');
    }

    public function countUpcomingMeetings(User $guru, CarbonInterface $start, CarbonInterface $end): int
    {
        return Meeting::whereIn('class_id', $this->classes($guru)->select('id'))
            ->whereBetween('scheduled_at', [$start, $end])
            ->count();
    }

    public function countUnrecordedAttendances(User $guru, CarbonInterface $now): int
    {
        return Meeting::whereIn('class_id', $this->classes($guru)->select('id'))
            ->where('scheduled_at', '<', $now)
            ->whereRaw('(SELECT COUNT(*) FROM attendances WHERE attendances.meeting_id = meetings.id) < (SELECT COUNT(*) FROM class_members WHERE class_members.class_id = meetings.class_id)')
            ->count();
    }

    public function countActiveQuizzes(User $guru, CarbonInterface $now): int
    {
        return Quiz::whereIn('class_id', $this->classes($guru)->select('id'))
            ->where('is_published', true)
            ->where(fn ($query) => $query->whereNull('opens_at')->orWhere('opens_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('closes_at')->orWhere('closes_at', '>=', $now))
            ->count();
    }

    public function countClosedQuizzesWithAttempts(User $guru, CarbonInterface $now): int
    {
        return Quiz::whereIn('class_id', $this->classes($guru)->select('id'))
            ->where('is_published', true)
            ->where('closes_at', '<', $now)
            ->has('attempts')
            ->count();
    }

    public function activitiesByDate(User $guru, CarbonInterface $start, CarbonInterface $end): Collection
    {
        return ActivityLog::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('user_id', $guru->id)
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'date');
    }

    public function alerts(User $guru): array
    {
        $activeClasses = $this->classes($guru)->where('is_active', true);

        return [
            'classes_without_students' => (clone $activeClasses)->doesntHave('members')->count(),
            'classes_without_materials' => (clone $activeClasses)->doesntHave('materials')->count(),
            'classes_without_published_quizzes' => (clone $activeClasses)
                ->whereDoesntHave('quizzes', fn ($query) => $query->where('is_published', true))
                ->count(),
        ];
    }

    public function latestActivities(User $guru): Collection
    {
        return ActivityLog::query()
            ->select(['id', 'user_id', 'event_type', 'description', 'created_at'])
            ->where('user_id', $guru->id)
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    private function classes(User $guru)
    {
        return SchoolClass::whereBelongsTo($guru, 'guru');
    }
}
