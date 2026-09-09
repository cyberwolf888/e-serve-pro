<?php

// Demo fixtures: 10 lecturers, 50 students, 5 classes/lecturer, 25 students/class (random),
// 10 published materials + 10 quizzes/class (4 options, 1 correct).

namespace Database\Seeders;

use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private const LECTURER_COUNT = 10;

    private const STUDENT_COUNT = 50;

    private const CLASSES_PER_LECTURER = 5;

    private const MEMBERS_PER_CLASS = 25;

    private const ITEMS_PER_CLASS = 10;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $lecturers = $this->seedUsers('guru', self::LECTURER_COUNT, 'Dosen');
        $students = $this->seedUsers('siswa', self::STUDENT_COUNT, 'Mahasiswa');

        $classes = $this->seedClasses($lecturers);
        $this->seedMembers($classes, $students);
        $this->seedLearningContent($classes);
    }

    /** @return Collection<int, User> */
    private function seedUsers(string $role, int $count, string $label): Collection
    {
        $users = collect();
        $emailPrefix = Str::lower($label);

        for ($i = 1; $i <= $count; $i++) {
            $email = $i === 1 ? "{$emailPrefix}@mail.com" : "{$emailPrefix}{$i}@mail.com";

            $user = User::firstOrCreate(['email' => $email], [
                'name' => "{$label} {$i}",
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $user->syncRoles([$role]);
            $users->push($user);
        }

        return $users;
    }

    /**
     * @param  Collection<int, User>  $lecturers
     * @return Collection<int, SchoolClass>
     */
    private function seedClasses(Collection $lecturers): Collection
    {
        $classes = collect();

        foreach ($lecturers as $lecturerIndex => $lecturer) {
            for ($c = 1; $c <= self::CLASSES_PER_LECTURER; $c++) {
                $code = sprintf('SEED%02d%d', $lecturerIndex + 1, $c);

                $classes->push(SchoolClass::updateOrCreate(['class_code' => $code], [
                    'guru_id' => $lecturer->id,
                    'name' => "Kelas {$lecturer->name} - {$c}",
                    'is_active' => true,
                ]));
            }
        }

        return $classes;
    }

    /**
     * @param  Collection<int, SchoolClass>  $classes
     * @param  Collection<int, User>  $students
     */
    private function seedMembers(Collection $classes, Collection $students): void
    {
        $now = now();

        foreach ($classes as $class) {
            // ponytail: hash-sort picks a stable "random" 25 per class without touching global RNG state
            $rows = $students
                ->sortBy(fn (User $s) => md5($class->id.'-'.$s->id))
                ->take(self::MEMBERS_PER_CLASS)
                ->map(fn (User $s) => [
                    'class_id' => $class->id,
                    'student_id' => $s->id,
                    'joined_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all();

            DB::table((new ClassMember)->getTable())->upsert(
                $rows,
                ['class_id', 'student_id'],
                ['joined_at', 'updated_at']
            );
        }
    }

    /** @param  Collection<int, SchoolClass>  $classes */
    private function seedLearningContent(Collection $classes): void
    {
        foreach ($classes as $class) {
            for ($n = 1; $n <= self::ITEMS_PER_CLASS; $n++) {
                Material::updateOrCreate(
                    ['class_id' => $class->id, 'title' => "Materi Pertemuan {$n}"],
                    [
                        'description' => "Materi pembelajaran {$n} untuk {$class->name}.",
                        'type' => 'figma',
                        'figma_url' => "https://figma.com/file/demo-{$class->id}-{$n}",
                        'is_published' => true,
                    ]
                );

                $quiz = Quiz::updateOrCreate(
                    ['class_id' => $class->id, 'title' => "Kuis Pertemuan {$n}"],
                    ['is_published' => true]
                );

                $question = QuizQuestion::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'order' => 1],
                    ['question_text' => "Soal demo untuk {$quiz->title}"]
                );

                foreach (['A', 'B', 'C', 'D'] as $i => $label) {
                    QuizOption::updateOrCreate(
                        ['question_id' => $question->id, 'label' => $label],
                        ['option_text' => "Pilihan {$label}", 'is_correct' => $i === 0]
                    );
                }
            }
        }
    }
}
