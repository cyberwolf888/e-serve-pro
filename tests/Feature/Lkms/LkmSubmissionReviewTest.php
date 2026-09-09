<?php

// FR-SA-08 / FR-GR-11 / FR-GR-15 / DATA-27 / NFR-08 / §11 / M7.9

namespace Tests\Feature\Lkms;

use App\Models\ClassMember;
use App\Models\ComponentScore;
use App\Models\GradeComponent;
use App\Models\LkmAssignment;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GradeService;
use App\Services\LkmService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LkmSubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    public function test_owner_and_admin_can_review_only_proof_submissions_with_read_only_reflections(): void
    {
        [$guru, $class, $lkm, $assignments] = $this->fixture(4);
        $admin = $this->user('super_admin');
        $foreignGuru = $this->user('guru');
        $student = $assignments[0]->student;
        $assignments[0]->update([
            'proof_url' => 'https://youtu.be/video-proof',
            'proof_submitted_at' => now(),
            'sop_checks' => [0],
            'reflection_submitted_at' => now(),
        ]);
        $assignments[1]->update([
            'proof_url' => 'https://drive.google.com/drive-proof',
            'proof_submitted_at' => now()->subMinute(),
        ]);
        $assignments[2]->update([
            'proof_url' => 'https://docs.google.com/docs-proof',
            'proof_submitted_at' => now()->subMinutes(2),
            'sop_checks' => [1],
            'reflection_submitted_at' => now(),
        ]);
        $assignments[2]->student->update(['is_active' => false]);
        $assignments[3]->student->update(['name' => 'Belum Mengirim']);

        $route = route('guru.classes.lkms.submissions.index', [$class, $lkm]);
        $response = $this->actingAs($guru)->get($route);

        $response->assertOk()
            ->assertSeeInOrder(['video-proof', 'drive-proof', 'docs-proof'])
            ->assertDontSee('Belum Mengirim')
            ->assertSee('fieldset disabled', false)
            ->assertSee('value="0" checked', false)
            ->assertSee('value="1"', false)
            ->assertSee('Menunggu refleksi')
            ->assertSee('Tidak aktif')
            ->assertSee(route('guru.classes.lkms.submissions.grade', [$class, $lkm, $assignments[0]]), false)
            ->assertDontSee(route('guru.classes.lkms.submissions.grade', [$class, $lkm, $assignments[1]]), false)
            ->assertDontSee(route('guru.classes.lkms.submissions.grade', [$class, $lkm, $assignments[2]]), false);

        $this->actingAs($admin)->get(route('admin.classes.lkms.submissions.index', [$class, $lkm]))->assertOk();
        $this->actingAs($foreignGuru)->get($route)->assertForbidden();
        $this->actingAs($student)->get($route)->assertForbidden();
    }

    public function test_completed_submission_accepts_boundary_and_decimal_scores_and_syncs_automatic_component(): void
    {
        [$guru, $class, $lkm, $assignments] = $this->fixture();
        $assignment = $this->complete($assignments[0]);
        $component = GradeComponent::create([
            'class_id' => $class->id,
            'name' => 'LKM',
            'weight' => 100,
            'lkm_id' => $lkm->id,
        ]);
        $page = route('guru.classes.lkms.submissions.index', [$class, $lkm, 'page' => 2]);
        $route = route('guru.classes.lkms.submissions.grade', [$class, $lkm, $assignment]);

        foreach ([0, 100, 82.5] as $score) {
            $this->actingAs($guru)->from($page)->patch($route, ['score' => $score])
                ->assertRedirect($page)
                ->assertSessionHas('success');
            $this->assertSame(number_format($score, 2, '.', ''), $assignment->fresh()->score);
        }

        $this->assertDatabaseHas('component_scores', [
            'grade_component_id' => $component->id,
            'student_id' => $assignment->student_id,
            'score' => 82.5,
            'is_manual_override' => false,
        ]);

        app(GradeService::class)->recordScores($component, [$assignment->student_id => 95]);
        $this->actingAs($guru)->patch($route, ['score' => 70])->assertRedirect();
        $this->assertSame('70.00', $assignment->fresh()->score);
        $this->assertSame('95.00', ComponentScore::firstOrFail()->score);
        $this->assertTrue(ComponentScore::firstOrFail()->is_manual_override);
    }

    public function test_invalid_incomplete_inactive_unauthorized_and_tampered_grades_do_not_mutate(): void
    {
        [$guru, $class, $lkm, $assignments] = $this->fixture(2);
        $completed = $this->complete($assignments[0]);
        $pending = $assignments[1];
        $pending->update(['proof_url' => 'https://youtu.be/pending', 'proof_submitted_at' => now()]);
        $route = fn (LkmAssignment $assignment) => route('guru.classes.lkms.submissions.grade', [$class, $lkm, $assignment]);

        foreach ([null, '', -0.01, 100.01, 'not-a-number'] as $score) {
            $this->actingAs($guru)->patch($route($completed), ['score' => $score])->assertSessionHasErrors('score');
        }
        $this->actingAs($guru)->patch($route($pending), ['score' => 80])->assertSessionHasErrors('score');

        $completed->student->update(['is_active' => false]);
        $this->actingAs($guru)->patch($route($completed), ['score' => 80])->assertForbidden();
        $completed->student->update(['is_active' => true]);

        $class->update(['is_active' => false]);
        $this->actingAs($guru)->patch($route($completed), ['score' => 80])->assertForbidden();
        $class->update(['is_active' => true]);

        $foreignGuru = $this->user('guru');
        $this->actingAs($foreignGuru)->patch($route($completed), ['score' => 80])->assertForbidden();

        [$otherGuru, $otherClass, $otherLkm] = $this->fixture();
        $this->actingAs($otherGuru)->patch(
            route('guru.classes.lkms.submissions.grade', [$otherClass, $otherLkm, $completed]),
            ['score' => 80],
        )->assertNotFound();

        $this->assertNull($completed->fresh()->score);
        $this->assertNull($pending->fresh()->score);
    }

    private function complete(LkmAssignment $assignment): LkmAssignment
    {
        $assignment->update([
            'proof_url' => 'https://youtu.be/completed',
            'proof_submitted_at' => now(),
            'sop_checks' => [0],
            'reflection_submitted_at' => now(),
        ]);

        return $assignment;
    }

    private function fixture(int $studentCount = 1): array
    {
        $guru = $this->user('guru');
        $class = SchoolClass::create([
            'guru_id' => $guru->id,
            'name' => 'Kelas LKM',
            'class_code' => fake()->unique()->bothify('LKM#####'),
            'is_active' => true,
        ]);
        foreach (range(1, $studentCount) as $index) {
            $student = $this->user('siswa');
            ClassMember::create(['class_id' => $class->id, 'student_id' => $student->id, 'joined_at' => now()]);
        }
        $lkm = app(LkmService::class)->create($class, $guru, [
            'title' => 'LKM Kolaborasi',
            'description' => 'Kerjakan bersama kelompok.',
            'roles' => [
                ['name' => 'Ketua', 'description' => 'Memimpin.', 'instructions' => 'Atur.', 'sop_items' => ['Bagi tugas', 'Periksa hasil']],
            ],
        ]);

        return [$guru, $class, $lkm, $lkm->assignments()->with('student')->orderBy('id')->get()];
    }

    private function user(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}
