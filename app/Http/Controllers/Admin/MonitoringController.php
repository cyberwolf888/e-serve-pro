<?php

// FR-SA-04 / BR-06 / NFR-02

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MonitoringFilterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\ActivityLogRepository;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function __construct(
        private ActivityLogRepository $repo,
    ) {}

    public function index(MonitoringFilterRequest $request): View
    {
        $this->authorize('viewAny', ActivityLog::class);

        $filters = array_filter($request->validated(), fn ($v) => $v !== null && $v !== '');
        $logs = $this->repo->paginateForSuperAdmin($filters);

        return view('admin.monitoring.index', [
            'logs' => $logs,
            'eventTypes' => ['login', 'logout', 'quiz_attempt', 'attendance', 'other'],
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
            'filters' => $filters,
        ]);
    }
}
