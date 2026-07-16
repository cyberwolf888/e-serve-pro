<?php

// DATA-13 / FR-GR-12 / BR-03 / M6

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'name', 'weight', 'quiz_id'])]
class GradeComponent extends Model
{
    protected function casts(): array
    {
        return ['weight' => 'decimal:2'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ComponentScore::class);
    }
}
