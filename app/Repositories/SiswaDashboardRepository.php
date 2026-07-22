<?php

// FR-SW-06

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\ClassMember;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SiswaDashboardRepository
{
    public function countActiveClasses(User $student): int
    {
        return $this->classes($student)->where('is_active', true)->count();
    }

    public function countClasses(User $student): int
    {
        return $this->classes($student)->count();
    }

    public function countUpcomingMeetings(User $student, CarbonInterface $start, CarbonInterface $end): int
    {
        return Meeting::whereIn('class_id', $this->activeClasses($student)->select('id'))
            ->whereBetween('scheduled_at', [$start, $end])
            ->count();
    }

    public function countAvailableQuizzes(User $student, CarbonInterface $now): int
    {
        return $this->availableQuizzes($student, $now)->count();
    }

    public function countFinalGrades(User $student): int
    {
        return $student->finalGrades()->count();
    }

    public function activitiesByDate(User $student, CarbonInterface $start, CarbonInterface $end): Collection
    {
        return ActivityLog::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('user_id', $student->id)
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'date');
    }

    public function countUnattemptedQuizzes(User $student, CarbonInterface $now): int
    {
        return $this->availableQuizzes($student, $now)
            ->whereDoesntHave('attempts', fn ($query) => $query->where('student_id', $student->id))
            ->count();
    }

    public function latestActivities(User $student): Collection
    {
        return ActivityLog::query()
            ->select(['id', 'user_id', 'event_type', 'description', 'created_at'])
            ->where('user_id', $student->id)
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    private function availableQuizzes(User $student, CarbonInterface $now)
    {
        return Quiz::whereIn('class_id', $this->activeClasses($student)->select('id'))
            ->where('is_published', true)
            ->where(fn ($query) => $query->whereNull('opens_at')->orWhere('opens_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('closes_at')->orWhere('closes_at', '>=', $now));
    }

    private function activeClasses(User $student)
    {
        return $this->classes($student)->where('is_active', true);
    }

    private function classes(User $student)
    {
        return SchoolClass::whereIn('id', ClassMember::where('student_id', $student->id)->select('class_id'));
    }
}
