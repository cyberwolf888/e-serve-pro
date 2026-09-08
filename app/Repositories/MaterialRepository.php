<?php

// DATA-04 / FR-GR-04 / FR-GR-05 / FR-SW-04 / M4

namespace App\Repositories;

use App\Models\Material;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialRepository
{
    public function forClass(SchoolClass $class): Collection
    {
        return $this->withDiscussionPreview($class)->get();
    }

    public function publishedForClass(SchoolClass $class): Collection
    {
        return $this->withDiscussionPreview($class)->where('is_published', true)->get();
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }

    public function update(Material $material, array $data): Material
    {
        $material->update($data);

        return $material;
    }

    public function delete(Material $material): void
    {
        $material->delete();
    }

    private function withDiscussionPreview(SchoolClass $class): HasMany
    {
        return $class->materials()
            ->with([
                'discussions' => fn ($query) => $query
                    ->with('author')
                    ->withCount('comments')
                    ->latest()
                    ->latest('id')
                    ->limit(3),
            ])
            ->withCount('discussions')
            ->latest();
    }
}
