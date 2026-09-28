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

    public function test_running_border_visible_outside_profile(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $yeski = User::factory()->create(['email' => 'yehezkielmayogi.ptnti@gmail.com']);
        $yeski->assignRole('super-admin');
        $biasa = User::factory()->create();
        $biasa->assignRole('marketing');

        // Sidebar (dashboard) Yeski: ada ring running
        $this->actingAs($yeski)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('conic-gradient', false);

        // Sidebar user biasa: tidak ada
        $this->actingAs($biasa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('conic-gradient', false);
    }

    public function test_avatar_wrapper_stays_square_in_flex_contexts(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $yeski = User::factory()->create(['email' => 'yehezkielmayogi.ptnti@gmail.com']);

        $html = \Illuminate\Support\Facades\Blade::render(
            '<x-user-avatar :user="$user" size="w-10 h-10" />',
            ['user' => $yeski]
        );

        // Wrapper anti-melar (penyebab avatar lonjong di list flex) + ring running tetap ada
        $this->assertStringContainsString('self-center aspect-square', $html);
        $this->assertStringContainsString('conic-gradient', $html);
    }

    public function test_avatar_shows_google_style_decoration_by_default(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $html = \Illuminate\Support\Facades\Blade::render(
            '<x-user-avatar :user="$user" size="w-10 h-10" />',
            ['user' => $user]
        );

        // Dekoratif: ring biru + dot hijau meski password masih bawaan
        $this->assertStringContainsString('ring-blue-500', $html);
        $this->assertStringContainsString('bg-green-500', $html);
        $this->assertStringNotContainsString('ring-red-500', $html);
    }

    public function test_avatar_security_mode_reflects_password_status(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();

        $red = \Illuminate\Support\Facades\Blade::render(
            '<x-user-avatar :user="$user" size="w-10 h-10" :security="true" />',
            ['user' => $user]
        );
        $this->assertStringContainsString('ring-red-500', $red);

        $user->forceFill(['password_changed_at' => now()])->save();

        $green = \Illuminate\Support\Facades\Blade::render(
            '<x-user-avatar :user="$user" size="w-10 h-10" :security="true" />',
            ['user' => $user->fresh()]
        );
        $this->assertStringContainsString('ring-blue-500', $green);
        $this->assertStringNotContainsString('ring-red-500', $green);
    }
}
