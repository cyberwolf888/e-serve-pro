<?php

// FR-SW-08 / BR-09 / DATA-25..27 / M7.9

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitLkmProofRequest;
use App\Http\Requests\SubmitLkmReflectionRequest;
use App\Models\Lkm;
use App\Models\SchoolClass;
use App\Repositories\LkmRepository;
use App\Services\LkmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LkmController extends Controller
{
    public function __construct(
        private LkmRepository $repo,
        private LkmService $service,
    ) {}

    public function show(SchoolClass $class, Lkm $lkm): View
    {
        abort_unless(Auth::user()->can('view', $lkm), 404);

        return view('siswa.lkms.show', [
            'class' => $class,
            'lkm' => $lkm,
            'assignment' => $this->repo->assignmentForStudent($lkm, Auth::user())->load('role'),
        ]);
    }

    public function submitProof(SubmitLkmProofRequest $request, SchoolClass $class, Lkm $lkm): RedirectResponse
    {
        $this->service->submitProof($lkm, $request->user(), $request->string('proof_url')->toString());

        return back()->with('success', 'Bukti berhasil dikirim. Lanjutkan refleksi SOP.');
    }

    public function submitReflection(SubmitLkmReflectionRequest $request, SchoolClass $class, Lkm $lkm): RedirectResponse
    {
        $this->service->submitReflection($lkm, $request->user(), $request->validated('sop_checks'));

        return back()->with('success', 'Refleksi berhasil dikirim.');
    }
}
