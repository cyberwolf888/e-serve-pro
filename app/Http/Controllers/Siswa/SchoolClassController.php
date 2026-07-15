<?php

// FR-SW-03 / FR-SW-04 / BR-01 / §3.2 / M3

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\JoinSchoolClassRequest;
use App\Models\SchoolClass;
use App\Repositories\MeetingRepository;
use App\Repositories\QuizRepository;
use App\Repositories\SchoolClassRepository;
use App\Services\SchoolClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function __construct(
        private SchoolClassRepository $repo,
        private SchoolClassService $service,
        private MeetingRepository $meetingRepo,
        private QuizRepository $quizRepo,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', SchoolClass::class);

        return view('siswa.classes.index', ['classes' => $this->repo->forStudent(auth()->user())]);
    }

    public function join(JoinSchoolClassRequest $request): RedirectResponse
    {
        $class = $this->repo->findByCode($request->string('class_code')->upper()->toString());
        $this->service->addStudent($class, $request->user(), 'class_code');

        return to_route('siswa.classes.index')->with('success', 'Berhasil bergabung ke kelas.');
    }

    public function show(SchoolClass $class): View
    {
        $this->authorize('view', $class);

        // FR-SW-04 — meetings + only the materials shared to them
        // FR-SW-05 — quizzes currently available to this student
        return view('siswa.classes.show', [
            'class' => $class,
            'meetings' => $this->meetingRepo->forStudentClass($class),
            'quizzes' => $this->quizRepo->availableForStudent($class, auth()->user()),
        ]);
    }
}
