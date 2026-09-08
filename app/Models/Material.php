<?php

// DATA-04 / FR-GR-04 / FR-GR-05 / BR-04 / M4

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'title', 'description', 'type', 'figma_url', 'file_path', 'file_size_kb', 'is_published'])]
class Material extends Model
{
    protected $attributes = [
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_materials')->withTimestamps();
    }

    // DATA-23 / FR-GR-14 / FR-SW-07 / M7.8
    public function discussions(): HasMany
    {
        return $this->hasMany(DiscussionTopic::class);
    }
}
