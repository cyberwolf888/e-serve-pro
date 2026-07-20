<?php

// Demo fixtures: 10 guru, 50 siswa, 5 kelas/guru, 25 siswa/kelas (random),
// 10 pertemuan/kelas each with 1 materi + 1 kuis (4 opsi, 1 benar).

namespace Database\Seeders;

use App\Models\ClassMember;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    private const GURU_COUNT = 10;

    private const SISWA_COUNT = 50;

    private const CLASSES_PER_GURU = 5;

    private const MEMBERS_PER_CLASS = 25;

    private const SESSIONS_PER_CLASS = 10;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $gurus = $this->seedUsers('guru', self::GURU_COUNT, 'Guru');
        $siswas = $this->seedUsers('siswa', self::SISWA_COUNT, 'Siswa');

        $classes = $this->seedClasses($gurus);
        $this->seedMembers($classes, $siswas);
        $this->seedSessions($classes);
    }

    /** @return Collection<int, User> */
    private function seedUsers(string $role, int $count, string $label): Collection
    {
        $users = collect();

        for ($i = 1; $i <= $count; $i++) {
            $email = $i === 1 ? "{$role}@mail.com" : "{$role}{$i}@mail.com";

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
     * @param  Collection<int, User>  $gurus
     * @return Collection<int, SchoolClass>
     */
    private function seedClasses(Collection $gurus): Collection
    {
        $classes = collect();

        foreach ($gurus as $guruIndex => $guru) {
            for ($c = 1; $c <= self::CLASSES_PER_GURU; $c++) {
                $code = sprintf('SEED%02d%d', $guruIndex + 1, $c);

                $classes->push(SchoolClass::updateOrCreate(['class_code' => $code], [
                    'guru_id' => $guru->id,
                    'name' => "Kelas {$guru->name} - {$c}",
                    'is_active' => true,
                ]));
            }
        }

        return $classes;
    }

    /**
     * @param  Collection<int, SchoolClass>  $classes
     * @param  Collection<int, User>  $siswas
     */
    private function seedMembers(Collection $classes, Collection $siswas): void
    {
        $now = now();

        foreach ($classes as $class) {
            // ponytail: hash-sort picks a stable "random" 25 per class without touching global RNG state
            $rows = $siswas
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
    private function seedSessions(Collection $classes): void
    {
        $base = Carbon::parse('2026-01-12');

        foreach ($classes as $class) {
            for ($n = 1; $n <= self::SESSIONS_PER_CLASS; $n++) {
                $meeting = Meeting::updateOrCreate(
                    ['class_id' => $class->id, 'title' => "Pertemuan {$n}"],
                    ['scheduled_at' => $base->copy()->addWeeks($n - 1)]
                );

                $material = Material::updateOrCreate(
                    ['class_id' => $class->id, 'title' => "Materi Pertemuan {$n}"],
                    ['type' => 'figma', 'figma_url' => "https://figma.com/file/demo-{$class->id}-{$n}"]
                );

                $meeting->materials()->syncWithoutDetaching([$material->id]);

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
