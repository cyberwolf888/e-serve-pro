<?php

// DATA-02 / FR-GR-02 / BR-01 / M3

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['guru_id', 'name', 'class_code', 'description', 'is_active'])]
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ClassMember::class, 'class_id');
    }

    // DATA-04 / M4
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'class_id');
    }

    // DATA-05 / M4
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'class_id');
    }

    // DATA-08 / FR-GR-09 / M5
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'class_id');
    }
}
