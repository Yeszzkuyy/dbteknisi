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

    public function test_trash_is_scoped_per_account()
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $a = User::factory()->create();
        $b = User::factory()->create();
        $a->givePermissionTo('view-trash', 'manage-sales');
        $b->givePermissionTo('view-trash');

        // A menghapus customer miliknya
        $cA = Customer::create(['name' => 'Milik A']);
        $this->actingAs($a)->delete('/customers/'.$cA->id);
        $this->assertNotNull($cA->fresh()->deleted_at);
        $this->assertEquals($a->id, $cA->fresh()->deleted_by);

        // B tidak melihat trash milik A
        $res = $this->actingAs($b)->get('/trash');
        $res->assertOk();
        $this->assertStringNotContainsString('Milik A', $res->getContent());

        // B tidak bisa restore milik A (keblokir permission atau bukan pemilik)
        $this->actingAs($b)->patch("/trash/customers/{$cA->id}/restore")->assertForbidden();

        // A tetap melihat punyanya
        $res = $this->actingAs($a)->get('/trash');
        $this->assertStringContainsString('Milik A', $res->getContent());
    }
}
