<?php

// DATA-07 (legacy) / BR-06 — historical attendance data and logs remain readable.

namespace Tests\Feature\Attendance;

use App\Models\ActivityLog;
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

    public function test_historical_attendance_record_and_activity_remain_visible(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $student = User::factory()->create();
        $student->assignRole('siswa');
        $class = SchoolClass::create([
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => 'KELAS001',
            'is_active' => true,
        ]);
        ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        $meeting = Meeting::create([
            'class_id' => $class->id,
            'title' => 'Pertemuan Lama',
            'scheduled_at' => now()->subMonth(),
        ]);
        $attendance = Attendance::create([
            'meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'hadir',
            'recorded_at' => now()->subMonth(),
        ]);
        ActivityLog::create([
            'user_id' => $student->id,
            'event_type' => 'attendance',
            'description' => 'Absensi historis',
            'subject_type' => Attendance::class,
            'subject_id' => $attendance->id,
        ]);

        $this->assertModelExists($attendance);
        $this->actingAs($student)->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Absensi')
            ->assertSee('Absensi historis');
    }
}
