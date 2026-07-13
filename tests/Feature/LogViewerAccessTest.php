<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LogViewerAccessTest extends TestCase
{
    use RefreshDatabase;

    // FR-SA-04 / BR-06
    public function test_non_super_admin_cannot_access_log_viewer(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/log-viewer')->assertForbidden();
    }

    // FR-SA-04 / BR-06
    public function test_super_admin_can_access_log_viewer(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');

        $this->actingAs($user);

        $this->get('/log-viewer')->assertOk();
    }
}
