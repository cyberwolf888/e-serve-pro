<?php

// FR-SA-07 / FR-GR-14 / FR-SW-07 / M7.8

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscussionCommentRequest;
use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
use App\Models\SchoolClass;
use App\Services\DiscussionService;
use Illuminate\Http\RedirectResponse;

class DiscussionCommentController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private DiscussionService $service) {}

    public function store(
        StoreDiscussionCommentRequest $request,
        SchoolClass $class,
        DiscussionTopic $discussion,
    ): RedirectResponse {
        $this->service->createComment($discussion, $request->user(), $request->validated());

        return to_route($this->routePrefix().'.classes.discussions.show', [$class, $discussion])
            ->with('success', 'Komentar berhasil dikirim.');
    }

    public function destroy(
        SchoolClass $class,
        DiscussionTopic $discussion,
        DiscussionComment $comment,
    ): RedirectResponse {
        $this->authorize('delete', $comment);
        $this->service->deleteComment($comment);

        return to_route($this->routePrefix().'.classes.discussions.show', [$class, $discussion])
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
