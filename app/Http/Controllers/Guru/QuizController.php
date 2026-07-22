<?php

// FR-GR-09 / §3.2 / M5 / ADMIN_CLASS_ACCESS_PLAN

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HasRoutePrefix;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Repositories\QuizRepository;
use App\Services\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private QuizRepository $repo,
        private QuizService $service,
    ) {}

    public function index(SchoolClass $class): View
    {
        $this->authorize('viewAny', [Quiz::class, $class]);

        return view('guru.quizzes.index', [
            'class' => $class,
            'quizzes' => $this->repo->forClass($class),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(SchoolClass $class): View
    {
        $this->authorize('create', [Quiz::class, $class]);

        return view('guru.quizzes.create', [
            'class' => $class,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreQuizRequest $request, SchoolClass $class): RedirectResponse
    {
        $quiz = $this->service->create($class, $request->validated());

        return to_route($this->routePrefix().'.classes.quizzes.show', [$class, $quiz])
            ->with('success', 'Kuis berhasil dibuat.');
    }

    public function show(SchoolClass $class, Quiz $quiz): View
    {
        $this->authorize('view', $quiz);

        return view('guru.quizzes.show', [
            'class' => $class,
            'quiz' => $quiz->loadCount('attempts')->load('questions.options'),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function edit(SchoolClass $class, Quiz $quiz): View
    {
        $this->authorize('update', $quiz);

        return view('guru.quizzes.edit', [
            'class' => $class,
            'quiz' => $quiz,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdateQuizRequest $request, SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->service->update($quiz, $request->validated());

        return to_route($this->routePrefix().'.classes.quizzes.show', [$class, $quiz])
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorize('delete', $quiz);
        $this->service->delete($quiz);

        return to_route($this->routePrefix().'.classes.quizzes.index', $class)
            ->with('success', 'Kuis berhasil dihapus.');
    }

    public function publish(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorize('publish', $quiz);
        $this->service->publish($quiz);

        return to_route($this->routePrefix().'.classes.quizzes.show', [$class, $quiz])
            ->with('success', 'Kuis berhasil dipublikasikan.');
    }

    public function unpublish(SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->authorize('unpublish', $quiz);
        $this->service->unpublish($quiz);

        return to_route($this->routePrefix().'.classes.quizzes.show', [$class, $quiz])
            ->with('success', 'Kuis dibatalkan publikasinya.');
    }
}
