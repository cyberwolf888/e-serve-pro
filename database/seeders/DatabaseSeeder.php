<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    // FR-AUTH-01 / NFR-03
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Demo fixtures: 10 lecturers (incl. dosen@mail.com), 50 students (incl. mahasiswa@mail.com),
        // 5 classes/lecturer, 25 students/class, 10 meetings/class + materials/quizzes.
        $this->call(DemoDataSeeder::class);

        $superAdmin = User::updateOrCreate([
            'email' => 'superadmin@mail.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $superAdmin->syncRoles(['super_admin']);
    }
}
