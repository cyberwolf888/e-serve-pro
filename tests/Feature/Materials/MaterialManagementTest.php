<?php

// DATA-04 / FR-GR-04 / FR-GR-05 / FR-SW-04 / BR-04 / BR-05 / M4

namespace Tests\Feature\Materials;

use App\Models\ClassMember;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialManagementTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    private function user(string $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru, array $data = []): SchoolClass
    {
        return SchoolClass::create($data + [
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => 'KELAS001',
            'is_active' => true,
        ]);
    }

    public function test_guru_creates_material_via_figma_link(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Desain UI',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/abc',
        ])->assertRedirect(route('guru.classes.materials.index', $class));

        $this->assertDatabaseHas('materials', ['class_id' => $class->id, 'type' => 'figma', 'figma_url' => 'https://figma.com/file/abc']);
    }

    public function test_guru_uploads_valid_pdf_material(): void
    {
        Storage::fake('local');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $file = UploadedFile::fake()->create('materi.pdf', 2048, 'application/pdf');

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Modul Bab 1',
            'type' => 'file',
            'file' => $file,
        ])->assertRedirect(route('guru.classes.materials.index', $class));

        $material = Material::firstOrFail();
        $this->assertSame('file', $material->type);
        $this->assertSame(2048, $material->file_size_kb);
        Storage::disk('local')->assertExists($material->file_path);
    }

    public function test_non_pdf_upload_is_rejected(): void
    {
        Storage::fake('local');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $file = UploadedFile::fake()->create('materi.docx', 100, 'application/msword');

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Modul Salah Format',
            'type' => 'file',
            'file' => $file,
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('materials', 0);
    }

    public function test_oversized_pdf_upload_is_rejected(): void
    {
        Storage::fake('local');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $file = UploadedFile::fake()->create('materi.pdf', 20481, 'application/pdf');

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Modul Kebesaran',
            'type' => 'file',
            'file' => $file,
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('materials', 0);
    }

    public function test_other_guru_cannot_manage_materials_of_another_class(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($owner);

        $this->actingAs($otherGuru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Tidak Boleh',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/xyz',
        ])->assertForbidden();
    }

    public function test_inactive_guru_class_blocks_material_writes(): void
    {
        $guru = $this->user('guru', false);
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Tidak Boleh',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/xyz',
        ])->assertForbidden();
    }

    public function test_download_allowed_for_class_member_and_forbidden_for_others(): void
    {
        Storage::fake('local');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $member = $this->user('siswa');
        $outsider = $this->user('siswa');
        ClassMember::create(['class_id' => $class->id, 'student_id' => $member->id, 'joined_at' => now()]);

        $file = UploadedFile::fake()->create('materi.pdf', 100, 'application/pdf');
        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Modul Unduh',
            'type' => 'file',
            'file' => $file,
        ]);
        $material = Material::firstOrFail();

        $this->actingAs($member)->get(route('materials.download', $material))->assertOk();
        $this->actingAs($outsider)->get(route('materials.download', $material))->assertForbidden();
    }

    public function test_download_redirects_guest_to_login(): void
    {
        Storage::fake('local');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = Material::create([
            'class_id' => $class->id,
            'title' => 'Modul Unduh',
            'type' => 'file',
            'file_path' => 'materials/1/fake.pdf',
            'file_size_kb' => 100,
        ]);

        $this->get(route('materials.download', $material))->assertRedirect(route('auth.login.show'));
    }
}
