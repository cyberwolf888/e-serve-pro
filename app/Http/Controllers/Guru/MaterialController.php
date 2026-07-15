<?php

// FR-GR-04 / FR-GR-05 / BR-04 / §3.2 / M4

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Repositories\MaterialRepository;
use App\Services\MaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function __construct(
        private MaterialRepository $repo,
        private MaterialService $service,
    ) {}

    public function index(SchoolClass $class): View
    {
        $this->authorize('viewAny', [Material::class, $class]);

        return view('guru.materials.index', ['class' => $class, 'materials' => $this->repo->forClass($class)]);
    }

    public function create(SchoolClass $class): View
    {
        $this->authorize('create', [Material::class, $class]);

        return view('guru.materials.create', ['class' => $class]);
    }

    public function store(StoreMaterialRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->service->create($class, $request->validated());

        return to_route('guru.classes.materials.index', $class)->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class, Material $material): View
    {
        $this->authorize('update', $material);

        return view('guru.materials.edit', ['class' => $class, 'material' => $material]);
    }

    public function update(UpdateMaterialRequest $request, SchoolClass $class, Material $material): RedirectResponse
    {
        $this->service->update($material, $request->validated());

        return to_route('guru.classes.materials.index', $class)->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class, Material $material): RedirectResponse
    {
        $this->authorize('delete', $material);
        $this->service->delete($material);

        return to_route('guru.classes.materials.index', $class)->with('success', 'Materi berhasil dihapus.');
    }
}
