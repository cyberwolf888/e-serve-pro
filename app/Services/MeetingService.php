<?php

// FR-GR-06 / FR-GR-08 / M4

namespace App\Services;

use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Repositories\MeetingRepository;

class MeetingService
{
    public function __construct(private MeetingRepository $repo) {}

    public function create(SchoolClass $class, array $data): Meeting
    {
        return $this->repo->create($data + ['class_id' => $class->id]);
    }

    public function update(Meeting $meeting, array $data): Meeting
    {
        return $this->repo->update($meeting, $data);
    }

    public function delete(Meeting $meeting): void
    {
        $this->repo->delete($meeting);
    }

    /** Replace this meeting's shared materials with exactly the given set. FR-GR-08 */
    public function shareMaterials(Meeting $meeting, array $materialIds): void
    {
        $meeting->materials()->sync($materialIds);
    }
}
