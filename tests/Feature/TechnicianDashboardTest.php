<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('view-teknisi');

        return $user;
    }

    private function projectWith(String $name, int $statusId): Project
    {
        $customer = Customer::create(['name' => 'PT ABC Indonesia']);
        $workType = WorkType::create(['name' => 'Instalasi']);

        return Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => $workType->id,
            'project_status_id' => $statusId,
            'pic_engineer' => 'Naufal',
            'project_name' => $name,
        ]);
    }

    public function test_dashboard_shows_stats_progress_project_activity_and_active_technician(): void
    {
        $user = $this->authorizedUser();

        $naufal = User::factory()->create(['name' => 'Naufal']);
        $naufal->assignRole('teknisi');
        $budi = User::factory()->create(['name' => 'Budi']);
        $budi->assignRole('teknisi');

        $open = ProjectStatus::create(['name' => 'Open', 'color' => 'blue', 'sort_order' => 1]);
        $done = ProjectStatus::create(['name' => 'Done', 'color' => 'green', 'sort_order' => 2]);

        $this->actingAs($user);
        $project = $this->projectWith('Instalasi CCTV', $open->id);
        $this->projectWith('Maintenance Server', $done->id);

        ProjectActivity::create([
            'project_id' => $project->id,
            'user_id' => $naufal->id,
            'activity_date' => now()->subHour(),
            'title' => 'menambahkan catatan',
        ]);

        $this->actingAs($user)
            ->get(route('teknisi.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Teknisi')
            ->assertSee('Total Teknisi')
            ->assertSee('Teknisi Aktif')
            ->assertSee('Project Berjalan')
            ->assertSee('Project Selesai')
            ->assertSee('Progress Pekerjaan')
            ->assertSee('Instalasi CCTV')
            ->assertSee('PT ABC Indonesia')
            ->assertSee('Maintenance Server')
            ->assertSee('menambahkan catatan')
            ->assertSee('Buka Jadwal')
            ->assertDontSee('Jadwal Hari Ini');
    }

    public function test_technician_matched_despite_case_whitespace_and_support_list(): void
    {
        $user = $this->authorizedUser();

        $andi = User::factory()->create(['name' => 'Andi Pratama']);
        $andi->assignRole('teknisi');
        User::factory()->create(['name' => 'Sari'])->assignRole('teknisi');

        $open = ProjectStatus::create(['name' => 'Open', 'color' => 'blue', 'sort_order' => 1]);

        $this->actingAs($user);

        // pic_engineer beda kapital/spasi, support_technicians berupa daftar koma
        Project::create([
            'customer_id' => Customer::create(['name' => 'PT ABC'])->id,
            'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id,
            'project_status_id' => $open->id,
            'pic_engineer' => ' andi pratama ',
            'support_technicians' => 'Rudi, Sari ,Joko',
            'project_name' => 'Instalasi Jaringan',
        ]);

        $this->actingAs($user)
            ->get(route('teknisi.dashboard'))
            ->assertOk()
            ->assertSee('Andi Pratama')
            ->assertSee('Sari')
            ->assertSee('Instalasi Jaringan');
    }

    public function test_technician_without_running_project_is_not_active(): void
    {
        $user = $this->authorizedUser();

        $inactive = User::factory()->create(['name' => 'Dedi']);
        $inactive->assignRole('teknisi');

        $this->actingAs($user)
            ->get(route('teknisi.dashboard'))
            ->assertOk()
            ->assertSee('Teknisi Aktif')
            ->assertSee('Belum ada teknisi aktif');
    }
}