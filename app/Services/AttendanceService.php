<?php

// FR-GR-07 / BR-06 / M4

namespace App\Services;

use App\Models\Meeting;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;

class AttendanceService
{
    public function __construct(
        private AttendanceRepository $repo,
        private AuthService $auth,
    ) {}

    /**
     * Record attendance for a meeting. $statuses is [student_id => status].
     * Only class members are recorded — ignores/rejects foreign ids defensively.
     * BR-06: one activity_logs row per student per submission.
     */
    public function record(Meeting $meeting, array $statuses, Request $request): void
    {
        $memberIds = $meeting->schoolClass->members()->pluck('student_id')->all();

        foreach ($statuses as $studentId => $status) {
            if (! in_array((int) $studentId, $memberIds, true)) {
                continue;
            }

            $attendance = $this->repo->upsert($meeting, (int) $studentId, $status);

            $this->auth->logActivity(
                $attendance->student,
                'attendance',
                $request,
                "Absensi dicatat: {$status}",
                $attendance,
            );
        }
    }
}
