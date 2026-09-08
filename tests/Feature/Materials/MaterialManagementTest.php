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
            'description' => 'Panduan visual layanan.',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/abc',
            'is_published' => '1',
        ])->assertRedirect(route('guru.classes.materials.index', $class));

        $this->assertDatabaseHas('materials', [
            'class_id' => $class->id,
            'description' => 'Panduan visual layanan.',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/abc',
            'is_published' => true,
        ]);
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
            'figma_url' => '',
            'file' => $file,
            'is_published' => '0',
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

        $this->followingRedirects()->actingAs($guru)->from(route('guru.classes.materials.create', $class))->post(route('guru.classes.materials.store', $class), [
            'title' => 'Modul Salah Format',
            'type' => 'file',
            'file' => $file,
            'is_published' => '0',
        ])->assertSee('Periksa kembali data yang diisi.')->assertSee('The file field must be a file of type: pdf.');

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
            'is_published' => '0',
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
            'is_published' => '0',
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
            'is_published' => '0',
        ])->assertForbidden();
    }

    public function test_enrolled_student_sees_only_published_materials_in_inactive_class(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['is_active' => false]);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        Material::create([
            'class_id' => $class->id,
            'title' => 'Materi Terbit',
            'description' => '<b>Deskripsi aman</b>',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/published',
            'is_published' => true,
        ]);
        Material::create([
            'class_id' => $class->id,
            'title' => 'Materi Draf',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/draft',
        ]);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertSee('Materi Terbit')
            ->assertSee('&lt;b&gt;Deskripsi aman&lt;/b&gt;', false)
            ->assertSee('https://figma.com/published', false)
            ->assertDontSee('Materi Draf');
    }

    public function test_published_download_allowed_for_member_but_draft_and_outsider_forbidden(): void
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
            'is_published' => '1',
        ]);
        $material = Material::firstOrFail();
        $draft = Material::create([
            'class_id' => $class->id,
            'title' => 'Modul Draf',
            'type' => 'file',
            'file_path' => $material->file_path,
            'file_size_kb' => 100,
        ]);

        $this->actingAs($member)->get(route('materials.download', $material))->assertOk();
        $this->actingAs($member)->get(route('materials.download', $draft))->assertForbidden();
        $this->actingAs($outsider)->get(route('materials.download', $material))->assertForbidden();
    }

    public function test_guru_updates_description_and_publication_and_still_sees_drafts(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = Material::create([
            'class_id' => $class->id,
            'title' => 'Draf Lama',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/old',
        ]);

        $this->actingAs($guru)->get(route('guru.classes.materials.index', $class))
            ->assertOk()
            ->assertSee('Draf Lama')
            ->assertSee('Draf');

        $this->actingAs($guru)->put(route('guru.classes.materials.update', [$class, $material]), [
            'title' => 'Materi Baru',
            'description' => 'Deskripsi baru',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/new',
            'is_published' => '1',
        ])->assertRedirect(route('guru.classes.materials.index', $class));

        $material->refresh();
        $this->assertSame('Deskripsi baru', $material->description);
        $this->assertTrue($material->is_published);
    }

    public function test_invalid_publication_value_is_rejected_and_new_model_defaults_to_draft(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.materials.store', $class), [
            'title' => 'Status Salah',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/invalid',
            'is_published' => 'yes',
        ])->assertSessionHasErrors('is_published');

        $material = Material::create([
            'class_id' => $class->id,
            'title' => 'Draf Bawaan',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/default',
        ]);

        $this->assertFalse($material->is_published);
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
