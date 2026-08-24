<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RoleMenuTest extends TestCase
{
    use RefreshDatabase;

    private function loginAs(string $role): User
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_teknisi_sees_only_teknisi_menu()
    {
        $u = $this->loginAs('teknisi');
        $res = $this->actingAs($u)->get('/teknisi/dashboard')->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('Teknisi', $html);
        foreach (['/leads"', '/admin/invoices', '/customers', '/monitoring', '/trash'] as $link) {
            $this->assertStringNotContainsString($link, $html, "$link should be hidden for teknisi");
        }
        // tidak bisa akses halaman divisi lain
        $this->actingAs($u)->get('/leads')->assertForbidden();
        $this->actingAs($u)->get('/admin/invoices')->assertForbidden();
        $this->actingAs($u)->get('/customers')->assertForbidden();
        $this->actingAs($u)->get('/sales/meetings')->assertForbidden();
    }

    public function test_sales_sees_only_sales_menu()
    {
        $u = $this->loginAs('sales');
        $res = $this->actingAs($u)->get('/sales/meetings')->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('Sales', $html);
        $this->assertStringNotContainsString('Teknisi', $html);
        $this->actingAs($u)->get('/projects')->assertForbidden();
        $this->actingAs($u)->get('/teknisi/dashboard')->assertForbidden();
    }

    public function test_admin_sees_only_admin_menu()
    {
        $u = $this->loginAs('admin');
        $res = $this->actingAs($u)->get('/admin/invoices')->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('Admin', $html);
        $this->assertStringNotContainsString('Marketing', $html);
        $this->actingAs($u)->get('/projects')->assertForbidden();
    }

    public function test_manager_still_sees_all()
    {
        $u = $this->loginAs('manager');
        $this->actingAs($u)->get('/projects')->assertOk();
        $this->actingAs($u)->get('/leads')->assertOk();
        $this->actingAs($u)->get('/customers')->assertOk();
    }

    public function test_super_admin_sees_all()
    {
        $u = $this->loginAs('super-admin');
        $this->actingAs($u)->get('/projects')->assertOk();
        $this->actingAs($u)->get('/admin-panel')->assertOk();
    }

    public function test_super_admin_bypasses_all_gates()
    {
        $u = $this->loginAs('super-admin');
        foreach (['view-teknisi', 'view-sales', 'view-admin', 'view-marketing', 'manage-monitoring', 'apa-aja-yang-tidak-ada'] as $p) {
            $this->assertTrue($u->can($p), $p);
        }
    }
}
