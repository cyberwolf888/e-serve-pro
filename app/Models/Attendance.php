<?php

// DATA-07 / FR-GR-07 / BR-06 / M4

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['meeting_id', 'student_id', 'status', 'recorded_at'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
