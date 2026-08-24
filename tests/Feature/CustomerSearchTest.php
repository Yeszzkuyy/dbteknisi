<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CustomerSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_customers()
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $u = User::factory()->create();
        $u->givePermissionTo('view-sales');

        Customer::create(['name' => 'Alpha Corp', 'email' => 'alpha@x.com']);
        Customer::create(['name' => 'Beta Ltd']);

        // dengan search: hanya yang cocok
        $this->actingAs($u)->call('GET', '/customers', ['search' => 'Alpha'])
            ->assertOk()
            ->assertSee('Alpha Corp')
            ->assertDontSee('Beta Ltd');

        // tanpa search: semua tampil + tombol Cari ada
        $this->actingAs($u)->get('/customers')
            ->assertOk()
            ->assertSee('Alpha Corp')
            ->assertSee('Beta Ltd')
            ->assertSee('Cari');
    }
}
