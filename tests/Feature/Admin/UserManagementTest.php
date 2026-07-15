<?php

// FR-SA-02 / BR-05 / NFR-09 / §3.2 / §9 / §11 Deactivation scenario / M2

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('super_admin');

        return $u;
    }

    private function guru(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('guru');

        return $u;
    }

    private function siswa(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('siswa');

        return $u;
    }

    // ─── RBAC: non-admin blocked ─────────────────────────────────────────────

    // §3.2: guru cannot list users
    public function test_guru_cannot_access_user_index(): void
    {
        $this->actingAs($this->guru())->get(route('admin.users.index'))->assertForbidden();
    }

    // §3.2: siswa cannot list users
    public function test_siswa_cannot_access_user_index(): void
    {
        $this->actingAs($this->siswa())->get(route('admin.users.index'))->assertForbidden();
    }

    // §3.2: guest redirected
    public function test_guest_redirected_from_user_index(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('auth.login.show'));
    }

    // ─── Index ───────────────────────────────────────────────────────────────

    // FR-SA-02: admin sees user list
    public function test_admin_can_list_users(): void
    {
        $admin = $this->admin();
        $this->guru(); // create one guru

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    // Super admin itself not in the list (excluded by repo)
    public function test_super_admin_not_in_list(): void
    {
        $admin = $this->admin();

        // Only admin in DB — table should be empty (0 non-admin users)
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('Belum ada pengguna.');
    }

    // ─── Create guru ─────────────────────────────────────────────────────────

    // FR-SA-02 / FR-AUTH-03: admin creates a guru
    public function test_admin_can_create_guru(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Guru Baru',
            'email' => 'guru.baru@test.com',
            'role' => 'guru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'guru.baru@test.com')->firstOrFail();
        $this->assertTrue($user->hasRole('guru'));
        $this->assertTrue((bool) $user->is_active);
    }

    // FR-SA-02: admin creates a siswa (ASSUMPTION: scope extended)
    public function test_admin_can_create_siswa(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Siswa Baru',
            'email' => 'siswa.baru@test.com',
            'role' => 'siswa',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'siswa.baru@test.com')->firstOrFail();
        $this->assertTrue($user->hasRole('siswa'));
    }

    // DATA-01: created_by = admin id for all admin-created users
    public function test_created_by_is_set_to_admin_id(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test.user@test.com',
            'role' => 'guru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test.user@test.com',
            'created_by' => $admin->id,
        ]);
    }

    // §9: email unique enforced
    public function test_duplicate_email_rejected_on_create(): void
    {
        $admin = $this->admin();
        $existing = $this->guru();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Duplicate',
            'email' => $existing->email,
            'role' => 'guru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    // §9: password min 8 enforced
    public function test_short_password_rejected_on_create(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test',
            'email' => 'test@test.com',
            'role' => 'guru',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])->assertSessionHasErrors('password');
    }

    // §9: invalid role rejected server-side
    public function test_invalid_role_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test',
            'email' => 'test@test.com',
            'role' => 'super_admin', // must be rejected
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('role');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    // FR-SA-02: admin can edit active user
    public function test_admin_can_edit_active_user(): void
    {
        $admin = $this->admin();
        $guru = $this->guru();

        $this->actingAs($admin)->put(route('admin.users.update', $guru), [
            'name' => 'Nama Baru',
            'email' => $guru->email,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $guru->id, 'name' => 'Nama Baru']);
    }

    // §9: blank password on edit keeps old password
    public function test_blank_password_on_edit_preserves_existing(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create([
            'password' => bcrypt('oldpassword'),
            'is_active' => true,
        ]);
        $guru->assignRole('guru');
        $oldHash = $guru->password;

        $this->actingAs($admin)->put(route('admin.users.update', $guru), [
            'name' => $guru->name,
            'email' => $guru->email,
            // password intentionally omitted
        ])->assertRedirect(route('admin.users.index'));

        $this->assertEquals($oldHash, $guru->fresh()->password);
    }

    // BR-05: editing inactive user forbidden (except reactivation path)
    public function test_edit_on_inactive_user_forbidden(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['is_active' => false]);
        $guru->assignRole('guru');

        $this->actingAs($admin)->put(route('admin.users.update', $guru), [
            'name' => 'Should Fail',
            'email' => $guru->email,
        ])->assertForbidden();
    }

    // ─── Deactivate ──────────────────────────────────────────────────────────

    // FR-SA-02 / BR-05: deactivation sets is_active=0
    public function test_admin_can_deactivate_active_user(): void
    {
        $admin = $this->admin();
        $guru = $this->guru();

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $guru))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $guru->id, 'is_active' => false]);
    }

    // BR-05: data NOT hard-deleted after deactivation
    public function test_deactivation_does_not_hard_delete(): void
    {
        $admin = $this->admin();
        $guru = $this->guru();
        $id = $guru->id;

        $this->actingAs($admin)->patch(route('admin.users.status', $guru));

        $this->assertDatabaseHas('users', ['id' => $id]);
    }

    // BR-05 / NFR-09: write on inactive target returns 403
    public function test_write_on_inactive_user_rejected_403(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['is_active' => false]);
        $guru->assignRole('guru');

        // Attempt update on inactive user
        $this->actingAs($admin)->put(route('admin.users.update', $guru), [
            'name' => 'Attack',
            'email' => $guru->email,
        ])->assertForbidden();
    }

    // ─── Reactivate ──────────────────────────────────────────────────────────

    // FR-SA-02: reactivation flips is_active=1
    public function test_admin_can_reactivate_inactive_user(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['is_active' => false]);
        $guru->assignRole('guru');

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $guru))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $guru->id, 'is_active' => true]);
    }

    // BR-05: inactive user still can't log in (carried from M1, verified in M2 context)
    public function test_deactivated_user_cannot_login(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['password' => bcrypt('secret'), 'is_active' => true]);
        $guru->assignRole('guru');

        // Deactivate
        $this->actingAs($admin)->patch(route('admin.users.status', $guru));
        $this->post('/logout');

        // Attempt login
        $this->post(route('auth.login'), ['email' => $guru->email, 'password' => 'secret'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
