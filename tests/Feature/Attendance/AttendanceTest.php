<?php

// DATA-07 / FR-GR-07 / BR-05 / BR-06 / M4

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\ClassMember;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
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

    public function test_guru_records_attendance_for_class_members(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $studentOne = $this->user('siswa');
        $studentTwo = $this->user('siswa');
        ClassMember::create(['class_id' => $class->id, 'student_id' => $studentOne->id, 'joined_at' => now()]);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $studentTwo->id, 'joined_at' => now()]);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$studentOne->id => 'hadir', $studentTwo->id => 'izin'],
        ])->assertRedirect(route('guru.classes.meetings.attendance.edit', [$class, $meeting]));

        $this->assertDatabaseHas('attendances', ['meeting_id' => $meeting->id, 'student_id' => $studentOne->id, 'status' => 'hadir']);
        $this->assertDatabaseHas('attendances', ['meeting_id' => $meeting->id, 'student_id' => $studentTwo->id, 'status' => 'izin']);
    }

    public function test_resubmitting_attendance_updates_not_duplicates(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $student = $this->user('siswa');
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'hadir'],
        ]);
        $this->actingAs($guru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'sakit'],
        ]);

        $this->assertSame(1, Attendance::where('meeting_id', $meeting->id)->where('student_id', $student->id)->count());
        $this->assertDatabaseHas('attendances', ['meeting_id' => $meeting->id, 'student_id' => $student->id, 'status' => 'sakit']);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $student = $this->user('siswa');
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'hadir_banget'],
        ])->assertSessionHasErrors('statuses.'.$student->id);

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_attendance_writes_activity_log_entries(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $student = $this->user('siswa');
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($guru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'hadir'],
        ]);

        $attendance = Attendance::where('meeting_id', $meeting->id)->where('student_id', $student->id)->firstOrFail();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $student->id,
            'event_type' => 'attendance',
            'subject_type' => Attendance::class,
            'subject_id' => $attendance->id,
        ]);
    }

    public function test_other_guru_and_inactive_class_cannot_record_attendance(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($owner);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $meeting = Meeting::create(['class_id' => $class->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($otherGuru)->post(route('guru.classes.meetings.attendance.store', [$class, $meeting]), [
            'statuses' => [$student->id => 'hadir'],
        ])->assertForbidden();

        $inactiveGuru = $this->user('guru', false);
        $inactiveClass = $this->schoolClass($inactiveGuru, ['class_code' => 'INACTIVE']);
        $inactiveMeeting = Meeting::create(['class_id' => $inactiveClass->id, 'title' => 'P1', 'scheduled_at' => now()]);

        $this->actingAs($inactiveGuru)->post(route('guru.classes.meetings.attendance.store', [$inactiveClass, $inactiveMeeting]), [
            'statuses' => [$student->id => 'hadir'],
        ])->assertForbidden();
    }
}
