<?php

// DATA-23 / DATA-24 / FR-SA-07 / FR-GR-14 / FR-SW-07 / M7.8

namespace App\Repositories;

use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
use App\Models\SchoolClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiscussionRepository
{
    public function forClass(SchoolClass $class): LengthAwarePaginator
    {
        return $class->discussions()
            ->with('author')
            ->withCount('comments')
            ->latest()
            ->paginate(10);
    }

    public function commentsFor(DiscussionTopic $topic): LengthAwarePaginator
    {
        return $topic->comments()
            ->with('author')
            ->oldest()
            ->paginate(20);
    }

    public function createTopic(array $data): DiscussionTopic
    {
        return DiscussionTopic::create($data);
    }

    public function createComment(array $data): DiscussionComment
    {
        return DiscussionComment::create($data);
    }

    public function deleteComment(DiscussionComment $comment): void
    {
        $comment->delete();
    }
}
