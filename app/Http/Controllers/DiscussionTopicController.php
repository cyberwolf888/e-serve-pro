<?php

// FR-SA-07 / FR-GR-14 / FR-SW-07 / M7.8

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscussionTopicRequest;
use App\Models\DiscussionTopic;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Repositories\DiscussionRepository;
use App\Services\DiscussionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscussionTopicController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private DiscussionRepository $repo,
        private DiscussionService $service,
    ) {}

    public function index(SchoolClass $class, ?Material $material = null): View
    {
        $this->authorize('viewAny', [DiscussionTopic::class, $class]);

        if ($material) {
            $this->authorize('view', $material);
        }

        return view('discussions.index', [
            'class' => $class,
            'material' => $material,
            'discussions' => $material ? $this->repo->forMaterial($material) : $this->repo->forClass($class),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(SchoolClass $class, Material $material): View
    {
        $this->authorize('create', [DiscussionTopic::class, $class, $material]);

        return view('discussions.create', [
            'class' => $class,
            'material' => $material,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreDiscussionTopicRequest $request, SchoolClass $class, Material $material): RedirectResponse
    {
        $discussion = $this->service->createTopic($material, $request->user(), $request->validated());

        return to_route($this->routePrefix().'.classes.discussions.show', [$class, $discussion])
            ->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function show(SchoolClass $class, DiscussionTopic $discussion): View
    {
        $this->authorize('view', $discussion);

        return view('discussions.show', [
            'class' => $class,
            'discussion' => $discussion->load(['author', 'material']),
            'comments' => $this->repo->commentsFor($discussion),
            'routePrefix' => $this->routePrefix(),
        ]);
    }
}
