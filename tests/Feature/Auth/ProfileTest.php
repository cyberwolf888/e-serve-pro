<?php

// FR-AUTH-01 / FR-AUTH-05 / FR-AUTH-06 / BR-05 / §11
// Feature tests for the shared profile page across all roles.

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileTest extends AuthTestCase
{
    private function createUser(string $role, array $attributes = []): User
    {
        /** @var User $user */
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    public function test_guests_are_redirected_from_profile(): void
    {
        $this->get(route('profile.show'))->assertRedirectToRoute('auth.login.show');
    }

    public function test_super_admin_can_view_and_update_profile(): void
    {
        $user = $this->createUser('super_admin', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Profil Saya')
            ->assertSee($user->email)
            ->assertSee('Super Admin');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirectToRoute('profile.show')
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_guru_can_view_and_update_profile(): void
    {
        $user = $this->createUser('guru', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Guru');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Guru Baru',
                'email' => 'guru.baru@example.com',
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirectToRoute('profile.show');

        $user->refresh();
        $this->assertSame('Guru Baru', $user->name);
    }

    public function test_siswa_can_view_and_update_profile(): void
    {
        $user = $this->createUser('siswa', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Siswa');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Siswa Baru',
                'email' => 'siswa.baru@example.com',
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirectToRoute('profile.show');

        $user->refresh();
        $this->assertSame('Siswa Baru', $user->name);
    }

    public function test_update_name_and_email_without_password_change(): void
    {
        $user = $this->createUser('siswa', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Hanya Nama',
                'email' => 'hanya@example.com',
            ])
            ->assertRedirectToRoute('profile.show');

        $user->refresh();
        $this->assertSame('Hanya Nama', $user->name);
        $this->assertSame('hanya@example.com', $user->email);
        $this->assertTrue(Hash::check('old-password', $user->password));
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = $this->createUser('siswa', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirectToRoute('profile.show')
            ->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('old-password', $user->password));
    }

    public function test_update_rejects_duplicate_email(): void
    {
        $first = $this->createUser('siswa', ['email' => 'first@example.com', 'is_active' => true]);

        $second = $this->createUser('siswa', ['email' => 'second@example.com', 'is_active' => true]);

        $this->actingAs($second)
            ->from(route('profile.show'))
            ->put(route('profile.update'), [
                'name' => $second->name,
                'email' => 'first@example.com',
            ])
            ->assertRedirectToRoute('profile.show')
            ->assertSessionHasErrors('email');

        $second->refresh();
        $this->assertSame('second@example.com', $second->email);
    }

    public function test_inactive_user_cannot_update_profile(): void
    {
        $user = $this->createUser('siswa', [
            'password' => Hash::make('old-password'),
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Should Fail',
                'email' => 'should-fail@example.com',
            ])
            ->assertForbidden();

        $user->refresh();
        $this->assertNotSame('Should Fail', $user->name);
    }

    public function test_validation_requires_name_and_email(): void
    {
        $user = $this->createUser('guru', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->put(route('profile.update'), [
                'name' => '',
                'email' => '',
            ])
            ->assertRedirectToRoute('profile.show')
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_password_requires_confirmation(): void
    {
        $user = $this->createUser('guru', [
            'password' => Hash::make('old-password'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirectToRoute('profile.show')
            ->assertSessionHasErrors('password');

        $user->refresh();
        $this->assertTrue(Hash::check('old-password', $user->password));
    }
}
