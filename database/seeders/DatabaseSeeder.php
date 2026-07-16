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

        $guru = User::updateOrCreate([
            'email' => 'guru@mail.com',
        ], [
            'name' => 'Guru',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $siswa = User::updateOrCreate([
            'email' => 'siswa@mail.com',
        ], [
            'name' => 'Siswa',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $superAdmin->syncRoles(['super_admin']);
        $guru->syncRoles(['guru']);
        $siswa->syncRoles(['siswa']);

    }
}
