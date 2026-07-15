<?php

// DATA-10 / FR-GR-09 / M5 — exactly one option per question has is_correct=1 (validated in QuizQuestionService)

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question_id', 'option_text', 'is_correct', 'label'])]
class QuizOption extends Model
{
    protected function casts(): array
    {
        return ['is_correct' => 'boolean'];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
