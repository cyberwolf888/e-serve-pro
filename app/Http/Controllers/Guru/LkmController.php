<?php

// FR-SA-08 / FR-GR-11 / FR-GR-15 / BR-09 / DATA-25..27 / M7.9

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HasRoutePrefix;
use App\Http\Requests\GradeLkmSubmissionRequest;
use App\Http\Requests\StoreLkmAssignmentRequest;
use App\Http\Requests\StoreLkmRequest;
use App\Http\Requests\StoreLkmRoleRequest;
use App\Http\Requests\UpdateLkmAssignmentRequest;
use App\Http\Requests\UpdateLkmRequest;
use App\Http\Requests\UpdateLkmRoleRequest;
use App\Http\Requests\UpdateLkmSubmissionRequest;
use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\LkmRole;
use App\Models\SchoolClass;
use App\Repositories\LkmRepository;
use App\Services\LkmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LkmController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private LkmRepository $repo,
        private LkmService $service,
    ) {}

    public function index(SchoolClass $class): View
    {
        $this->authorize('viewAny', [Lkm::class, $class]);

        return view('guru.lkms.index', [
            'class' => $class,
            'lkms' => $this->repo->forClass($class),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(SchoolClass $class): View
    {
        $this->authorize('create', [Lkm::class, $class]);

        return view('guru.lkms.create', ['class' => $class, 'routePrefix' => $this->routePrefix()]);
    }

    public function store(StoreLkmRequest $request, SchoolClass $class): RedirectResponse
    {
        $lkm = $this->service->create($class, $request->user(), $request->validated());

        return to_route($this->routePrefix().'.classes.lkms.show', [$class, $lkm])
            ->with('success', 'LKM berhasil dibuat dan peran dibagikan.');
    }

    public function show(SchoolClass $class, Lkm $lkm): View
    {
        $this->authorize('view', $lkm);
        $members = $this->repo->paginatedMembers($class);

        return view('guru.lkms.show', [
            'class' => $class,
            'lkm' => $lkm,
            'roles' => $lkm->roles()->withCount('assignments')->orderBy('id')->get(),
            'members' => $members,
            'assignments' => $this->repo->assignmentsForStudents($lkm, $members->pluck('student_id')->all())->keyBy('student_id'),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function edit(SchoolClass $class, Lkm $lkm): View
    {
        $this->authorize('update', $lkm);

        return view('guru.lkms.edit', [
            'class' => $class,
            'lkm' => $lkm->load(['roles' => fn ($query) => $query->withCount('assignments')->orderBy('id')]),
            'structureLocked' => $lkm->assignments()->whereNotNull('proof_submitted_at')->exists(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdateLkmRequest $request, SchoolClass $class, Lkm $lkm): RedirectResponse
    {
        $this->service->update($lkm, $request->validated());

        return to_route($this->routePrefix().'.classes.lkms.show', [$class, $lkm])
            ->with('success', 'LKM berhasil diperbarui.');
    }

    public function storeRole(StoreLkmRoleRequest $request, SchoolClass $class, Lkm $lkm): RedirectResponse
    {
        $this->service->createRole($lkm, $request->validated());

        return back()->with('success', 'Peran berhasil ditambahkan.');
    }

    public function updateRole(UpdateLkmRoleRequest $request, SchoolClass $class, Lkm $lkm, LkmRole $role): RedirectResponse
    {
        $this->service->updateRole($lkm, $role, $request->validated());

        return back()->with('success', 'Peran berhasil diperbarui.');
    }

    public function destroyRole(SchoolClass $class, Lkm $lkm, LkmRole $role): RedirectResponse
    {
        $this->authorize('manageRoles', $lkm);
        $this->service->deleteRole($lkm, $role);

        return back()->with('success', 'Peran berhasil dihapus.');
    }

    public function storeAssignment(StoreLkmAssignmentRequest $request, SchoolClass $class, Lkm $lkm): RedirectResponse
    {
        $this->service->assignStudent($lkm, $request->integer('lkm_role_id'), $request->integer('student_id'));

        return back()->with('success', 'Peran mahasiswa berhasil ditetapkan.');
    }

    public function updateAssignment(UpdateLkmAssignmentRequest $request, SchoolClass $class, Lkm $lkm, LkmAssignment $assignment): RedirectResponse
    {
        $this->service->reassign($lkm, $assignment, $request->integer('lkm_role_id'));

        return back()->with('success', 'Peran mahasiswa berhasil diubah.');
    }

    public function editSubmission(SchoolClass $class, Lkm $lkm, LkmAssignment $assignment): View
    {
        $this->authorize('correctSubmission', $assignment);

        return view('guru.lkms.submission-edit', [
            'class' => $class,
            'lkm' => $lkm,
            'assignment' => $assignment->load(['student', 'role']),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function submissions(SchoolClass $class, Lkm $lkm): View
    {
        $this->authorize('viewAny', [Lkm::class, $class]);

        return view('guru.lkms.submissions', [
            'class' => $class,
            'lkm' => $lkm,
            'assignments' => $this->repo->submittedAssignments($lkm),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function gradeSubmission(GradeLkmSubmissionRequest $request, SchoolClass $class, Lkm $lkm, LkmAssignment $assignment): RedirectResponse
    {
        $this->service->correctSubmission($lkm, $assignment, $request->validated());

        return back()->with('success', 'Nilai LKM berhasil disimpan.');
    }

    public function updateSubmission(UpdateLkmSubmissionRequest $request, SchoolClass $class, Lkm $lkm, LkmAssignment $assignment): RedirectResponse
    {
        $this->service->correctSubmission($lkm, $assignment, $request->validated());

        return to_route($this->routePrefix().'.classes.lkms.show', [$class, $lkm])
            ->with('success', 'Kiriman mahasiswa berhasil diperbaiki.');
    }
}
