<?php

// DATA-05 / DATA-06 / DATA-07 (legacy) — meeting, sharing, and attendance runtime retired.

namespace Tests\Feature\Meetings;

use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MeetingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_meeting_sharing_and_attendance_routes_are_retired(): void
    {
        foreach (['admin', 'guru'] as $prefix) {
            $this->assertFalse(Route::has("{$prefix}.classes.meetings.index"));
            $this->assertFalse(Route::has("{$prefix}.classes.meetings.share"));
            $this->assertFalse(Route::has("{$prefix}.classes.meetings.attendance.edit"));
            $this->assertFalse(Route::has("{$prefix}.classes.meetings.attendance.store"));
        }
    }

    public function test_former_meeting_sharing_and_attendance_urls_return_not_found(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $class = SchoolClass::create([
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => 'KELAS001',
            'is_active' => true,
        ]);

        $this->actingAs($guru)->get("/guru/classes/{$class->id}/meetings")->assertNotFound();
        $this->actingAs($guru)->post("/guru/classes/{$class->id}/meetings/1/materials")->assertNotFound();
        $this->actingAs($guru)->get("/guru/classes/{$class->id}/meetings/1/attendance")->assertNotFound();
        $this->actingAs($guru)->post("/guru/classes/{$class->id}/meetings/1/attendance")->assertNotFound();

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin)->get("/admin/classes/{$class->id}/meetings")->assertNotFound();
        $this->actingAs($admin)->post("/admin/classes/{$class->id}/meetings/1/attendance")->assertNotFound();
    }
}
