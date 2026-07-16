<?php

// FR-GR-11 / FR-GR-12 / BR-03 / §3.2 / M6

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponentScoresRequest;
use App\Http\Requests\StoreGradeComponentRequest;
use App\Http\Requests\UpdateGradeComponentRequest;
use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Repositories\GradeRepository;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradeComponentController extends Controller
{
    public function __construct(
        private GradeRepository $repo,
        private GradeService $service,
    ) {}

    public function index(SchoolClass $class): View
    {
        $this->authorize('viewAny', [GradeComponent::class, $class]);

        return view('grades.components.index', [
            'class' => $class,
            'components' => $this->repo->components($class),
            'quizzes' => $class->quizzes()->orderBy('title')->get(),
            'routePrefix' => $this->prefix(),
        ]);
    }

    public function store(StoreGradeComponentRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->service->createComponent($class, $request->validated());

        return to_route($this->prefix().'.classes.grade-components.index', $class)
            ->with('success', 'Komponen nilai berhasil ditambahkan.');
    }

    public function update(UpdateGradeComponentRequest $request, SchoolClass $class, GradeComponent $gradeComponent): RedirectResponse
    {
        abort_unless($gradeComponent->class_id === $class->id, 404);
        $this->service->updateComponent($gradeComponent, $request->validated());

        return to_route($this->prefix().'.classes.grade-components.index', $class)
            ->with('success', 'Komponen nilai berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class, GradeComponent $gradeComponent): RedirectResponse
    {
        abort_unless($gradeComponent->class_id === $class->id, 404);
        $this->authorize('delete', $gradeComponent);
        $this->service->deleteComponent($gradeComponent);

        return to_route($this->prefix().'.classes.grade-components.index', $class)
            ->with('success', 'Komponen nilai berhasil dihapus.');
    }

    public function scores(SchoolClass $class, GradeComponent $gradeComponent): View
    {
        abort_unless($gradeComponent->class_id === $class->id, 404);
        $this->authorize('view', $gradeComponent);

        return view('grades.components.scores', [
            'class' => $class,
            'component' => $gradeComponent->load('scores'),
            'members' => $this->repo->members($class),
            'routePrefix' => $this->prefix(),
        ]);
    }

    public function storeScores(StoreComponentScoresRequest $request, SchoolClass $class, GradeComponent $gradeComponent): RedirectResponse
    {
        abort_unless($gradeComponent->class_id === $class->id, 404);
        $this->service->recordScores($gradeComponent, $request->validated('scores'));

        return to_route($this->prefix().'.classes.grade-components.scores', [$class, $gradeComponent])
            ->with('success', 'Nilai berhasil disimpan.');
    }

    private function prefix(): string
    {
        return auth()->user()?->hasRole('super_admin') ? 'admin' : 'guru';
    }
}
