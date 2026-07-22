<?php

// FR-GR-11 / §3.2 / M6 / ADMIN_CLASS_ACCESS_PLAN

namespace App\Http\Controllers;

use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;

class GradeController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private GradeService $service) {}

    public function calculate(SchoolClass $class): RedirectResponse
    {
        $this->authorize('calculate', [GradeComponent::class, $class]);
        $this->service->calculate($class);

        return to_route($this->routePrefix().'.classes.recap', $class)->with('success', 'Nilai akhir berhasil dihitung.');
    }
}
