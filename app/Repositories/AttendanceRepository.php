<?php

// DATA-07 / FR-GR-07 / M4

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    /** Existing attendance rows for a meeting, keyed by student_id. */
    public function forMeeting(Meeting $meeting): Collection
    {
        return $meeting->attendances()->get()->keyBy('student_id');
    }

    /** Create or update the attendance row for one student in one meeting. */
    public function upsert(Meeting $meeting, int $studentId, string $status): Attendance
    {
        return Attendance::updateOrCreate(
            ['meeting_id' => $meeting->id, 'student_id' => $studentId],
            ['status' => $status, 'recorded_at' => now()],
        );
    }
}
