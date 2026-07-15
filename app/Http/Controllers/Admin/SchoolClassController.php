<?php

// FR-SA-03 / FR-GR-02 / BR-05 / §3.2 / M3

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSchoolClassRequest;
use App\Http\Requests\Admin\UpdateSchoolClassRequest;
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
        $classes = $this->repo->all($filters['status'] ?? null, $filters['sort'] ?? 'newest');

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $this->authorize('create', SchoolClass::class);

        return view('admin.classes.create', ['gurus' => User::role('guru')->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return to_route('admin.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(SchoolClass $class): View
    {
        $this->authorize('view', $class);

        return view('admin.classes.show', [
            'class' => $class->load('guru'),
            'members' => $this->repo->paginatedMembers($class),
        ]);
    }

    public function edit(SchoolClass $class): View
    {
        $this->authorize('update', $class);

        return view('admin.classes.edit', [
            'class' => $class,
            'gurus' => User::role('guru')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->service->update($class, $request->validated());

        return to_route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $this->authorize('deactivate', $class);
        $this->service->deactivate($class);

        return to_route('admin.classes.index')->with('success', 'Kelas berhasil dinonaktifkan.');
    }

    public function activate(SchoolClass $class): RedirectResponse
    {
        $this->authorize('activate', $class);
        $this->service->activate($class);

        return to_route('admin.classes.index')->with('success', 'Kelas berhasil diaktifkan kembali.');
    }
}
