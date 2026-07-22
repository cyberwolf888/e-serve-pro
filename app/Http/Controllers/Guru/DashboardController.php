<?php

// FR-GR-13

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\GuruDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private GuruDashboardService $service) {}

    public function index(Request $request): View
    {
        return view('guru.dashboard', ['dashboard' => $this->service->data($request->user())]);
    }
}
