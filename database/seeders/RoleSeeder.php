<?php

// §3.1 / DATA-18

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ponytail: firstOrCreate guards idempotent re-runs
        foreach (['super_admin', 'guru', 'siswa'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
