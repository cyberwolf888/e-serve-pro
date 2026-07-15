<?php

// DATA-05 / DATA-06 / FR-GR-06 / FR-GR-08 / FR-SW-04 / BR-05 / M4

namespace Tests\Feature\Meetings;

use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingManagementTest extends TestCase
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

    public function test_guru_creates_meeting_for_owned_class(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.meetings.store', $class), [
            'title' => 'Pertemuan 1',
            'scheduled_at' => '2026-08-01 09:00',
        ])->assertRedirect(route('guru.classes.meetings.index', $class));

        $this->assertDatabaseHas('meetings', ['class_id' => $class->id, 'title' => 'Pertemuan 1']);
    }

    public function test_other_guru_cannot_manage_meeting(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($owner);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($otherGuru)->put(route('guru.classes.meetings.update', [$class, $meeting]), [
            'title' => 'Tidak Boleh',
            'scheduled_at' => now(),
        ])->assertForbidden();
    }

    public function test_inactive_guru_class_blocks_meeting_writes(): void
    {
        $guru = $this->user('guru', false);
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)->post(route('guru.classes.meetings.store', $class), [
            'title' => 'Tidak Boleh',
            'scheduled_at' => now(),
        ])->assertForbidden();
    }

    public function test_guru_shares_and_unshares_materials_on_a_meeting(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);
        $materialOne = Material::create(['class_id' => $class->id, 'title' => 'M1', 'type' => 'figma', 'figma_url' => 'https://figma.com/1']);
        $materialTwo = Material::create(['class_id' => $class->id, 'title' => 'M2', 'type' => 'figma', 'figma_url' => 'https://figma.com/2']);

        $this->actingAs($guru)->post(route('guru.classes.meetings.share', [$class, $meeting]), [
            'material_ids' => [$materialOne->id, $materialTwo->id],
        ])->assertRedirect(route('guru.classes.meetings.show', [$class, $meeting]));

        $this->assertDatabaseHas('meeting_materials', ['meeting_id' => $meeting->id, 'material_id' => $materialOne->id]);
        $this->assertDatabaseHas('meeting_materials', ['meeting_id' => $meeting->id, 'material_id' => $materialTwo->id]);

        // re-submit with only one material — the other is unshared
        $this->actingAs($guru)->post(route('guru.classes.meetings.share', [$class, $meeting]), [
            'material_ids' => [$materialOne->id],
        ])->assertRedirect(route('guru.classes.meetings.show', [$class, $meeting]));

        $this->assertDatabaseHas('meeting_materials', ['meeting_id' => $meeting->id, 'material_id' => $materialOne->id]);
        $this->assertDatabaseMissing('meeting_materials', ['meeting_id' => $meeting->id, 'material_id' => $materialTwo->id]);
    }

    public function test_siswa_only_sees_materials_shared_to_a_meeting_in_joined_classes(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);

        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'Pertemuan Bagikan', 'scheduled_at' => now()]);
        $shared = Material::create(['class_id' => $class->id, 'title' => 'Materi Dibagikan', 'type' => 'figma', 'figma_url' => 'https://figma.com/shared']);
        $unshared = Material::create(['class_id' => $class->id, 'title' => 'Materi Belum Dibagikan', 'type' => 'figma', 'figma_url' => 'https://figma.com/unshared']);
        $meeting->materials()->attach($shared->id);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertSee('Materi Dibagikan')
            ->assertDontSee('Materi Belum Dibagikan');
    }
}
