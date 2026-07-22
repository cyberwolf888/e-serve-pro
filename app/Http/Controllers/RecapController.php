<?php

// FR-GR-10 / FR-SA-05 / FR-SW-06 / §3.2 / M6 / ADMIN_CLASS_ACCESS_PLAN

namespace App\Http\Controllers;

use App\Models\FinalGrade;
use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Repositories\GradeRepository;
use App\Services\SiswaDashboardService;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecapController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private GradeRepository $repo,
        private SiswaDashboardService $siswaDashboard,
    ) {}

    public function classRecap(SchoolClass $class): View
    {
        $this->authorize('viewAny', [GradeComponent::class, $class]);

        return view('grades.recap.class', [
            'class' => $this->repo->recap($class),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function adminRecap(): View
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);

        return view('grades.recap.admin', ['classes' => $this->repo->allRecap()]);
    }

    public function studentGrades(): View
    {
        $this->authorize('viewAny', FinalGrade::class);

        return view('siswa.grades.index', ['grades' => $this->repo->gradesForStudent(auth()->user())]);
    }

    public function studentDashboard(): View
    {
        $this->authorize('viewAny', FinalGrade::class);

        return view('siswa.dashboard', ['dashboard' => $this->siswaDashboard->data(auth()->user())]);
    }

    public function exportClass(SchoolClass $class): StreamedResponse
    {
        $this->authorize('export', [GradeComponent::class, $class]);

        return $this->export([$this->repo->recap($class)], "rekap-{$class->class_code}.xlsx");
    }

    public function exportAll(): StreamedResponse
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);

        return $this->export($this->repo->allRecap()->all(), 'rekap-semua-kelas.xlsx');
    }

    private function export(array $classes, string $filename): StreamedResponse
    {
        $componentNames = collect($classes)->flatMap(fn (SchoolClass $class) => $class->gradeComponents)
            ->pluck('name')->unique()->values();
        $rows = [['Kelas', 'Siswa', ...$componentNames->all(), 'Nilai Akhir']];

        foreach ($classes as $class) {
            foreach ($class->members as $member) {
                $scores = $class->gradeComponents->mapWithKeys(fn ($component) => [
                    $component->name => $component->scores->firstWhere('student_id', $member->student_id)?->score,
                ]);
                $final = $class->finalGrades->firstWhere('student_id', $member->student_id)?->final_score;
                $rows[] = [$class->name, $member->student->name, ...$componentNames->map(fn ($name) => $scores[$name] ?? null)->all(), $final];
            }
        }

        $sheet = (new Spreadsheet)->getActiveSheet();
        $sheet->fromArray($rows);
        $writer = new Xlsx($sheet->getParent());

        return response()->streamDownload(fn () => $writer->save('php://output'), $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
