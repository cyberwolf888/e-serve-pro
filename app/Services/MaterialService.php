<?php

// FR-GR-04 / FR-GR-05 / BR-04 / M4
// ASSUMPTION: PDF files are stored on the private "local" disk (not "public") because
// §3.2 restricts material visibility to class members — a public disk would expose
// files via a guessable URL with no authorization check. Access is instead served
// through the authorized MaterialDownloadController.

namespace App\Services;

use App\Models\Material;
use App\Models\SchoolClass;
use App\Repositories\MaterialRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MaterialService
{
    public function __construct(private MaterialRepository $repo) {}

    public function create(SchoolClass $class, array $data): Material
    {
        $attributes = ['class_id' => $class->id, 'title' => $data['title'], 'type' => $data['type']];

        if ($data['type'] === 'figma') {
            $attributes['figma_url'] = $data['figma_url'];
        } else {
            $attributes += $this->storeFile($class, $data['file']);
        }

        return $this->repo->create($attributes);
    }

    public function update(Material $material, array $data): Material
    {
        $attributes = ['title' => $data['title'], 'type' => $data['type']];

        if ($data['type'] === 'figma') {
            $attributes['figma_url'] = $data['figma_url'];
            $attributes['file_path'] = null;
            $attributes['file_size_kb'] = null;
            $this->deleteFile($material);
        } elseif (isset($data['file'])) {
            $this->deleteFile($material);
            $attributes += $this->storeFile($material->schoolClass, $data['file']);
            $attributes['figma_url'] = null;
        }

        return $this->repo->update($material, $attributes);
    }

    public function delete(Material $material): void
    {
        $this->deleteFile($material);
        $this->repo->delete($material);
    }

    private function storeFile(SchoolClass $class, UploadedFile $file): array
    {
        $path = $file->store("materials/{$class->id}", 'local');

        return ['file_path' => $path, 'file_size_kb' => (int) ceil($file->getSize() / 1024)];
    }

    private function deleteFile(Material $material): void
    {
        if ($material->file_path) {
            Storage::disk('local')->delete($material->file_path);
        }
    }
}
