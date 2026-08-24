<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
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
            ->assertSee('Kebutuhan User')
            ->assertSee('Tanggal Masuk')
            ->assertDontSee('Nilai Opportunity');
    }

    public function test_store_creates_lead_with_customer_details(): void
    {
        $this->actingAs($this->marketingUser())
            ->post(route('leads.store'), [
                'customer_mode' => 'new',
                'customer_name' => 'PT Uji Coba',
                'customer_company' => 'PT Uji Coba',
                'customer_email' => 'info@ujicoba.id',
                'customer_phone' => '0812-3456-7890',
                'customer_address' => 'Jl. Testing No. 1, Jakarta',
                'segment' => 'system_integrator',
                'source' => 'canvasing',
                'kebutuhan' => 'Instalasi jaringan 3 lantai',
                'incoming_date' => '2026-08-24',
            ])
            ->assertRedirect(route('leads.index'));

        $lead = Lead::whereHas('customer', fn ($q) => $q->where('name', 'PT Uji Coba'))->first();

        $this->assertNotNull($lead);
        $this->assertSame('system_integrator', $lead->segment);
        $this->assertSame('canvasing', $lead->source);
        $this->assertSame('new', $lead->status);
        $this->assertSame('2026-08-24', $lead->incoming_date->toDateString());
        $this->assertSame('info@ujicoba.id', $lead->customer->email);
        $this->assertSame('Jl. Testing No. 1, Jakarta', $lead->customer->address);
    }

    public function test_segment_is_required_and_validated(): void
    {
        $customer = Customer::create(['name' => 'PT Lama']);

        $this->actingAs($this->marketingUser())
            ->post(route('leads.store'), [
                'customer_mode' => 'existing',
                'customer_id' => $customer->id,
                'segment' => 'bukan_segment',
            ])
            ->assertSessionHasErrors(['segment']);
    }
}
