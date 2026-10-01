<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPipelineTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function makeLead(array $attributes = []): Lead
    {
        $customer = Customer::create(['name' => 'PT Pipeline']);

        return Lead::create(array_merge([
            'customer_id' => $customer->id,
            'pt_group' => 'NTI',
            'segment' => 'vendor',
            'status' => 'new',
        ], $attributes));
    }

    public function test_pipeline_page_shows_own_leads_grouped_by_status(): void
    {
        $user = $this->userWithRole('sales');
        $this->makeLead(['status' => 'proposal', 'assigned_to' => $user->id]);
        $this->makeLead(['status' => 'won', 'assigned_to' => $user->id]);

        $response = $this->actingAs($user)->get(route('leads.pipeline'))
            ->assertOk()
            ->assertSee('PT Pipeline');

        $leads = $response->viewData('leads');
        $this->assertSame(1, $leads->where('status', 'proposal')->count());
        $this->assertSame(1, $leads->where('status', 'won')->count());
    }

    public function test_pipeline_never_mixes_other_sales_customers(): void
    {
        $mine = $this->userWithRole('sales');
        $other = User::factory()->create();
        $other->assignRole('sales');
        $this->makeLead(['assigned_to' => $mine->id]);

        $otherCustomer = Customer::create(['name' => 'PT Orang Lain']);
        Lead::create([
            'customer_id' => $otherCustomer->id,
            'pt_group' => 'NTI',
            'segment' => 'vendor',
            'status' => 'new',
            'assigned_to' => $other->id,
        ]);
        $this->makeLead(); // tanpa assignee

        $this->actingAs($mine)->get(route('leads.pipeline'))
            ->assertOk()
            ->assertSee('PT Pipeline')
            ->assertDontSee('PT Orang Lain');
    }

    public function test_non_sales_cannot_open_pipeline(): void
    {
        $this->actingAs($this->userWithRole('marketing'))
            ->get(route('leads.pipeline'))->assertForbidden();
    }

    public function test_management_sees_no_mixed_customers(): void
    {
        // Management pegang semua view-* (matriks role) sehingga boleh buka,
        // tapi scope own-lead membuat hasilnya kosong (tidak campur).
        $sales = $this->userWithRole('sales');
        $this->makeLead(['assigned_to' => $sales->id]);

        $response = $this->actingAs($this->userWithRole('management'))
            ->get(route('leads.pipeline'))->assertOk();
        $this->assertSame(0, $response->viewData('leads')->count());
    }

    public function test_last_week_won_hidden_unless_closed_all(): void
    {
        $user = $this->userWithRole('sales');
        $this->makeLead(['status' => 'won', 'assigned_to' => $user->id, 'closed_at' => now()->subWeek()]);
        $thisWeek = $this->makeLead(['status' => 'won', 'assigned_to' => $user->id, 'closed_at' => now()]);

        $response = $this->actingAs($user)->get(route('leads.pipeline'))->assertOk();
        $leads = $response->viewData('leads');
        $this->assertSame(1, $leads->where('status', 'won')->count());
        $this->assertTrue($leads->contains($thisWeek));

        $response = $this->actingAs($user)->get(route('leads.pipeline', ['closed' => 'all']))->assertOk();
        $this->assertSame(2, $response->viewData('leads')->where('status', 'won')->count());
    }

    public function test_drag_to_won_stamps_closed_at(): void
    {
        $user = $this->userWithRole('sales');
        $lead = $this->makeLead(['assigned_to' => $user->id]);
        $this->assertNull($lead->closed_at);

        $this->actingAs($user)->patchJson(route('leads.batch-status'), [
            'changes' => [['lead_id' => $lead->id, 'status' => 'won']],
        ])->assertOk();

        $fresh = $lead->fresh();
        $this->assertSame('won', $fresh->status);
        $this->assertNotNull($fresh->closed_at);

        // Geser balik keluar won -> closed_at dibersihkan.
        $this->actingAs($user)->patchJson(route('leads.batch-status'), [
            'changes' => [['lead_id' => $lead->id, 'status' => 'proposal']],
        ])->assertOk();
        $this->assertNull($lead->fresh()->closed_at);
    }

    public function test_marketing_can_update_lead_status_via_pipeline(): void
    {
        $user = $this->userWithRole('marketing');
        $lead = $this->makeLead();

        $this->actingAs($user)
            ->patch(route('leads.update-status', $lead), ['status' => 'contacted'])
            ->assertNoContent();

        $this->assertSame('contacted', $lead->fresh()->status);
        $this->assertSame(1, $lead->activities()->where('action', 'status_changed')->count());
    }

    public function test_status_update_is_rejected_with_invalid_status(): void
    {
        $user = $this->userWithRole('marketing');
        $lead = $this->makeLead();

        $this->actingAs($user)
            ->patch(route('leads.update-status', $lead), ['status' => 'ngawur'])
            ->assertSessionHasErrors('status');

        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_sales_cannot_update_unassigned_lead_status(): void
    {
        $user = $this->userWithRole('sales');
        $lead = $this->makeLead();

        $this->actingAs($user)
            ->patch(route('leads.update-status', $lead), ['status' => 'won'])
            ->assertForbidden();

        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_sales_can_update_own_assigned_lead_status(): void
    {
        $user = $this->userWithRole('sales');
        $lead = $this->makeLead(['assigned_to' => $user->id]);

        $this->actingAs($user)
            ->patch(route('leads.update-status', $lead), ['status' => 'contacted'])
            ->assertNoContent();

        $this->assertSame('contacted', $lead->fresh()->status);
    }

    public function test_dashboard_shows_marketing_stats(): void
    {
        $user = $this->userWithRole('marketing');
        $this->makeLead(['status' => 'new', 'source' => 'whatsapp', 'incoming_date' => now()]);
        $this->makeLead(['status' => 'won', 'source' => 'referral', 'incoming_date' => now()->subMonths(2)]);
        $this->makeLead(['status' => 'lost', 'incoming_date' => now()]);

        $this->actingAs($user)->get(route('marketing.dashboard'))
            ->assertOk()
            ->assertViewHas('stats', fn ($stats) => $stats['total'] === 3
                && $stats['won'] === 1 && $stats['conversion'] === 50)
            ->assertViewHas('funnel', fn ($funnel) => $funnel->sum('value') === 3
                && $funnel->pluck('key')->all() === ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost']);

        $this->assertSame(3, Lead::count());
    }
}
