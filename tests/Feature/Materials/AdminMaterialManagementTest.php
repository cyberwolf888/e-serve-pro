<?php

// FR-SA-03 / FR-GR-04 / FR-GR-05 / BR-04 / §3.2 / M4

namespace Tests\Feature\Materials;

use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMaterialManagementTest extends TestCase
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

    public function test_admin_creates_material_via_figma_link(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($admin)->post(route('admin.classes.materials.store', $class), [
            'title' => 'Desain UI Admin',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/admin',
        ])->assertRedirect(route('admin.classes.materials.index', $class));

        $this->assertDatabaseHas('materials', ['class_id' => $class->id, 'type' => 'figma', 'figma_url' => 'https://figma.com/file/admin']);
    }

    public function test_admin_uploads_valid_pdf_material(): void
    {
        Storage::fake('local');
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $file = UploadedFile::fake()->create('admin-materi.pdf', 1024, 'application/pdf');

        $this->actingAs($admin)->post(route('admin.classes.materials.store', $class), [
            'title' => 'Admin Modul',
            'type' => 'file',
            'figma_url' => '',
            'file' => $file,
        ])->assertRedirect(route('admin.classes.materials.index', $class));

        $material = Material::firstOrFail();
        $this->assertSame('file', $material->type);
        $this->assertSame(1024, $material->file_size_kb);
        Storage::disk('local')->assertExists($material->file_path);
    }

    public function test_admin_edit_show_rejects_non_pdf(): void
    {
        Storage::fake('local');
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = Material::create(['class_id' => $class->id, 'title' => 'Lama', 'type' => 'figma', 'figma_url' => 'https://figma.com/lama']);
        $file = UploadedFile::fake()->create('admin-materi.docx', 100, 'application/msword');

        $this->followingRedirects()->actingAs($admin)->from(route('admin.classes.materials.edit', [$class, $material]))->put(route('admin.classes.materials.update', [$class, $material]), [
            'title' => 'PDF Gagal',
            'type' => 'file',
            'file' => $file,
        ])->assertSee('Periksa kembali data yang diisi.');
    }
}
