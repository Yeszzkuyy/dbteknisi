<?php

namespace Tests\Unit\Tools;

use App\Ai\Tools\GetProjectProgress;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetProjectProgressTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_authorized_user_gets_progress_summary(): void
    {
        $teknisi = $this->userWithRole('teknisi');
        $this->actingAs($teknisi);

        $customer = Customer::create(['name' => 'PT Contoh']);
        $project = Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id,
            'project_name' => 'Project CCTV',
            'project_code' => 'CCTV-2026',
            'progress' => 75,
        ]);

        ProjectTask::forceCreate(['project_id' => $project->id, 'assigned_to' => $teknisi->id, 'title' => 'A', 'status' => 'Done']);
        ProjectTask::forceCreate(['project_id' => $project->id, 'assigned_to' => $teknisi->id, 'title' => 'B', 'status' => 'Done']);
        ProjectTask::forceCreate(['project_id' => $project->id, 'assigned_to' => $teknisi->id, 'title' => 'C', 'status' => 'Open']);

        ProjectActivity::create([
            'project_id' => $project->id,
            'user_id' => $teknisi->id,
            'activity_date' => now()->toDateString(),
            'title' => 'Survey lokasi selesai',
        ]);

        $result = json_decode((string) (new GetProjectProgress($teknisi))->handle(new Request([
            'project_id' => $project->id,
        ])), true);

        $this->assertSame(75, $result['progress']);
        $this->assertSame(['total' => 3, 'done' => 2, 'in_progress' => 0, 'open' => 1], $result['tasks']);
        $this->assertContains('Survey lokasi selesai', array_column($result['recent_activities'], 'title'));
    }

    public function test_marketing_is_denied(): void
    {
        $marketing = $this->userWithRole('marketing');
        $this->actingAs($marketing);

        $customer = Customer::create(['name' => 'PT Contoh']);
        $project = Project::create(['customer_id' => $customer->id, 'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id, 'project_name' => 'Project X']);

        $result = (string) (new GetProjectProgress($marketing))->handle(new Request([
            'project_id' => $project->id,
        ]));

        $this->assertStringContainsString('Akses ditolak', $result);
    }
}
