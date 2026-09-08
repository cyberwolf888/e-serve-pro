<?php

// DATA-25 / FR-GR-15 / FR-SW-08 / M7.9

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'created_by', 'title', 'description', 'is_published'])]
class Lkm extends Model
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(LkmRole::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LkmAssignment::class);
    }
}
