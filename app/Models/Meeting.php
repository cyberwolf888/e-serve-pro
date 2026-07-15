<?php

// DATA-05 / DATA-06 / FR-GR-06 / FR-GR-08 / M4

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'title', 'scheduled_at', 'notes'])]
class Meeting extends Model
{
    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'meeting_materials')->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
