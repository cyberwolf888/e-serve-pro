<?php

// FR-GR-10 / FR-GR-11 / FR-GR-12 / BR-03 / M6

namespace App\Services;

use App\Models\GradeComponent;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Repositories\GradeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeService
{
    public function __construct(private GradeRepository $repo) {}

    public function createComponent(SchoolClass $class, array $data): GradeComponent
    {
        return DB::transaction(function () use ($class, $data) {
            $component = $this->repo->createComponent($data + ['class_id' => $class->id]);
            $this->backfillQuizScores($component);

            return $component;
        });
    }

    public function updateComponent(GradeComponent $component, array $data): GradeComponent
    {
        return DB::transaction(function () use ($component, $data) {
            $quizChanged = $component->quiz_id !== ($data['quiz_id'] ?? null);
            $component = $this->repo->updateComponent($component, $data);

            if ($quizChanged) {
                $this->repo->deleteAutomaticScores($component);
                $this->backfillQuizScores($component);
            }

            return $component;
        });
    }

    public function deleteComponent(GradeComponent $component): void
    {
        $this->repo->deleteComponent($component);
    }

    public function recordScores(GradeComponent $component, array $scores): void
    {
        $members = $this->repo->members($component->schoolClass)->keyBy('student_id');

        DB::transaction(function () use ($component, $scores, $members) {
            foreach ($scores as $studentId => $score) {
                if ($score === null || $score === '') {
                    continue;
                }

                $member = $members->get($studentId);
                if (! $member) {
                    throw ValidationException::withMessages(['scores' => 'Siswa tidak terdaftar di kelas ini.']);
                }

                $this->repo->scoreOverride($component, $member->student, (float) $score);
            }
        });
    }

    public function syncQuizAttempt(QuizAttempt $attempt): void
    {
        $component = GradeComponent::where('quiz_id', $attempt->quiz_id)->first();

        if (! $component || $attempt->score === null) {
            return;
        }

        $existing = $this->repo->score($component, $attempt->student);
        if (! $existing || ! $existing->is_manual_override) {
            $this->repo->syncQuizScore($component, $attempt->student, (float) $attempt->score);
        }
    }

    public function calculate(SchoolClass $class): void
    {
        $components = $this->repo->components($class)->load('scores');
        $weightTotal = $components->sum(fn (GradeComponent $component) => (float) $component->weight);

        if ($weightTotal <= 0) {
            throw ValidationException::withMessages(['grades' => 'Total bobot harus lebih dari 0.']);
        }

        DB::transaction(function () use ($class, $components, $weightTotal) {
            foreach ($this->repo->members($class) as $member) {
                if (! $member->student->is_active) {
                    continue;
                }

                $total = $components->sum(function (GradeComponent $component) use ($member): float {
                    return ((float) ($component->scores->firstWhere('student_id', $member->student_id)?->score ?? 0))
                        * (float) $component->weight;
                });

                $this->repo->upsertFinal($class, $member->student, round($total / $weightTotal, 2, PHP_ROUND_HALF_UP));
            }
        });
    }

    private function backfillQuizScores(GradeComponent $component): void
    {
        if (! $component->quiz_id) {
            return;
        }

        foreach ($this->repo->quizAttempts($component->quiz()->firstOrFail()) as $attempt) {
            $existing = $this->repo->score($component, $attempt->student);
            if (! $existing || ! $existing->is_manual_override) {
                $this->repo->syncQuizScore($component, $attempt->student, (float) $attempt->score);
            }
        }
    }
}
