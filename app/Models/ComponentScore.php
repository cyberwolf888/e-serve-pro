<?php

// DATA-15 / FR-GR-11 / M6

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['grade_component_id', 'student_id', 'score', 'is_manual_override'])]
class ComponentScore extends Model
{
    protected $attributes = ['is_manual_override' => false];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'is_manual_override' => 'boolean'];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class, 'grade_component_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
