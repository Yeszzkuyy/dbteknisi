<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionGuardTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        return $user;
    }

    public function test_company_account_cannot_be_deleted_from_admin_panel(): void
    {
        $admin = $this->superAdmin();
        $karyawan = User::factory()->create(['email' => 'coba@tridayaapp.com']);
        $karyawan->assignRole('technician');

        $this->actingAs($admin)
            ->delete(route('admin-panel.users.destroy', $karyawan))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted($karyawan);
    }

    public function test_regular_user_can_still_be_deleted(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create();
        $user->assignRole('marketing');

        $this->actingAs($admin)
            ->delete(route('admin-panel.users.destroy', $user))
            ->assertRedirect();

        $this->assertSoftDeleted($user);
    }

    public function test_non_last_super_admin_can_be_deleted(): void
    {
        $admin = $this->superAdmin();
        $other = User::factory()->create();
        $other->assignRole('super-admin');

        $this->actingAs($admin)
            ->delete(route('admin-panel.users.destroy', $other))
            ->assertRedirect();

        $this->assertSoftDeleted($other);
    }

    public function test_last_super_admin_cannot_self_delete_via_profile(): void
    {
        $admin = $this->superAdmin();
        // Jadikan dia satu-satunya super-admin
        User::where('email', 'superadmin@dbteknisi.com')->first()?->removeRole('super-admin');

        $this->actingAs($admin)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect();

        $this->assertNotSoftDeleted($admin);
    }
}
