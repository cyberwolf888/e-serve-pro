<?php

// DATA-05 / DATA-06 / FR-GR-06 / FR-GR-08 / FR-SW-04 / M4

namespace App\Repositories;

use App\Models\Meeting;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;

class MeetingRepository
{
    public function forClass(SchoolClass $class): Collection
    {
        return $class->meetings()->withCount(['materials', 'attendances'])->orderBy('scheduled_at', 'desc')->get();
    }

    /** Meetings + only the materials shared to them — FR-SW-04 (siswa view). */
    public function forStudentClass(SchoolClass $class): Collection
    {
        return $class->meetings()->with('materials')->orderBy('scheduled_at', 'desc')->get();
    }

    public function create(array $data): Meeting
    {
        return Meeting::create($data);
    }

    public function update(Meeting $meeting, array $data): Meeting
    {
        $meeting->update($data);

        return $meeting;
    }

    public function delete(Meeting $meeting): void
    {
        $meeting->delete();
    }
}
