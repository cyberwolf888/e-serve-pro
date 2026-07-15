<?php

// FR-GR-05 / FR-SW-04 / §3.2 / M4
// Single shared download endpoint for all roles — authorization is via MaterialPolicy::view,
// so no need to duplicate this controller per role namespace.

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialDownloadController extends Controller
{
    public function __invoke(Material $material): StreamedResponse
    {
        $this->authorize('view', $material);

        abort_unless($material->type === 'file' && $material->file_path, 404);

        return Storage::disk('local')->download($material->file_path, $material->title.'.pdf');
    }
}
