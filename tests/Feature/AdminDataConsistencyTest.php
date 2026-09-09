<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkType;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminDataConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(string $customerName): Project
    {
        $customer = Customer::create(['name' => $customerName]);
        $workType = WorkType::create(['name' => 'Instalasi']);

        return Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => $workType->id,
            'project_name' => 'Project ' . $customerName,
        ]);
    }

    public function test_invoice_rejects_project_of_different_customer(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->actingAs(User::factory()->create()->assignRole('super-admin'));

        $project = $this->makeProject('PT Satu');
        $otherCustomer = Customer::create(['name' => 'PT Dua']);

        $this->expectException(ValidationException::class);

        app(\App\Services\AdminService::class)->createInvoice([
            'customer_id' => $otherCustomer->id,
            'project_id' => $project->id,
            'amount' => 1000,
            'status' => 'unpaid',
            'issue_date' => now()->toDateString(),
        ]);
    }

    public function test_invoice_accepts_project_of_same_customer(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->actingAs(User::factory()->create()->assignRole('super-admin'));

        $project = $this->makeProject('PT Satu');

        $invoice = app(\App\Services\AdminService::class)->createInvoice([
            'customer_id' => $project->customer_id,
            'project_id' => $project->id,
            'amount' => 1000,
            'status' => 'unpaid',
            'issue_date' => now()->toDateString(),
        ]);

        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    public function test_creating_project_marks_customer_as_deal(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->actingAs(User::factory()->create()->assignRole('super-admin'));

        $customer = Customer::create(['name' => 'PT Baru', 'status' => 'lead']);

        $project = Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => WorkType::create(['name' => 'Instalasi'])->id,
            'project_name' => 'Project Baru',
        ]);

        $this->assertSame('deal', $project->customer->fresh()->status);
    }
}