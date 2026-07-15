<?php

// DATA-02 / DATA-03 / FR-GR-02 / FR-SW-04 / M3

namespace App\Repositories;

use App\Models\ClassMember;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SchoolClassRepository
{
    /** Paginated enrolled students for class detail page. FR-GR-02 / FR-SA-03 */
    public function paginatedMembers(SchoolClass $class, int $perPage = 15): LengthAwarePaginator
    {
        return $class->members()->with('student')->latest('joined_at')->paginate($perPage);
    }

    public function all(?string $status = null, string $sort = 'newest'): Collection
    {
        return SchoolClass::with('guru')
            ->withCount('members')
            ->when($status, fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->get();
    }

    public function forGuru(User $guru, ?string $status = null, string $sort = 'newest'): Collection
    {
        return SchoolClass::whereBelongsTo($guru, 'guru')
            ->withCount('members')
            ->when($status, fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->get();
    }

    public function forStudent(User $student): Collection
    {
        return SchoolClass::with('guru')
            ->whereHas('members', fn ($query) => $query->where('student_id', $student->id))
            ->latest()
            ->get();
    }

    public function create(array $data): SchoolClass
    {
        return SchoolClass::create($data);
    }

    public function findByCode(string $code): SchoolClass
    {
        return SchoolClass::where('class_code', $code)->firstOrFail();
    }

    public function update(SchoolClass $class, array $data): SchoolClass
    {
        $class->update($data);

        return $class;
    }

    public function deactivate(SchoolClass $class): SchoolClass
    {
        return $this->update($class, ['is_active' => false]);
    }

    public function activate(SchoolClass $class): SchoolClass
    {
        return $this->update($class, ['is_active' => true]);
    }

    public function addMember(SchoolClass $class, User $student): ClassMember
    {
        return ClassMember::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'joined_at' => now(),
        ]);
    }
}
