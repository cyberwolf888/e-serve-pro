<?php

// DATA-27 / FR-GR-11 / FR-GR-15 / FR-SW-08 / M7.9

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lkm_id', 'lkm_role_id', 'student_id', 'proof_url', 'proof_submitted_at', 'sop_checks', 'reflection_submitted_at', 'score'])]
class LkmAssignment extends Model
{
    protected function casts(): array
    {
        return [
            'proof_submitted_at' => 'datetime',
            'sop_checks' => 'array',
            'reflection_submitted_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    public function lkm(): BelongsTo
    {
        return $this->belongsTo(Lkm::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(LkmRole::class, 'lkm_role_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
