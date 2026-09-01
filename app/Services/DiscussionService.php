<?php

// DATA-23 / DATA-24 / FR-SA-07 / FR-GR-14 / FR-SW-07 / M7.8

namespace App\Services;

use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
use App\Models\SchoolClass;
use App\Models\User;
use App\Repositories\DiscussionRepository;

class DiscussionService
{
    public function __construct(private DiscussionRepository $repo) {}

    public function createTopic(SchoolClass $class, User $author, array $data): DiscussionTopic
    {
        return $this->repo->createTopic($data + [
            'class_id' => $class->id,
            'author_id' => $author->id,
        ]);
    }

    public function createComment(DiscussionTopic $topic, User $author, array $data): DiscussionComment
    {
        return $this->repo->createComment($data + [
            'discussion_topic_id' => $topic->id,
            'author_id' => $author->id,
        ]);
    }

    public function deleteComment(DiscussionComment $comment): void
    {
        $this->repo->deleteComment($comment);
    }
}
