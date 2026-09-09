<?php

// DATA-25..27 / FR-SA-08 / FR-GR-11 / FR-GR-15 / FR-SW-08 / M7.9

namespace App\Repositories;

use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\LkmRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LkmRepository
{
    public function forClass(SchoolClass $class): LengthAwarePaginator
    {
        return $class->lkms()->withCount(['roles', 'assignments'])->latest()->paginate(15);
    }

    public function create(SchoolClass $class, User $creator, array $data): Lkm
    {
        return $class->lkms()->create($data + ['created_by' => $creator->id]);
    }

    public function update(Lkm $lkm, array $data): Lkm
    {
        $lkm->update($data);

        return $lkm;
    }

    public function createRole(Lkm $lkm, array $data): LkmRole
    {
        return $lkm->roles()->create($data);
    }

    public function updateRole(LkmRole $role, array $data): LkmRole
    {
        $role->update($data);

        return $role;
    }

    public function deleteRole(LkmRole $role): void
    {
        $role->delete();
    }

    public function shuffledStudents(SchoolClass $class): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('classMemberships', fn ($query) => $query->where('class_id', $class->id))
            ->inRandomOrder()
            ->get();
    }

    public function createAssignment(Lkm $lkm, LkmRole $role, User $student): LkmAssignment
    {
        return $lkm->assignments()->create([
            'lkm_role_id' => $role->id,
            'student_id' => $student->id,
        ]);
    }

    public function roleForLkm(Lkm $lkm, int $roleId): LkmRole
    {
        return $lkm->roles()->findOrFail($roleId);
    }

    public function studentInClass(SchoolClass $class, int $studentId): User
    {
        return User::whereKey($studentId)
            ->where('is_active', true)
            ->whereHas('classMemberships', fn ($query) => $query->where('class_id', $class->id))
            ->firstOrFail();
    }

    public function paginatedMembers(SchoolClass $class): LengthAwarePaginator
    {
        return $class->members()->with('student')->oldest('joined_at')->paginate(25);
    }

    public function assignmentsForStudents(Lkm $lkm, array $studentIds): Collection
    {
        return $lkm->assignments()
            ->whereIn('student_id', $studentIds)
            ->with(['role', 'student'])
            ->get();
    }

    public function submittedAssignments(Lkm $lkm): LengthAwarePaginator
    {
        return $lkm->assignments()
            ->whereNotNull('proof_submitted_at')
            ->with(['student', 'role', 'lkm.schoolClass.guru'])
            ->latest('proof_submitted_at')
            ->paginate(25);
    }

    public function studentAssignments(SchoolClass $class, User $student): Collection
    {
        return LkmAssignment::query()
            ->where('student_id', $student->id)
            ->whereHas('lkm', fn ($query) => $query->where('class_id', $class->id)->where('is_published', true))
            ->with(['lkm', 'role'])
            ->latest()
            ->get();
    }

    public function assignmentForStudent(Lkm $lkm, User $student, bool $lock = false): LkmAssignment
    {
        return $lkm->assignments()
            ->where('student_id', $student->id)
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->firstOrFail();
    }

    public function lockAssignment(LkmAssignment $assignment): LkmAssignment
    {
        return LkmAssignment::lockForUpdate()->findOrFail($assignment->id);
    }
}
