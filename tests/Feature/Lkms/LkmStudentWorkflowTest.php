<?php

// FR-SW-08 / BR-05 / BR-09 / DATA-27 / §11 / M7.9

namespace Tests\Feature\Lkms;

use App\Models\ClassMember;
use App\Models\Lkm;
use App\Models\LkmAssignment;
use App\Models\LkmRole;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LkmStudentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_class_page_shows_only_published_assigned_lkms(): void
    {
        [$student, $class, $published] = $this->fixture();
        $draft = $this->lkm($class, false, 'LKM Rahasia');
        $otherPublished = $this->lkm($class, true, 'LKM Bukan Tugas');
        $this->assign($draft, $student);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title)
            ->assertDontSee($otherPublished->title);
        $this->actingAs($student)->get(route('siswa.classes.lkms.show', [$class, $draft]))->assertNotFound();
        $this->actingAs($student)->get(route('siswa.classes.lkms.show', [$class, $published]))
            ->assertOk()
            ->assertDontSee('SOP');
    }

    #[DataProvider('validProofUrls')]
    public function test_student_can_submit_each_allowed_https_proof_host(string $url): void
    {
        [$student, $class, $lkm, $assignment] = $this->fixture();

        $this->actingAs($student)->post(route('siswa.classes.lkms.proof.store', [$class, $lkm]), ['proof_url' => $url])
            ->assertRedirect();
        $this->assertSame($url, $assignment->fresh()->proof_url);
    }

    public static function validProofUrls(): array
    {
        return [
            ['https://drive.google.com/file/1'],
            ['https://docs.google.com/document/1'],
            ['https://youtube.com/watch?v=1'],
            ['https://www.youtube.com/watch?v=1'],
            ['https://m.youtube.com/watch?v=1'],
            ['https://youtu.be/1'],
        ];
    }

    public function test_invalid_scheme_and_host_are_rejected(): void
    {
        [$student, $class, $lkm] = $this->fixture();

        foreach (['http://drive.google.com/file/1', 'https://evil.example/drive.google.com', 'https://youtube.com.evil.example/1', 'https://youtu.be/bad path'] as $url) {
            $this->actingAs($student)->post(route('siswa.classes.lkms.proof.store', [$class, $lkm]), ['proof_url' => $url])
                ->assertSessionHasErrors('proof_url');
        }
    }

    public function test_reflection_requires_proof_and_accepts_subset_or_no_checks(): void
    {
        [$student, $class, $lkm, $assignment] = $this->fixture();
        $reflectionRoute = route('siswa.classes.lkms.reflection.store', [$class, $lkm]);

        $this->actingAs($student)->post($reflectionRoute, ['sop_checks' => [0]])
            ->assertSessionHasErrors('sop_checks');

        $assignment->update(['proof_url' => 'https://youtu.be/1', 'proof_submitted_at' => now()]);
        $this->actingAs($student)->post($reflectionRoute, ['sop_checks' => [1]])
            ->assertRedirect();
        $this->assertSame([1], $assignment->fresh()->sop_checks);

        [$otherStudent, $otherClass, $otherLkm, $otherAssignment] = $this->fixture('LKM00002');
        $otherAssignment->update(['proof_url' => 'https://youtu.be/2', 'proof_submitted_at' => now()]);
        $this->actingAs($otherStudent)->post(route('siswa.classes.lkms.reflection.store', [$otherClass, $otherLkm]))
            ->assertRedirect();
        $this->assertSame([], $otherAssignment->fresh()->sop_checks);
    }

    public function test_invalid_sop_indexes_and_duplicate_submissions_are_rejected(): void
    {
        [$student, $class, $lkm, $assignment] = $this->fixture();
        $proofRoute = route('siswa.classes.lkms.proof.store', [$class, $lkm]);
        $reflectionRoute = route('siswa.classes.lkms.reflection.store', [$class, $lkm]);

        $this->actingAs($student)->post($proofRoute, ['proof_url' => 'https://youtu.be/1'])->assertRedirect();
        $this->actingAs($student)->post($proofRoute, ['proof_url' => 'https://youtu.be/2'])->assertSessionHasErrors('proof_url');
        $this->actingAs($student)->post($reflectionRoute, ['sop_checks' => [99]])->assertSessionHasErrors('sop_checks');
        $this->actingAs($student)->post($reflectionRoute, ['sop_checks' => [0]])->assertRedirect();
        $this->actingAs($student)->post($reflectionRoute, ['sop_checks' => []])->assertSessionHasErrors('sop_checks');
        $this->assertNotNull($assignment->fresh()->reflection_submitted_at);
    }

    public function test_inactive_student_class_or_owner_cannot_write(): void
    {
        [$student, $class, $lkm] = $this->fixture();
        $route = route('siswa.classes.lkms.proof.store', [$class, $lkm]);

        $student->update(['is_active' => false]);
        $this->actingAs($student)->post($route, ['proof_url' => 'https://youtu.be/1'])->assertForbidden();
        $student->update(['is_active' => true]);
        $class->update(['is_active' => false]);
        $this->actingAs($student)->post($route, ['proof_url' => 'https://youtu.be/1'])->assertForbidden();
        $class->update(['is_active' => true]);
        $class->guru->update(['is_active' => false]);
        $this->actingAs($student)->post($route, ['proof_url' => 'https://youtu.be/1'])->assertForbidden();
    }

    public function test_foreign_student_and_nested_class_tampering_return_not_found(): void
    {
        [$student, $class, $lkm] = $this->fixture();
        $foreign = $this->user('siswa');
        $otherClass = $this->schoolClass($class->guru, ['class_code' => 'OTHER001']);

        $this->actingAs($foreign)->get(route('siswa.classes.lkms.show', [$class, $lkm]))->assertNotFound();
        $this->actingAs($student)->get(route('siswa.classes.lkms.show', [$otherClass, $lkm]))->assertNotFound();
    }

    private function fixture(string $classCode = 'LKM00001'): array
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['class_code' => $classCode]);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $lkm = $this->lkm($class, true);
        $assignment = $this->assign($lkm, $student);

        return [$student, $class, $lkm, $assignment];
    }

    private function user(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru, array $data = []): SchoolClass
    {
        return SchoolClass::create($data + ['guru_id' => $guru->id, 'name' => 'Kelas LKM', 'class_code' => 'LKM00001', 'is_active' => true]);
    }

    private function lkm(SchoolClass $class, bool $published, string $title = 'LKM Publik'): Lkm
    {
        return Lkm::create(['class_id' => $class->id, 'created_by' => $class->guru_id, 'title' => $title, 'description' => 'Deskripsi', 'is_published' => $published]);
    }

    private function assign(Lkm $lkm, User $student): LkmAssignment
    {
        $role = LkmRole::create(['lkm_id' => $lkm->id, 'name' => 'Ketua', 'description' => 'Memimpin', 'instructions' => 'Kerjakan', 'sop_items' => ['Mulai', 'Selesai']]);

        return LkmAssignment::create(['lkm_id' => $lkm->id, 'lkm_role_id' => $role->id, 'student_id' => $student->id]);
    }
}
