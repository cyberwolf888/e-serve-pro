<?php

// DATA-26 / FR-GR-15 / FR-SW-08 / M7.9

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['lkm_id', 'name', 'instructions', 'description', 'sop_items'])]
class LkmRole extends Model
{
    protected function casts(): array
    {
        return ['sop_items' => 'array'];
    }

    public function lkm(): BelongsTo
    {
        return $this->belongsTo(Lkm::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LkmAssignment::class);
    }
}
