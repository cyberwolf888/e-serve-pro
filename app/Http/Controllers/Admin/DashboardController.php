<?php

// FR-SA-06

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private AdminDashboardService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', ActivityLog::class);

        return view('admin.dashboard', ['dashboard' => $this->service->data()]);
    }
}
