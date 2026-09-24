<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CustomerSubmitTest extends TestCase
{
    use RefreshDatabase;

    private function user()
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $u = \App\Models\User::factory()->create();
        $u->givePermissionTo('manage-sales');
        return $u;
    }

    public function test_store_redirects_with_success_card_flag(): void
    {
        $this->actingAs($this->user())
            ->post(route('customers.store'), [
                'name' => 'PT Maju Jaya',
                'pt_group' => 'NTI',
            ])
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('success_card', true);

        $this->assertSame(1, Customer::where('name', 'PT Maju Jaya')->count());
    }

    public function test_update_redirects_with_success_card_flag(): void
    {
        $customer = Customer::create(['name' => 'PT Lama', 'pt_group' => 'NTI']);

        $this->actingAs($this->user())
            ->put(route('customers.update', $customer), [
                'name' => 'PT Baru',
                'pt_group' => 'MGK',
            ])
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('success_card', true);

        $this->assertSame('MGK', $customer->fresh()->pt_group);
    }
}
