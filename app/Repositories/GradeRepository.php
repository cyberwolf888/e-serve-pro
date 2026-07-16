<?php

// DATA-13 / DATA-14 / DATA-15 / FR-GR-10 / M6

namespace App\Repositories;

use App\Models\ComponentScore;
use App\Models\FinalGrade;
use App\Models\GradeComponent;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository
{
    public function components(SchoolClass $class): Collection
    {
        return $class->gradeComponents()->with('quiz')->orderBy('id')->get();
    }

    public function createComponent(array $data): GradeComponent
    {
        return GradeComponent::create($data);
    }

    public function updateComponent(GradeComponent $component, array $data): GradeComponent
    {
        $component->update($data);

        return $component;
    }

    public function deleteComponent(GradeComponent $component): void
    {
        $component->delete();
    }

    public function quizAttempts(Quiz $quiz): Collection
    {
        return $quiz->attempts()->with('student')->whereNotNull('submitted_at')->get();
    }

    public function syncQuizScore(GradeComponent $component, User $student, float $score): void
    {
        $component->scores()->updateOrCreate(
            ['student_id' => $student->id],
            ['score' => $score, 'is_manual_override' => false],
        );
    }

    public function score(GradeComponent $component, User $student): ?ComponentScore
    {
        return $component->scores()->where('student_id', $student->id)->first();
    }

    public function scoreOverride(GradeComponent $component, User $student, float $score): void
    {
        $component->scores()->updateOrCreate(
            ['student_id' => $student->id],
            ['score' => $score, 'is_manual_override' => true],
        );
    }

    public function deleteAutomaticScores(GradeComponent $component): void
    {
        $component->scores()->where('is_manual_override', false)->delete();
    }

    public function members(SchoolClass $class): Collection
    {
        return $class->members()->with('student')->orderBy('joined_at')->get();
    }

    public function recap(SchoolClass $class): SchoolClass
    {
        return $class->load([
            'guru',
            'gradeComponents.quiz',
            'gradeComponents.scores',
            'members.student',
            'finalGrades',
        ]);
    }

    public function allRecap(): Collection
    {
        return SchoolClass::with([
            'guru',
            'gradeComponents.quiz',
            'gradeComponents.scores',
            'members.student',
            'finalGrades',
        ])->orderBy('name')->get();
    }

    public function upsertFinal(SchoolClass $class, User $student, float $score): void
    {
        FinalGrade::updateOrCreate(
            ['class_id' => $class->id, 'student_id' => $student->id],
            ['final_score' => $score, 'calculated_at' => now()],
        );
    }

    public function gradesForStudent(User $student): Collection
    {
        return $student->finalGrades()->with('schoolClass.guru')->latest('calculated_at')->get();
    }
}
