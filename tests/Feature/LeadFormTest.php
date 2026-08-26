<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    private function marketingUser(): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('marketing');

        return $user;
    }

    public function test_create_page_shows_segment_and_new_fields(): void
    {
        $this->actingAs($this->marketingUser())
            ->get(route('leads.create'))
            ->assertOk()
            ->assertSee('Segment')
            ->assertSee('System Integrator')
            ->assertSee('Canvasing')
            ->assertSee('Kebutuhan')
            ->assertSee('Tanggal Masuk')
            ->assertDontSee('Nilai Opportunity');
    }

    public function test_store_creates_lead_with_customer_details(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole('sales');
        $this->actingAs($this->marketingUser())
            ->post(route('leads.store'), [
                'customer_mode' => 'new',
                'customer_name' => 'PT Uji Coba',
                'customer_email' => 'info@ujicoba.id',
                'customer_phone' => 'wa.me/6281234567890',
                'customer_address' => 'Jl. Testing No. 1, Jakarta',
                'customer_contact_person' => 'Budi PIC',
                'pt_group' => 'NTI',
                'segment' => 'system_integrator',
                'source' => 'canvasing',
                'kebutuhan' => 'Instalasi jaringan 3 lantai',
                'incoming_date' => '2026-08-24',
                'assigned_to' => $sales->id,
            ])
            ->assertRedirect(route('leads.index'));

        $lead = Lead::whereHas('customer', fn ($q) => $q->where('name', 'PT Uji Coba'))->first();

        $this->assertNotNull($lead);
        $this->assertSame('system_integrator', $lead->segment);
        $this->assertSame('canvasing', $lead->source);
        $this->assertSame('new', $lead->status);
        $this->assertSame('2026-08-24', $lead->incoming_date->toDateString());
        $this->assertSame('NTI', $lead->pt_group);
        $this->assertSame('info@ujicoba.id', $lead->customer->email);
        $this->assertSame('Budi PIC', $lead->customer->contact_person);
        $this->assertSame('Jl. Testing No. 1, Jakarta', $lead->customer->address);
    }

    public function test_activities_are_logged_with_user(): void
    {
        $user = $this->marketingUser();
        $customer = Customer::create(['name' => 'PT Log Uji']);

        $this->actingAs($user)->post(route('leads.store'), [
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'pt_group' => 'NTI',
            'segment' => 'vendor',
            'kebutuhan' => 'Instalasi CCTV',
            'incoming_date' => now()->toDateString(),
            'assigned_to' => $user->id,
        ])->assertRedirect(route('leads.index'));

        $lead = Lead::latest('id')->first();

        $this->actingAs($user)->put(route('leads.update', $lead), [
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'pt_group' => 'TPS',
            'segment' => 'end_user',
            'kebutuhan' => 'Instalasi CCTV',
            'incoming_date' => now()->toDateString(),
            'assigned_to' => $user->id,
        ])->assertRedirect(route('leads.index'));

        $created = LeadActivity::where('lead_id', $lead->id)->where('action', 'created')->first();
        $updated = LeadActivity::where('lead_id', $lead->id)->where('action', 'updated')->first();

        $this->assertNotNull($created);
        $this->assertSame($user->id, $created->user_id);

        $this->assertNotNull($updated);
        $this->assertSame($user->id, $updated->user_id);
        $this->assertSame('vendor', $updated->changes['segment']['old']);
        $this->assertSame('end_user', $updated->changes['segment']['new']);
    }

    public function test_activities_page_renders(): void
    {
        $this->actingAs($this->marketingUser())
            ->get(route('leads.activities'))
            ->assertOk()
            ->assertSee('Log Aktivitas Lead');
    }

    public function test_segment_is_required_and_validated(): void
    {
        $customer = Customer::create(['name' => 'PT Lama']);

        $this->actingAs($this->marketingUser())
            ->post(route('leads.store'), [
                'customer_mode' => 'existing',
                'customer_id' => $customer->id,
                'pt_group' => 'MGK',
                'segment' => 'bukan_segment',
            ])
            ->assertSessionHasErrors(['segment']);
    }
}
