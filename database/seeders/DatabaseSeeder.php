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

        $superAdmin = User::updateOrCreate([
            'email' => 'superadmin@mail.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $superAdmin->syncRoles(['super_admin']);

        // Demo fixtures: 10 guru (incl. guru@mail.com), 50 siswa (incl. siswa@mail.com),
        // 5 kelas/guru, 25 siswa/kelas, 10 pertemuan/kelas + materi/kuis.
        $this->call(DemoDataSeeder::class);
    }
}
