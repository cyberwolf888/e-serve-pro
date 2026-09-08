<?php

// FR-SA-08 / FR-GR-15 / BR-09 / DATA-25..27 / §11 / M7.9

namespace Tests\Feature\Lkms;

use App\Models\ClassMember;
use App\Models\Lkm;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\LkmService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LkmManagementTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_guru_and_super_admin_manage_scoped_lkms_but_foreign_guru_cannot(): void
    {
        $guru = $this->user('guru');
        $otherGuru = $this->user('guru');
        $admin = $this->user('super_admin');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->get(route('guru.classes.lkms.index', $class))->assertOk();
        $this->actingAs($otherGuru)->get(route('guru.classes.lkms.index', $class))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.classes.lkms.index', $class))->assertOk();

        $this->actingAs($admin)->post(route('admin.classes.lkms.store', $class), $this->payload())
            ->assertRedirect();
        $this->assertSame($admin->id, Lkm::latest('id')->value('created_by'));
    }

    public function test_creation_validates_nested_roles_and_duplicate_names(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.lkms.store', $class), [
            'title' => 'LKM 1',
            'description' => 'Deskripsi',
            'roles' => [],
        ])->assertSessionHasErrors('roles');

        $payload = $this->payload();
        $payload['roles'][1]['name'] = 'ketua';
        $this->actingAs($guru)->post(route('guru.classes.lkms.store', $class), $payload)
            ->assertSessionHasErrors('roles.1.name');
    }

    public function test_creation_assigns_existing_members_evenly_and_allows_empty_class(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        foreach (range(1, 5) as $index) {
            $this->join($class, $this->user('siswa'));
        }
        $inactiveStudent = $this->user('siswa');
        $inactiveStudent->update(['is_active' => false]);
        $this->join($class, $inactiveStudent);

        $this->actingAs($guru)->post(route('guru.classes.lkms.store', $class), $this->payload())->assertRedirect();
        $lkm = Lkm::firstOrFail();
        $counts = $lkm->roles()->withCount('assignments')->pluck('assignments_count');
        $this->assertSame(5, $lkm->assignments()->count());
        $this->assertFalse($lkm->assignments()->where('student_id', $inactiveStudent->id)->exists());
        $this->assertLessThanOrEqual(1, $counts->max() - $counts->min());

        $emptyClass = $this->schoolClass($guru, ['class_code' => 'EMPTY001']);
        $this->actingAs($guru)->post(route('guru.classes.lkms.store', $emptyClass), $this->payload())->assertRedirect();
        $this->assertSame(0, Lkm::latest('id')->firstOrFail()->assignments()->count());
    }

    public function test_late_member_can_be_assigned_and_safely_reassigned(): void
    {
        [$guru, $class, $lkm] = $this->lkmFixture();
        $student = $this->user('siswa');
        $this->join($class, $student);
        [$firstRole, $secondRole] = $lkm->roles()->get();

        $this->actingAs($guru)->post(route('guru.classes.lkms.assignments.store', [$class, $lkm]), [
            'student_id' => $student->id,
            'lkm_role_id' => $firstRole->id,
        ])->assertRedirect();

        $assignment = $lkm->assignments()->where('student_id', $student->id)->firstOrFail();
        $this->actingAs($guru)->patch(route('guru.classes.lkms.assignments.update', [$class, $lkm, $assignment]), [
            'lkm_role_id' => $secondRole->id,
        ])->assertRedirect();
        $this->assertSame($secondRole->id, $assignment->fresh()->lkm_role_id);

        $inactiveStudent = $this->user('siswa');
        $inactiveStudent->update(['is_active' => false]);
        $this->join($class, $inactiveStudent);
        $this->actingAs($guru)->post(route('guru.classes.lkms.assignments.store', [$class, $lkm]), [
            'student_id' => $inactiveStudent->id,
            'lkm_role_id' => $firstRole->id,
        ])->assertSessionHasErrors('student_id');
    }

    public function test_role_deletion_requires_reassignment_and_structure_locks_after_first_proof(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $lkm = app(LkmService::class)->create($class, $guru, $this->payload());
        [$assignedRole, $otherRole] = $lkm->roles()->get();
        $assignment = $lkm->assignments()->firstOrFail();
        if ($assignment->lkm_role_id !== $assignedRole->id) {
            [$assignedRole, $otherRole] = [$otherRole, $assignedRole];
        }

        $this->actingAs($guru)->post(route('guru.classes.lkms.roles.store', [$class, $lkm]), [
            'name' => 'Dokumentasi',
            'description' => 'Mendokumentasikan hasil.',
            'instructions' => 'Simpan dokumentasi.',
            'sop_items' => ['Ambil foto'],
        ])->assertRedirect();
        $newRole = $lkm->roles()->where('name', 'Dokumentasi')->firstOrFail();
        $this->actingAs($guru)->put(route('guru.classes.lkms.roles.update', [$class, $lkm, $newRole]), [
            'name' => 'Dokumentator',
            'description' => 'Mendokumentasikan hasil.',
            'instructions' => 'Simpan dokumentasi.',
            'sop_items' => ['Ambil foto', 'Unggah foto'],
        ])->assertRedirect();
        $this->assertSame('Dokumentator', $newRole->fresh()->name);
        $this->actingAs($guru)->delete(route('guru.classes.lkms.roles.destroy', [$class, $lkm, $newRole]))->assertRedirect();

        $this->actingAs($guru)->post(route('guru.classes.lkms.roles.store', [$class, $lkm]), [
            'name' => strtolower($assignedRole->name),
            'description' => 'Duplikat.',
            'instructions' => 'Duplikat.',
            'sop_items' => ['Duplikat'],
        ])->assertSessionHasErrors('name');

        $this->actingAs($guru)->delete(route('guru.classes.lkms.roles.destroy', [$class, $lkm, $assignedRole]))
            ->assertSessionHasErrors('role');
        $this->actingAs($guru)->patch(route('guru.classes.lkms.assignments.update', [$class, $lkm, $assignment]), [
            'lkm_role_id' => $otherRole->id,
        ])->assertRedirect();
        $this->actingAs($guru)->delete(route('guru.classes.lkms.roles.destroy', [$class, $lkm, $assignedRole]))
            ->assertRedirect();
        $this->assertModelMissing($assignedRole);

        $assignment->update(['proof_url' => 'https://youtu.be/test', 'proof_submitted_at' => now()]);
        $this->actingAs($guru)->put(route('guru.classes.lkms.roles.update', [$class, $lkm, $otherRole]), [
            'name' => 'Nama Baru',
            'description' => 'Deskripsi',
            'instructions' => 'Instruksi',
            'sop_items' => ['Langkah'],
        ])->assertForbidden();

        $this->actingAs($guru)->put(route('guru.classes.lkms.update', [$class, $lkm]), [
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru',
            'is_published' => true,
        ])->assertRedirect();
        $this->assertTrue($lkm->fresh()->is_published);
    }

    public function test_submitted_assignment_cannot_be_reassigned_and_correction_preserves_timestamps(): void
    {
        [$guru, $class, $lkm, $student] = $this->lkmFixture(true);
        $lkm->update(['is_published' => true]);
        $assignment = $lkm->assignments()->where('student_id', $student->id)->firstOrFail();
        $originalProofTime = now()->subHour();
        $originalReflectionTime = now()->subMinutes(30);
        $assignment->update([
            'proof_url' => 'https://youtu.be/old',
            'proof_submitted_at' => $originalProofTime,
            'sop_checks' => [0],
            'reflection_submitted_at' => $originalReflectionTime,
        ]);
        $otherRole = $lkm->roles()->whereKeyNot($assignment->lkm_role_id)->firstOrFail();

        $this->actingAs($guru)->patch(route('guru.classes.lkms.assignments.update', [$class, $lkm, $assignment]), [
            'lkm_role_id' => $otherRole->id,
        ])->assertForbidden();

        $this->actingAs($guru)->put(route('guru.classes.lkms.submissions.update', [$class, $lkm, $assignment]), [
            'proof_url' => 'https://example.com/not-allowed',
            'sop_checks' => [0],
        ])->assertSessionHasErrors('proof_url');
        $this->assertSame('https://youtu.be/old', $assignment->fresh()->proof_url);

        $this->actingAs($guru)->put(route('guru.classes.lkms.submissions.update', [$class, $lkm, $assignment]), [
            'proof_url' => 'https://drive.google.com/new',
            'sop_checks' => [],
        ])->assertRedirect();
        $assignment->refresh();
        $this->assertSame('https://drive.google.com/new', $assignment->proof_url);
        $this->assertSame($originalProofTime->toDateTimeString(), $assignment->proof_submitted_at->toDateTimeString());
        $this->assertSame($originalReflectionTime->toDateTimeString(), $assignment->reflection_submitted_at->toDateTimeString());
        $this->assertSame([], $assignment->sop_checks);

        $this->actingAs($guru)->put(route('guru.classes.lkms.update', [$class, $lkm]), [
            'title' => $lkm->title,
            'description' => $lkm->description,
            'is_published' => false,
        ])->assertRedirect();
        $this->assertFalse($lkm->fresh()->is_published);
        $this->assertModelExists($assignment->fresh());
    }

    public function test_nested_resource_tampering_returns_not_found(): void
    {
        $guru = $this->user('guru');
        $firstClass = $this->schoolClass($guru);
        $secondClass = $this->schoolClass($guru, ['class_code' => 'OTHER001']);
        $student = $this->user('siswa');
        $this->join($firstClass, $student);
        $lkm = app(LkmService::class)->create($firstClass, $guru, $this->payload());
        $otherLkm = app(LkmService::class)->create($firstClass, $guru, $this->payload());
        $foreignAssignment = $otherLkm->assignments()->firstOrFail();

        $this->actingAs($guru)->get(route('guru.classes.lkms.show', [$secondClass, $lkm]))->assertNotFound();
        $this->actingAs($guru)->patch(route('guru.classes.lkms.assignments.update', [$firstClass, $lkm, $foreignAssignment]), [
            'lkm_role_id' => $lkm->roles()->value('id'),
        ])->assertNotFound();
    }

    private function user(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru, array $data = []): SchoolClass
    {
        return SchoolClass::create($data + [
            'guru_id' => $guru->id,
            'name' => 'Kelas LKM',
            'class_code' => 'LKM00001',
            'is_active' => true,
        ]);
    }

    private function join(SchoolClass $class, User $student): void
    {
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
    }

    private function payload(): array
    {
        return [
            'title' => 'LKM Kolaborasi',
            'description' => 'Kerjakan bersama kelompok.',
            'roles' => [
                ['name' => 'Ketua', 'description' => 'Memimpin tim.', 'instructions' => 'Atur pembagian.', 'sop_items' => ['Bagi tugas', 'Periksa hasil']],
                ['name' => 'Penyaji', 'description' => 'Menyajikan hasil.', 'instructions' => 'Siapkan presentasi.', 'sop_items' => ['Buat slide', 'Presentasikan']],
            ],
        ];
    }

    private function lkmFixture(bool $withStudent = false): array
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $student = null;
        if ($withStudent) {
            $student = $this->user('siswa');
            $this->join($class, $student);
        }
        $lkm = app(LkmService::class)->create($class, $guru, $this->payload());

        return [$guru, $class, $lkm, $student];
    }
}
