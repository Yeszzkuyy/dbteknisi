<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelUserTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsSuperAdmin(): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        return $user;
    }

    public function test_store_user_syncs_legacy_role_column(): void
    {
        $this->actingAs($this->loginAsSuperAdmin())
            ->post(route('admin-panel.users.store'), [
                'name' => 'Sales Baru',
                'email' => 'salesbaru@tridayaapp.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['sales'],
            ])
            ->assertRedirect(route('admin-panel.index'));

        $user = User::where('email', 'salesbaru@tridayaapp.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('sales'));
        $this->assertSame('sales', $user->role);
    }

    public function test_store_user_rejects_unknown_role(): void
    {
        $this->actingAs($this->loginAsSuperAdmin())
            ->post(route('admin-panel.users.store'), [
                'name' => 'X',
                'email' => 'x@tridayaapp.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['role-tidak-ada'],
            ])
            ->assertSessionHasErrors('roles.0');

        $this->assertNull(User::where('email', 'x@tridayaapp.com')->first());
    }

    public function test_update_user_syncs_legacy_role_column(): void
    {
        $admin = $this->loginAsSuperAdmin();
        $user = User::factory()->create(['role' => 'guest']);

        $this->actingAs($admin)
            ->put(route('admin-panel.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => ['technician'],
            ])
            ->assertRedirect(route('admin-panel.index'));

        $fresh = $user->fresh();
        $this->assertTrue($fresh->hasRole('technician'));
        $this->assertSame('technician', $fresh->role);
    }
}
