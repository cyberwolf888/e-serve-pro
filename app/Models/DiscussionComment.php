<?php

// DATA-24 / FR-SA-07 / FR-GR-14 / FR-SW-07 / M7.8

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['discussion_topic_id', 'author_id', 'body'])]
class DiscussionComment extends Model
{
    public function discussionTopic(): BelongsTo
    {
        return $this->belongsTo(DiscussionTopic::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
