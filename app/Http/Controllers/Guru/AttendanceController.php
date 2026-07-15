<?php

// FR-GR-07 / BR-06 / §3.2 / M4

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Repositories\AttendanceRepository;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceRepository $repo,
        private AttendanceService $service,
    ) {}

    public function edit(SchoolClass $class, Meeting $meeting): View
    {
        $this->authorize('recordAttendance', $meeting);

        return view('guru.attendance.edit', [
            'class' => $class,
            'meeting' => $meeting,
            'members' => $class->members()->with('student')->get(),
            'existing' => $this->repo->forMeeting($meeting),
        ]);
    }

    public function store(StoreAttendanceRequest $request, SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->service->record($meeting, $request->validated('statuses'), $request);

        return to_route('guru.classes.meetings.attendance.edit', [$class, $meeting])
            ->with('success', 'Absensi berhasil dicatat.');
    }
}
