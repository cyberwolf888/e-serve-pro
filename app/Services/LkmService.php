<?php

// FR-GR-11 / FR-GR-15 / FR-SW-08 / BR-09 / DATA-25..27 / M7.9

namespace App\Services;

use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\LkmRole;
use App\Models\SchoolClass;
use App\Models\User;
use App\Repositories\LkmRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LkmService
{
    public function __construct(private LkmRepository $repo, private GradeService $gradeService) {}

    public function create(SchoolClass $class, User $creator, array $data): Lkm
    {
        return DB::transaction(function () use ($class, $creator, $data) {
            $rolesData = $data['roles'];
            unset($data['roles']);

            $lkm = $this->repo->create($class, $creator, $data + ['is_published' => false]);
            $roles = collect($rolesData)->map(fn (array $role) => $this->repo->createRole($lkm, $role))->values();

            $this->repo->shuffledStudents($class)->values()->each(
                fn (User $student, int $index) => $this->repo->createAssignment($lkm, $roles[$index % $roles->count()], $student)
            );

            return $lkm;
        });
    }

    public function update(Lkm $lkm, array $data): Lkm
    {
        return $this->repo->update($lkm, $data);
    }

    public function createRole(Lkm $lkm, array $data): LkmRole
    {
        $this->ensureStructureUnlocked($lkm);

        return $this->repo->createRole($lkm, $data);
    }

    public function updateRole(Lkm $lkm, LkmRole $role, array $data): LkmRole
    {
        $this->ensureSameLkm($lkm, $role->lkm_id);
        $this->ensureStructureUnlocked($lkm);

        return $this->repo->updateRole($role, $data);
    }

    public function deleteRole(Lkm $lkm, LkmRole $role): void
    {
        $this->ensureSameLkm($lkm, $role->lkm_id);
        $this->ensureStructureUnlocked($lkm);

        if ($lkm->roles()->count() === 1) {
            throw ValidationException::withMessages(['role' => 'LKM harus memiliki minimal satu peran.']);
        }

        if ($role->assignments()->exists()) {
            throw ValidationException::withMessages(['role' => 'Alihkan semua mahasiswa dari peran ini sebelum menghapusnya.']);
        }

        $this->repo->deleteRole($role);
    }

    public function assignStudent(Lkm $lkm, int $roleId, int $studentId): LkmAssignment
    {
        $role = $this->repo->roleForLkm($lkm, $roleId);
        $student = $this->repo->studentInClass($lkm->schoolClass, $studentId);

        try {
            return $this->repo->createAssignment($lkm, $role, $student);
        } catch (QueryException) {
            throw ValidationException::withMessages(['student_id' => 'Mahasiswa sudah memiliki peran pada LKM ini.']);
        }
    }

    public function reassign(Lkm $lkm, LkmAssignment $assignment, int $roleId): LkmAssignment
    {
        return DB::transaction(function () use ($lkm, $assignment, $roleId) {
            $assignment = $this->repo->lockAssignment($assignment);
            $role = $this->repo->roleForLkm($lkm, $roleId);
            $this->ensureSameLkm($lkm, $assignment->lkm_id);

            if ($assignment->proof_submitted_at !== null) {
                throw ValidationException::withMessages(['lkm_role_id' => 'Peran tidak dapat diubah setelah bukti dikirim.']);
            }

            $assignment->update(['lkm_role_id' => $role->id]);

            return $assignment;
        });
    }

    public function submitProof(Lkm $lkm, User $student, string $url): LkmAssignment
    {
        return DB::transaction(function () use ($lkm, $student, $url) {
            $assignment = $this->repo->assignmentForStudent($lkm, $student, true);

            if ($assignment->proof_submitted_at !== null) {
                throw ValidationException::withMessages(['proof_url' => 'Bukti hanya dapat dikirim satu kali.']);
            }

            $assignment->update(['proof_url' => $url, 'proof_submitted_at' => now()]);

            return $assignment;
        });
    }

    public function submitReflection(Lkm $lkm, User $student, array $checks): LkmAssignment
    {
        return DB::transaction(function () use ($lkm, $student, $checks) {
            $assignment = $this->repo->assignmentForStudent($lkm, $student, true);

            if ($assignment->proof_submitted_at === null) {
                throw ValidationException::withMessages(['sop_checks' => 'Kirim bukti sebelum refleksi.']);
            }

            if ($assignment->reflection_submitted_at !== null) {
                throw ValidationException::withMessages(['sop_checks' => 'Refleksi hanya dapat dikirim satu kali.']);
            }

            sort($checks);
            $assignment->update(['sop_checks' => array_values($checks), 'reflection_submitted_at' => now()]);

            return $assignment;
        });
    }

    public function correctSubmission(Lkm $lkm, LkmAssignment $assignment, array $data): LkmAssignment
    {
        return DB::transaction(function () use ($lkm, $assignment, $data) {
            $assignment = $this->repo->lockAssignment($assignment);
            $this->ensureSameLkm($lkm, $assignment->lkm_id);

            if ($assignment->proof_submitted_at === null) {
                throw ValidationException::withMessages(['proof_url' => 'Mahasiswa belum mengirim bukti.']);
            }

            $changes = [];
            if (isset($data['proof_url'])) {
                $changes['proof_url'] = $data['proof_url'];

                if ($assignment->reflection_submitted_at !== null) {
                    $checks = $data['sop_checks'];
                    sort($checks);
                    $changes['sop_checks'] = array_values($checks);
                }
            }

            if (isset($data['score'])) {
                if ($assignment->reflection_submitted_at === null) {
                    throw ValidationException::withMessages(['score' => 'Nilai hanya dapat diberikan setelah bukti dan refleksi selesai.']);
                }

                $changes['score'] = $data['score'];
            }
            $assignment->update($changes);
            $this->gradeService->syncLkmAssignment($assignment);

            return $assignment;
        });
    }

    private function ensureStructureUnlocked(Lkm $lkm): void
    {
        if ($lkm->assignments()->whereNotNull('proof_submitted_at')->exists()) {
            throw ValidationException::withMessages(['role' => 'Struktur peran dan SOP terkunci setelah bukti pertama dikirim.']);
        }
    }

    private function ensureSameLkm(Lkm $lkm, int $lkmId): void
    {
        abort_unless($lkm->id === $lkmId, 404);
    }
}
