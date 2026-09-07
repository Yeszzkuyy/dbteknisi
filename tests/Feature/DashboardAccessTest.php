<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_marketing_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('marketing'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_teknisi_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('teknisi'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_sales_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('sales'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_manager_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('manager'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_management_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('management'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_super_admin_can_access_general_dashboard(): void
    {
        $this->actingAs($this->userWithRole('super-admin'))
            ->get(route('dashboard'))
            ->assertOk();
    }
}
