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

    private function user()
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $u = User::factory()->create();
        $u->givePermissionTo('view-sales');
        return $u;
    }

    public function test_search_filters_customers()
    {
        $u = $this->user();
        Customer::create(['name' => 'Alpha Corp']);
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

    public function test_live_search_ajax_returns_table_partial_only()
    {
        $u = $this->user();
        Customer::create(['name' => 'Alpha Corp']);
        Customer::create(['name' => 'Beta Ltd']);

        $res = $this->actingAs($u)
            ->call('GET', '/customers', ['search' => 'Alpha'], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'])
            ->assertOk();

        $body = $res->getContent();
        $this->assertStringContainsString('Alpha Corp', $body);
        $this->assertStringNotContainsString('Beta Ltd', $body);
        $this->assertStringNotContainsString('<html', strtolower($body)); // partial, bukan halaman penuh

        // tanpa kata kunci: semua kembali
        $body = $this->actingAs($u)
            ->call('GET', '/customers', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'])
            ->getContent();
        $this->assertStringContainsString('Beta Ltd', $body);
    }
}
