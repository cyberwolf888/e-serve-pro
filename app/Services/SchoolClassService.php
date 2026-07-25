<?php

// FR-GR-02 / FR-GR-03 / FR-SW-03 / BR-01 / BR-07 / M3

namespace App\Services;

use App\Models\ClassMember;
use App\Models\SchoolClass;
use App\Models\User;
use App\Notifications\AddedToClass;
use App\Repositories\SchoolClassRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolClassService
{
    public function __construct(private SchoolClassRepository $repo) {}

    public function create(array $data): SchoolClass
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                return $this->repo->create($data + ['class_code' => Str::upper(Str::random(8))]);
            } catch (QueryException $exception) {
                if ($attempt === 2 || ! str_contains($exception->getMessage(), 'class_code')) {
                    throw $exception;
                }
            }
        }

        throw new \LogicException('Class code generation failed.');
    }

    public function update(SchoolClass $class, array $data): SchoolClass
    {
        return $this->repo->update($class, $data);
    }

    public function deactivate(SchoolClass $class): SchoolClass
    {
        return $this->repo->deactivate($class);
    }

    public function activate(SchoolClass $class): SchoolClass
    {
        return $this->repo->activate($class);
    }

    public function addStudent(SchoolClass $class, User $student, string $errorField = 'email', string $reason = AddedToClass::REASON_ADDED): ClassMember
    {
        return DB::transaction(function () use ($class, $student, $errorField, $reason) {
            if (ClassMember::where('class_id', $class->id)->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages([$errorField => 'Siswa sudah tergabung di kelas ini.']);
            }

            $member = $this->repo->addMember($class, $student);

            $student->notify(new AddedToClass($class, $reason));

            return $member;
        });
    }
}
