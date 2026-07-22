<?php

// FR-GR-13

namespace App\Services;

use App\Models\User;
use App\Repositories\GuruDashboardRepository;

class GuruDashboardService
{
    public function __construct(private GuruDashboardRepository $repo) {}

    public function data(User $guru): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $now = now();
        $activitiesByDate = $this->repo->activitiesByDate($guru, $start, $end);
        $alerts = $this->repo->alerts($guru);
        $days = collect(range(0, 29))->map(fn ($offset) => $start->copy()->addDays($offset));

        return [
            'kpis' => [
                ['label' => 'Kelas Aktif', 'value' => $this->repo->countActiveClasses($guru)],
                ['label' => 'Total Siswa', 'value' => $this->repo->countStudents($guru)],
                ['label' => 'Pertemuan 30 Hari', 'value' => $this->repo->countUpcomingMeetings($guru, $now, $now->copy()->addDays(30)->endOfDay())],
                ['label' => 'Absensi Belum Dicatat', 'value' => $this->repo->countUnrecordedAttendances($guru, $now)],
                ['label' => 'Kuis Aktif', 'value' => $this->repo->countActiveQuizzes($guru, $now)],
                ['label' => 'Kuis Perlu Ditinjau', 'value' => $this->repo->countClosedQuizzesWithAttempts($guru, $now)],
            ],
            'chart' => [
                'categories' => $days->map(fn ($date) => $date->format('d M'))->all(),
                'data' => $days->map(fn ($date) => (int) ($activitiesByDate[$date->toDateString()] ?? 0))->values()->all(),
            ],
            'alerts' => collect([
                ['label' => 'Kelas aktif tanpa siswa', 'count' => $alerts['classes_without_students']],
                ['label' => 'Kelas aktif tanpa materi', 'count' => $alerts['classes_without_materials']],
                ['label' => 'Kelas aktif tanpa kuis terbit', 'count' => $alerts['classes_without_published_quizzes']],
            ])->filter(fn ($alert) => $alert['count'] > 0)->values(),
            'recentActivities' => $this->repo->latestActivities($guru),
        ];
    }
}
