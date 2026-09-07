<?php

namespace Tests\Unit\Tools;

use App\Ai\Tools\GetLatestProjects;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetLatestProjectsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_teknisi_can_see_latest_projects(): void
    {
        $teknisi = $this->userWithRole('teknisi');
        $this->actingAs($teknisi);

        $customer = Customer::create(['name' => 'PT Contoh']);
        $workType = WorkType::create(['name' => 'Instalasi'])->id;

        Project::create(['customer_id' => $customer->id, 'work_type_id' => $workType, 'project_name' => 'Project A', 'project_code' => 'A-1']);
        Project::create(['customer_id' => $customer->id, 'work_type_id' => $workType, 'project_name' => 'Project B', 'project_code' => 'B-1', 'progress' => 50]);

        $result = json_decode((string) (new GetLatestProjects($teknisi))->handle(new Request), true);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContains('Project A', array_column($result, 'project'));
        $this->assertSame('PT Contoh', $result[0]['customer']);
    }

    public function test_sales_can_see_latest_projects(): void
    {
        $sales = $this->userWithRole('sales');
        $this->actingAs($sales);

        $customer = Customer::create(['name' => 'PT Contoh']);
        Project::create(['customer_id' => $customer->id, 'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id, 'project_name' => 'Project Sales']);

        $result = json_decode((string) (new GetLatestProjects($sales))->handle(new Request), true);

        $this->assertSame('Project Sales', $result[0]['project']);
    }

    public function test_marketing_is_denied(): void
    {
        $marketing = $this->userWithRole('marketing');

        $result = (string) (new GetLatestProjects($marketing))->handle(new Request);

        $this->assertStringContainsString('Akses ditolak', $result);
    }
}
