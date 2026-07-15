<?php

// DATA-04 / FR-GR-04 / FR-GR-05 / BR-04 / M4

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['class_id', 'title', 'type', 'figma_url', 'file_path', 'file_size_kb'])]
class Material extends Model
{
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_materials')->withTimestamps();
    }
}
