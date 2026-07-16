<?php

// DATA-14 / FR-GR-11 / M6

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['class_id', 'student_id', 'final_score', 'calculated_at'])]
class FinalGrade extends Model
{
    protected function casts(): array
    {
        return ['final_score' => 'decimal:2', 'calculated_at' => 'datetime'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
