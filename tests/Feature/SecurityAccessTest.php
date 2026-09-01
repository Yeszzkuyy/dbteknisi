<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAccessTest extends TestCase
{
    use RefreshDatabase;

    private function loginAs(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function makeProjectDocument(User $user, string $filename = 'boq.pdf'): ProjectDocument
    {
        $this->actingAs($user);

        $customer = Customer::create(['name' => 'PT Security']);
        $workType = \App\Models\WorkType::create(['name' => 'Instalasi']);
        $project = Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => $workType->id,
            'project_name' => 'Project Security',
        ]);

        Storage::fake('private');
        Storage::disk('private')->put('documents/1/'.$filename, 'isi rahasia');

        return $project->documents()->create([
            'file_name' => $filename,
            'file_path' => 'documents/1/'.$filename,
            'mime_type' => 'application/pdf',
            'uploaded_by' => User::first()->id,
        ]);
    }

    /**
     * Dokumen project hanya boleh diakses divisi yang punya akses project
     * (view-teknisi/manage-teknisi/view-sales). Marketing dilarang.
     */
    public function test_marketing_cannot_download_project_document(): void
    {
        $u = $this->loginAs('marketing');
        $doc = $this->makeProjectDocument($u);

        $this->actingAs($u)->get(route('project-documents.download', $doc))->assertForbidden();
        $this->actingAs($u)->get(route('project-documents.preview', $doc))->assertForbidden();
    }

    /**
     * Teknisi boleh melihat/mengunduh dokumen project (read), sesuai keputusan
     * bisnis: sesama teknisi boleh lihat project yang bukan miliknya.
     */
    public function test_teknisi_can_download_project_document(): void
    {
        $u = $this->loginAs('teknisi');
        $doc = $this->makeProjectDocument($u);

        $this->actingAs($u)->get(route('project-documents.download', $doc))->assertOk();
    }

    /**
     * Sales boleh download (read-only), tapi dilarang upload/hapus dokumen.
     */
    public function test_sales_can_download_but_not_modify_project_document(): void
    {
        $u = $this->loginAs('sales');
        $doc = $this->makeProjectDocument($u);

        $this->actingAs($u)->get(route('project-documents.download', $doc))->assertOk();
        $this->actingAs($u)->delete(route('project-documents.destroy', $doc))->assertForbidden();
    }

    /**
     * Teknisi tetap bisa upload dokumen ke project (fitur existing tidak rusak).
     */
    public function test_teknisi_can_upload_project_document(): void
    {
        $u = $this->loginAs('teknisi');
        $this->actingAs($u);
        $customer = Customer::create(['name' => 'PT Upload']);
        $workType = \App\Models\WorkType::create(['name' => 'Instalasi']);
        $project = Project::create([
            'customer_id' => $customer->id,
            'work_type_id' => $workType->id,
            'project_name' => 'Project Upload',
        ]);
        Storage::fake('private');

        $this->actingAs($u)->post(route('project-documents.store', $project), [
            'file' => UploadedFile::fake()->create('laporan.xlsx', 50, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ])->assertRedirect();

        $this->assertSame(1, $project->fresh()->documents()->count());
        $this->assertSame('laporan.xlsx', $project->fresh()->documents()->first()->file_name);
    }

    /**
     * Bukti transfer pembayaran hanya untuk divisi admin.
     */
    public function test_payment_proof_admin_can_view_marketing_forbidden(): void
    {
        $customer = Customer::create(['name' => 'PT Bayar']);
        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-001',
            'customer_id' => $customer->id,
            'amount' => 100000,
            'status' => 'unpaid',
            'issue_date' => now()->toDateString(),
        ]);
        Storage::fake('private');
        Storage::disk('private')->put('payments/bukti.jpg', 'gambar');

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => 100000,
            'payment_date' => now()->toDateString(),
            'proof_file' => 'payments/bukti.jpg',
        ]);

        $admin = $this->loginAs('admin');
        $this->actingAs($admin)->get(route('admin.payments.proof', $payment))->assertOk();

        $marketing = $this->loginAs('marketing');
        $this->actingAs($marketing)->get(route('admin.payments.proof', $payment))->assertForbidden();
    }

    /**
     * Super admin tetap melewati semua policy (Gate::before).
     */
    public function test_super_admin_can_download_any_project_document(): void
    {
        $u = $this->loginAs('super-admin');
        $doc = $this->makeProjectDocument($u);

        $this->actingAs($u)->get(route('project-documents.download', $doc))->assertOk();
    }
}
