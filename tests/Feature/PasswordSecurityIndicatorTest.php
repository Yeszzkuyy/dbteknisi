<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordSecurityIndicatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_red_for_default_password(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('marketing');

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(__('Password belum diganti'));
    }

    public function test_changing_own_password_marks_account_secure(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('marketing');

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect();

        $this->assertNotNull($user->fresh()->password_changed_at);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(__('Akun aman'));
    }

    public function test_admin_reset_does_not_mark_account_secure(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $user = User::factory()->create();
        $user->assignRole('marketing');

        $this->actingAs($admin)
            ->put(route('admin-panel.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'reset-oleh-admin-123',
                'password_confirmation' => 'reset-oleh-admin-123',
            ])
            ->assertRedirect();

        $this->assertNull($user->fresh()->password_changed_at);
    }

    public function test_yeski_gets_running_border(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $yeski = User::factory()->create(['email' => 'yehezkielmayogi.ptnti@gmail.com']);
        $yeski->assignRole('super-admin');

        $this->actingAs($yeski)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('conic-gradient', false);
    }
}
