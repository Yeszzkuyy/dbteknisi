<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TrashPerUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed sekali di awal, baru assign role & permission.
     * @return array{0:User,1:User,2:User} [teknisi A, sales B, super admin]
     */
    private function setUpUsers(): array
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

        $a = User::factory()->create();
        $b = User::factory()->create();
        $sa = User::factory()->create();

        $a->assignRole('teknisi');
        $b->assignRole('sales');
        $sa->assignRole('super-admin');

        $a->givePermissionTo(['view-trash', 'view-customer', 'manage-sales', 'manage-admin']);
        $b->givePermissionTo(['view-trash', 'view-customer', 'manage-sales', 'manage-admin']);

        return [$a, $b, $sa];
    }

    private function deleteCustomer(User $by, string $name): Customer
    {
        $c = Customer::create(['name' => $name]);
        $this->actingAs($by)->delete('/customers/'.$c->id);
        return $c->fresh();
    }

    public function test_regular_user_sees_only_own_trash()
    {
        [$a, $b] = $this->setUpUsers();

        $ca = $this->deleteCustomer($a, 'Milik A');
        $cb = $this->deleteCustomer($b, 'Milik B');
        $this->assertEquals($a->id, $ca->deleted_by);
        $this->assertEquals($b->id, $cb->deleted_by);

        $htmlA = $this->actingAs($a)->get('/trash')->assertOk()->getContent();
        $this->assertStringContainsString('Trash Saya', $htmlA);
        $this->assertStringContainsString('Milik A', $htmlA);
        $this->assertStringNotContainsString('Milik B', $htmlA);

        $htmlB = $this->actingAs($b)->get('/trash')->assertOk()->getContent();
        $this->assertStringContainsString('Milik B', $htmlB);
        $this->assertStringNotContainsString('Milik A', $htmlB);
    }

    public function test_user_cannot_touch_others_trash_items()
    {
        [$a, $b] = $this->setUpUsers();

        $ca = $this->deleteCustomer($a, 'Punya A');

        // B tidak bisa restore / hapus permanen milik A -> dianggap tidak ada (404)
        $this->actingAs($b)->patch('/trash/customers/'.$ca->id.'/restore')->assertNotFound();
        $this->actingAs($b)->delete('/trash/customers/'.$ca->id)->assertNotFound();
        $this->assertNotNull(Customer::onlyTrashed()->find($ca->id));
    }

    public function test_super_admin_sees_all_trash_with_deleter_and_filter()
    {
        [$a, $b, $sa] = $this->setUpUsers();

        $ca = $this->deleteCustomer($a, 'Milik A');
        $this->deleteCustomer($b, 'Milik B');

        $html = $this->actingAs($sa)->get('/trash')->assertOk()->getContent();
        $this->assertStringContainsString('All Trash', $html);
        $this->assertStringContainsString('Dihapus Oleh', $html);
        $this->assertStringContainsString('Milik A', $html);
        $this->assertStringContainsString('Milik B', $html);

        // filter per user
        $htmlFiltered = $this->actingAs($sa)->get('/trash?user='.$a->id)->assertOk()->getContent();
        $this->assertStringContainsString('Milik A', $htmlFiltered);
        $this->assertStringNotContainsString('Milik B', $htmlFiltered);

        // super admin bisa restore trash siapa pun
        $this->actingAs($sa)->patch('/trash/customers/'.$ca->id.'/restore')->assertRedirect();
        $this->assertNull($ca->fresh()->deleted_at);
    }
}
