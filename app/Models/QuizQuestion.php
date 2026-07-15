<?php

// DATA-09 / FR-GR-09 / M5

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['quiz_id', 'question_text', 'order'])]
class QuizQuestion extends Model
{
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id')->orderBy('label');
    }

    public function correctOption(): ?QuizOption
    {
        return $this->options->firstWhere('is_correct', true);
    }
}
