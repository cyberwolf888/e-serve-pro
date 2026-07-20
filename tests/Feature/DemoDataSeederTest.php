<?php

// Verifies demo fixture volumes/idempotency for DemoDataSeeder.

namespace Tests\Feature;

use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = DemoDataSeeder::class;

    /** @test — happy path: exact fixture volumes per requirement */
    public function test_seeder_creates_expected_volumes(): void
    {
        $this->assertSame(10, User::role('guru')->count());
        $this->assertSame(50, User::role('siswa')->count());
        $this->assertSame(50, SchoolClass::count());
        $this->assertSame(1250, ClassMember::count());
        $this->assertSame(500, Meeting::count());
        $this->assertSame(500, Material::count());
        $this->assertSame(500, Quiz::count());
        $this->assertSame(500, QuizQuestion::count());
        $this->assertSame(2000, QuizOption::count());

        SchoolClass::all()->each(function (SchoolClass $class) {
            $this->assertSame(5, SchoolClass::where('guru_id', $class->guru_id)->count());
            $this->assertSame(25, ClassMember::where('class_id', $class->id)->count());
            $this->assertSame(10, Meeting::where('class_id', $class->id)->count());
        });

        QuizQuestion::all()->each(function (QuizQuestion $question) {
            $this->assertSame(1, QuizOption::where('question_id', $question->id)->where('is_correct', true)->count());
            $this->assertSame(4, QuizOption::where('question_id', $question->id)->count());
        });
    }

    /** @test — failure path: re-running the seeder must not duplicate rows */
    public function test_seeder_is_idempotent_on_rerun(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(10, User::role('guru')->count());
        $this->assertSame(50, User::role('siswa')->count());
        $this->assertSame(50, SchoolClass::count());
        $this->assertSame(1250, ClassMember::count());
        $this->assertSame(500, Meeting::count());
        $this->assertSame(500, Material::count());
        $this->assertSame(500, Quiz::count());
        $this->assertSame(500, QuizQuestion::count());
        $this->assertSame(2000, QuizOption::count());
    }
}
