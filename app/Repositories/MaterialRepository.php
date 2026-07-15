<?php

// DATA-04 / FR-GR-04 / FR-GR-05 / FR-SW-04 / M4

namespace App\Repositories;

use App\Models\Material;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;

class MaterialRepository
{
    public function forClass(SchoolClass $class): Collection
    {
        return $class->materials()->latest()->get();
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
}
