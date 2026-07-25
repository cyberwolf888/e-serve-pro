<?php

// DATA-02 / DATA-03 / FR-GR-02 / FR-GR-03 / FR-SW-03 / FR-SW-04 / BR-01 / BR-05 / BR-07 / M3

namespace Tests\Feature\Classes;

use App\Models\ClassMember;
use App\Models\SchoolClass;
use App\Models\User;
use App\Notifications\AddedToClass;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClassMembershipTest extends TestCase
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

    public function test_guru_can_create_class_with_unique_eight_character_code(): void
    {
        $guru = $this->user('guru');

        $this->actingAs($guru)->post(route('guru.classes.store'), [
            'name' => 'Kelas Baru',
            'description' => 'Deskripsi',
        ])->assertRedirect(route('guru.classes.index'));

        $class = SchoolClass::firstOrFail();
        $this->assertSame($guru->id, $class->guru_id);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $class->class_code);
    }

    public function test_guru_cannot_edit_another_gurus_class(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($owner);

        $this->actingAs($otherGuru)->put(route('guru.classes.update', $class), [
            'name' => 'Tidak Boleh',
        ])->assertForbidden();
    }

    public function test_guru_views_own_class_detail_with_members_and_is_forbidden_from_others(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($owner);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);

        $this->actingAs($owner)->get(route('guru.classes.show', $class))
            ->assertOk()
            ->assertSee($class->class_code)
            ->assertSee($student->name)
            ->assertSee($student->email);

        $this->actingAs($otherGuru)->get(route('guru.classes.show', $class))->assertForbidden();
    }

    public function test_admin_views_any_class_detail_read_only_when_inactive(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru, ['class_code' => 'DETAIL01', 'is_active' => false]);

        $this->actingAs($admin)->get(route('admin.classes.show', $class))
            ->assertOk()
            ->assertSee($class->name)
            ->assertSee($guru->name);
    }

    public function test_guru_adds_active_siswa_by_email_and_rejects_duplicates(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);

        Notification::fake();

        $this->actingAs($guru)->post(route('guru.classes.students.store', $class), ['email' => $student->email])
            ->assertRedirect(route('guru.classes.show', $class));

        $this->assertDatabaseHas('class_members', ['class_id' => $class->id, 'student_id' => $student->id]);
        Notification::assertSentTo($student, AddedToClass::class, fn ($n) => $n->reason === AddedToClass::REASON_ADDED);

        $this->actingAs($guru)->post(route('guru.classes.students.store', $class), ['email' => $student->email])
            ->assertSessionHasErrors('email');

        Notification::assertSentToTimes($student, AddedToClass::class, 1);
    }

    public function test_siswa_joins_by_code_immediately_without_approval_and_duplicate_is_rejected(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['class_code' => 'JOIN2026']);

        Notification::fake();

        $this->actingAs($student)->post(route('siswa.classes.join'), ['class_code' => 'join2026'])
            ->assertRedirect(route('siswa.classes.index'));

        $this->assertDatabaseHas('class_members', ['class_id' => $class->id, 'student_id' => $student->id]);
        Notification::assertSentTo($student, AddedToClass::class, fn ($n) => $n->reason === AddedToClass::REASON_JOINED);

        $this->actingAs($student)->post(route('siswa.classes.join'), ['class_code' => 'JOIN2026'])
            ->assertSessionHasErrors('class_code');

        Notification::assertSentToTimes($student, AddedToClass::class, 1);
    }

    public function test_siswa_sees_only_joined_classes_including_inactive_read_only_class(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $joined = $this->schoolClass($guru, ['name' => 'Kelas Diikuti', 'class_code' => 'JOINED01', 'is_active' => false]);
        $other = $this->schoolClass($guru, ['name' => 'Kelas Lain', 'class_code' => 'OTHER001']);
        ClassMember::create(['class_id' => $joined->id, 'student_id' => $student->id, 'joined_at' => now()]);

        $this->actingAs($student)->get(route('siswa.classes.index'))
            ->assertOk()
            ->assertSee($joined->name)
            ->assertDontSee($other->name);

        $this->actingAs($student)->get(route('siswa.classes.show', $joined))->assertOk();
    }

    public function test_inactive_class_cannot_be_joined_or_changed(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru, ['class_code' => 'INACTIVE', 'is_active' => false]);

        $this->actingAs($student)->post(route('siswa.classes.join'), ['class_code' => $class->class_code])
            ->assertSessionHasErrors('class_code');

        $this->actingAs($guru)->put(route('guru.classes.update', $class), ['name' => 'Tidak Boleh'])
            ->assertForbidden();
    }

    public function test_guru_can_reactivate_own_inactive_class_but_not_another_gurus(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $class = $this->schoolClass($owner, ['class_code' => 'REACT001', 'is_active' => false]);

        $this->actingAs($otherGuru)->patch(route('guru.classes.activate', $class))->assertForbidden();

        $this->actingAs($owner)->patch(route('guru.classes.activate', $class))
            ->assertRedirect(route('guru.classes.index'));

        $this->assertDatabaseHas('classes', ['id' => $class->id, 'is_active' => true]);
    }

    public function test_admin_can_reactivate_any_inactive_class(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru, ['class_code' => 'REACT002', 'is_active' => false]);

        $this->actingAs($admin)->patch(route('admin.classes.activate', $class))
            ->assertRedirect(route('admin.classes.index'));

        $this->assertDatabaseHas('classes', ['id' => $class->id, 'is_active' => true]);
    }

    public function test_admin_can_create_and_transfer_class_to_active_guru(): void
    {
        $admin = $this->user('super_admin');
        $firstGuru = $this->user('guru');
        $secondGuru = $this->user('guru');

        $this->actingAs($admin)->post(route('admin.classes.store'), [
            'guru_id' => $firstGuru->id,
            'name' => 'Kelas Admin',
        ])->assertRedirect(route('admin.classes.index'));

        $class = SchoolClass::firstOrFail();
        $this->actingAs($admin)->put(route('admin.classes.update', $class), [
            'guru_id' => $secondGuru->id,
            'name' => 'Kelas Pindah',
        ])->assertRedirect(route('admin.classes.index'));

        $this->assertDatabaseHas('classes', ['id' => $class->id, 'guru_id' => $secondGuru->id, 'name' => 'Kelas Pindah']);
    }

    public function test_admin_create_page_has_a_searchable_teacher_select(): void
    {
        $admin = $this->user('super_admin');
        $guru = $this->user('guru');

        $this->actingAs($admin)->get(route('admin.classes.create'))
            ->assertOk()
            ->assertSee('id="guru_id"', false)
            ->assertSee('data-kt-select-enable-search="true"', false)
            ->assertSee($guru->email);

        $this->actingAs($this->user('siswa'))->get(route('admin.classes.create'))->assertForbidden();
    }

    public function test_inactive_guru_class_is_read_only_and_no_member_or_class_caps_exist(): void
    {
        $guru = $this->user('guru', false);
        $class = $this->schoolClass($guru);
        $studentOne = $this->user('siswa');
        $studentTwo = $this->user('siswa');

        $this->actingAs($guru)->post(route('guru.classes.students.store', $class), ['email' => $studentOne->email])->assertForbidden();

        $activeGuru = $this->user('guru');
        $this->actingAs($activeGuru)->post(route('guru.classes.store'), ['name' => 'Satu'])->assertRedirect();
        $this->actingAs($activeGuru)->post(route('guru.classes.store'), ['name' => 'Dua'])->assertRedirect();
        $activeClass = $this->schoolClass($activeGuru, ['class_code' => 'MEMBERS1']);
        $this->actingAs($activeGuru)->post(route('guru.classes.students.store', $activeClass), ['email' => $studentOne->email])->assertRedirect();
        $this->actingAs($activeGuru)->post(route('guru.classes.students.store', $activeClass), ['email' => $studentTwo->email])->assertRedirect();

        $this->assertSame(3, SchoolClass::where('guru_id', $activeGuru->id)->count());
        $this->assertSame(2, ClassMember::where('class_id', $activeClass->id)->count());
    }
}
