<?php

// FR-SA-06

namespace App\Services;

use App\Repositories\AdminDashboardRepository;

class AdminDashboardService
{
    public function __construct(private AdminDashboardRepository $repo) {}

    public function data(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $activitiesByDate = $this->repo->activitiesByDate($start, $end);
        $alerts = $this->repo->alerts();
        $days = collect(range(0, 29))->map(fn ($offset) => $start->copy()->addDays($offset));

        return [
            'kpis' => [
                ['label' => 'Total Guru', 'value' => $this->repo->countUsersByRole('guru'), 'route' => 'admin.users.index'],
                ['label' => 'Total Siswa', 'value' => $this->repo->countUsersByRole('siswa'), 'route' => 'admin.users.index'],
                ['label' => 'Kelas Aktif', 'value' => $this->repo->countActiveClasses(), 'route' => 'admin.classes.index'],
                ['label' => 'Pengguna Aktif 30 Hari', 'value' => $this->repo->countActiveUsers($start, $end), 'route' => 'admin.monitoring'],
                ['label' => 'Kuis Terbit', 'value' => $this->repo->countPublishedQuizzes(), 'route' => 'admin.classes.index'],
                ['label' => 'Aktivitas 30 Hari', 'value' => $this->repo->countActivities($start, $end), 'route' => 'admin.monitoring'],
            ],
            'chart' => [
                'categories' => $days->map(fn ($date) => $date->format('d M'))->all(),
                'data' => $days
                    ->map(fn ($date) => (int) ($activitiesByDate[$date->toDateString()] ?? 0))
                    ->values()
                    ->all(),
            ],
            'alerts' => collect([
                ['label' => 'Guru nonaktif', 'count' => $alerts['inactive_guru'], 'route' => 'admin.users.index'],
                ['label' => 'Siswa nonaktif', 'count' => $alerts['inactive_siswa'], 'route' => 'admin.users.index'],
                ['label' => 'Kelas aktif tanpa siswa', 'count' => $alerts['classes_without_students'], 'route' => 'admin.classes.index'],
                ['label' => 'Kelas aktif tanpa materi', 'count' => $alerts['classes_without_materials'], 'route' => 'admin.classes.index'],
                ['label' => 'Kelas aktif tanpa kuis terbit', 'count' => $alerts['classes_without_published_quizzes'], 'route' => 'admin.classes.index'],
            ])->filter(fn ($alert) => $alert['count'] > 0)->values(),
            'recentActivities' => $this->repo->latestActivities(),
        ];
    }
}
