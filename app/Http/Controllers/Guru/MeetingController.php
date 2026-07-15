<?php

// FR-GR-06 / FR-GR-08 / §3.2 / M4

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareMaterialsRequest;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Repositories\MaterialRepository;
use App\Repositories\MeetingRepository;
use App\Services\MeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function __construct(
        private MeetingRepository $repo,
        private MeetingService $service,
        private MaterialRepository $materialRepo,
    ) {}

    public function index(SchoolClass $class): View
    {
        $this->authorize('viewAny', [Meeting::class, $class]);

        return view('guru.meetings.index', ['class' => $class, 'meetings' => $this->repo->forClass($class)]);
    }

    public function create(SchoolClass $class): View
    {
        $this->authorize('create', [Meeting::class, $class]);

        return view('guru.meetings.create', ['class' => $class]);
    }

    public function store(StoreMeetingRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->service->create($class, $request->validated());

        return to_route('guru.classes.meetings.index', $class)->with('success', 'Pertemuan berhasil dibuat.');
    }

    // FR-GR-08 hub page: meeting details + share-materials checklist + link to attendance
    public function show(SchoolClass $class, Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        return view('guru.meetings.show', [
            'class' => $class,
            'meeting' => $meeting->load('materials'),
            'classMaterials' => $this->materialRepo->forClass($class),
        ]);
    }

    public function edit(SchoolClass $class, Meeting $meeting): View
    {
        $this->authorize('update', $meeting);

        return view('guru.meetings.edit', ['class' => $class, 'meeting' => $meeting]);
    }

    public function update(UpdateMeetingRequest $request, SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->service->update($meeting, $request->validated());

        return to_route('guru.classes.meetings.index', $class)->with('success', 'Pertemuan berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->authorize('delete', $meeting);
        $this->service->delete($meeting);

        return to_route('guru.classes.meetings.index', $class)->with('success', 'Pertemuan berhasil dihapus.');
    }

    public function share(ShareMaterialsRequest $request, SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->service->shareMaterials($meeting, $request->validated('material_ids', []));

        return to_route('guru.classes.meetings.show', [$class, $meeting])->with('success', 'Materi berhasil dibagikan.');
    }
}
