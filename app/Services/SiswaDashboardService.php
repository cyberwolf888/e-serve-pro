<?php

// FR-SW-06

namespace App\Services;

use App\Models\User;
use App\Repositories\SiswaDashboardRepository;

class SiswaDashboardService
{
    public function __construct(private SiswaDashboardRepository $repo) {}

    public function data(User $student): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $now = now();
        $activitiesByDate = $this->repo->activitiesByDate($student, $start, $end);
        $days = collect(range(0, 29))->map(fn ($offset) => $start->copy()->addDays($offset));

        return [
            'kpis' => [
                ['label' => 'Kelas Aktif', 'value' => $this->repo->countActiveClasses($student), 'route' => 'siswa.classes.index'],
                ['label' => 'Pertemuan 30 Hari', 'value' => $this->repo->countUpcomingMeetings($student, $now, $now->copy()->addDays(30)->endOfDay()), 'route' => 'siswa.classes.index'],
                ['label' => 'Kuis Tersedia', 'value' => $this->repo->countAvailableQuizzes($student, $now), 'route' => 'siswa.classes.index'],
                ['label' => 'Nilai Akhir', 'value' => $this->repo->countFinalGrades($student), 'route' => 'siswa.grades.index'],
            ],
            'chart' => [
                'categories' => $days->map(fn ($date) => $date->format('d M'))->all(),
                'data' => $days->map(fn ($date) => (int) ($activitiesByDate[$date->toDateString()] ?? 0))->values()->all(),
            ],
            'alerts' => collect([
                ['label' => 'Belum bergabung ke kelas', 'count' => $this->repo->countClasses($student) === 0 ? 1 : 0, 'route' => 'siswa.classes.index'],
                ['label' => 'Kuis belum dikerjakan', 'count' => $this->repo->countUnattemptedQuizzes($student, $now), 'route' => 'siswa.classes.index'],
            ])->filter(fn ($alert) => $alert['count'] > 0)->values(),
            'recentActivities' => $this->repo->latestActivities($student),
        ];
    }
}
