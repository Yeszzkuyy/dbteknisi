<?php

namespace Tests\Unit\Tools;

use App\Ai\Tools\GetMyTasks;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetMyTasksTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_teknisi_sees_only_their_own_tasks(): void
    {
        $teknisi = $this->userWithRole('teknisi');
        $other = User::factory()->create();

        $this->actingAs($teknisi);

        $customer = Customer::create(['name' => 'PT Contoh']);
        $project = Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id,
            'project_name' => 'Instalasi CCTV',
            'project_code' => 'PRJ-001',
        ]);

        ProjectTask::forceCreate([
            'project_id' => $project->id,
            'assigned_to' => $teknisi->id,
            'title' => 'Pasang kamera lantai 2',
            'status' => 'Progress',
        ]);

        ProjectTask::forceCreate([
            'project_id' => $project->id,
            'assigned_to' => $other->id,
            'title' => 'Kalibrasi sensor',
            'status' => 'Open',
        ]);

        $result = json_decode((string) (new GetMyTasks($teknisi))->handle(new Request), true);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame('Pasang kamera lantai 2', $result[0]['task']);
        $this->assertSame('PRJ-001', $result[0]['project_code']);
    }

    public function test_user_without_permission_is_denied(): void
    {
        $marketing = $this->userWithRole('marketing');

        $result = (string) (new GetMyTasks($marketing))->handle(new Request);

        $this->assertStringContainsString('Akses ditolak', $result);
    }

    public function test_teknisi_without_tasks_gets_empty_message(): void
    {
        $teknisi = $this->userWithRole('teknisi');

        $result = (string) (new GetMyTasks($teknisi))->handle(new Request);

        $this->assertStringContainsString('Tidak ada tugas', $result);
    }
}
