<?php

namespace Tests\Unit\Tools;

use App\Ai\Tools\GetProjectDetails;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetProjectDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function makeProject(): Project
    {
        $customer = Customer::create(['name' => 'PT Contoh', 'company' => 'PT Contoh Teknologi']);

        return Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id,
            'project_name' => 'Project CCTV',
            'project_code' => 'CCTV-2026',
            'progress' => 60,
            'description' => 'Pemasangan CCTV kantor',
        ]);
    }

    public function test_authorized_user_gets_project_details(): void
    {
        $teknisi = $this->userWithRole('teknisi');
        $this->actingAs($teknisi);
        $project = $this->makeProject();

        ProjectTask::forceCreate([
            'project_id' => $project->id,
            'assigned_to' => $teknisi->id,
            'title' => 'Instalasi kamera',
            'status' => 'Done',
        ]);

        $result = json_decode((string) (new GetProjectDetails($teknisi))->handle(new Request([
            'project_id' => $project->id,
        ])), true);

        $this->assertSame('Project CCTV', $result['project']);
        $this->assertSame('PT Contoh Teknologi', $result['company']);
        $this->assertSame(60, $result['progress']);
        $this->assertSame('Instalasi kamera', $result['tasks'][0]['task']);
    }

    public function test_project_id_is_required(): void
    {
        $teknisi = $this->userWithRole('teknisi');

        $result = (string) (new GetProjectDetails($teknisi))->handle(new Request);

        $this->assertStringContainsString('project_id', $result);
    }

    public function test_nonexistent_project_returns_not_found(): void
    {
        $teknisi = $this->userWithRole('teknisi');

        $result = (string) (new GetProjectDetails($teknisi))->handle(new Request(['project_id' => 9999]));

        $this->assertStringContainsString('tidak ditemukan', $result);
    }

    public function test_marketing_is_denied(): void
    {
        $marketing = $this->userWithRole('marketing');
        $this->actingAs($marketing);
        $project = $this->makeProject();

        $result = (string) (new GetProjectDetails($marketing))->handle(new Request([
            'project_id' => $project->id,
        ]));

        $this->assertStringContainsString('Akses ditolak', $result);
    }
}
