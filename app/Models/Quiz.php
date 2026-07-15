<?php

// DATA-08 / FR-GR-09 / M5

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'title', 'description', 'is_published', 'opens_at', 'closes_at'])]
class Quiz extends Model
{
    protected $attributes = [
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /** Quiz is time-wise open right now. Nullable bounds don't constrain. §9 */
    public function isWithinWindow(): bool
    {
        $now = now();

        return (! $this->opens_at || $now->gte($this->opens_at))
            && (! $this->closes_at || $now->lte($this->closes_at));
    }
}
