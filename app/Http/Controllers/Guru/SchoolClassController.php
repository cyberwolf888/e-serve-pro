<?php

// FR-GR-02 / FR-GR-03 / BR-05 / §3.2 / M3

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddClassStudentRequest;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use App\Repositories\SchoolClassRepository;
use App\Services\SchoolClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function __construct(
        private SchoolClassRepository $repo,
        private SchoolClassService $service,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', SchoolClass::class);

        $filters = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest'],
        ]);
        $classes = $this->repo->forGuru(auth()->user(), $filters['status'] ?? null, $filters['sort'] ?? 'newest');

        return view('guru.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $this->authorize('create', SchoolClass::class);

        return view('guru.classes.create');
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        $this->service->create($request->validated() + ['guru_id' => $request->user()->id]);

        return to_route('guru.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(SchoolClass $class): View
    {
        $this->authorize('view', $class);

        return view('guru.classes.show', [
            'class' => $class,
            'members' => $this->repo->paginatedMembers($class),
        ]);
    }

    public function edit(SchoolClass $class): View
    {
        $this->authorize('update', $class);

        return view('guru.classes.edit', compact('class'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->service->update($class, $request->validated());

        return to_route('guru.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $this->authorize('deactivate', $class);
        $this->service->deactivate($class);

        return to_route('guru.classes.index')->with('success', 'Kelas berhasil dinonaktifkan.');
    }

    public function activate(SchoolClass $class): RedirectResponse
    {
        $this->authorize('activate', $class);
        $this->service->activate($class);

        return to_route('guru.classes.index')->with('success', 'Kelas berhasil diaktifkan kembali.');
    }

    public function addStudent(AddClassStudentRequest $request, SchoolClass $class): RedirectResponse
    {
        $student = User::role('siswa')->where('is_active', true)->where('email', $request->string('email')->toString())->first();

        if (! $student) {
            return back()->withErrors(['email' => 'Siswa aktif dengan email tersebut tidak ditemukan.']);
        }

        $this->service->addStudent($class, $student);

        return to_route('guru.classes.show', $class)->with('success', 'Siswa berhasil ditambahkan.');
    }
}
