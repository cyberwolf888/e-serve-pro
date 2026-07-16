<?php

// FR-GR-11 / §3.2 / M6

namespace App\Http\Controllers;

use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;

class GradeController extends Controller
{
    public function __construct(private GradeService $service) {}

    public function calculate(SchoolClass $class): RedirectResponse
    {
        $this->authorize('calculate', [GradeComponent::class, $class]);
        $this->service->calculate($class);

        $prefix = auth()->user()?->hasRole('super_admin') ? 'admin' : 'guru';

        return to_route($prefix.'.classes.recap', $class)->with('success', 'Nilai akhir berhasil dihitung.');
    }
}
