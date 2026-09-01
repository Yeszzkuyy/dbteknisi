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

    public function test_pipeline_page_shows_leads_grouped_by_status(): void
    {
        $user = $this->userWithRole('marketing');
        $this->makeLead(['status' => 'proposal']);
        $this->makeLead(['status' => 'won']);

        $response = $this->actingAs($user)->get(route('leads.pipeline'))
            ->assertOk()
            ->assertSee('PT Pipeline');

        $leads = $response->viewData('leads');
        $this->assertSame(1, $leads->where('status', 'proposal')->count());
        $this->assertSame(1, $leads->where('status', 'won')->count());
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

    public function test_sales_cannot_update_lead_status(): void
    {
        $user = $this->userWithRole('sales');
        $lead = $this->makeLead();

        $this->actingAs($user)
            ->patch(route('leads.update-status', $lead), ['status' => 'won'])
            ->assertForbidden();

        $this->assertSame('new', $lead->fresh()->status);
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
