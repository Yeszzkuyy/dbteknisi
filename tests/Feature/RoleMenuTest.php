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

    /**
     * Matriks: tiap divisi melihat Dashboard + divisinya +
     * Customer + Trash, tapi TIDAK menu/halaman divisi lain.
     * Monitoring hanya untuk manager & super-admin.
     */
    public function test_teknisi_menu_and_access()
    {
        $u = $this->loginAs('teknisi');
        $res = $this->actingAs($u)->get('/teknisi/dashboard')->assertOk();
        $html = $res->getContent();

        // terlihat
        foreach (['/teknisi/jadwal', '/customers', '/trash'] as $link) {
            $this->assertStringContainsString($link, $html, "$link should be visible for teknisi");
        }
        // tersembunyi
        foreach (['/leads"', '/admin/invoices', '/sales/meetings', '/monitoring'] as $link) {
            $this->assertStringNotContainsString($link, $html, "$link should be hidden for teknisi");
        }

        // akses URL
        $this->actingAs($u)->get('/dashboard')->assertRedirect('/teknisi/dashboard');
        $this->actingAs($u)->get('/monitoring')->assertForbidden();
        $this->actingAs($u)->get('/customers')->assertOk();
        $this->actingAs($u)->get('/trash')->assertOk();
        $this->actingAs($u)->get('/leads')->assertForbidden();
        $this->actingAs($u)->get('/admin/invoices')->assertForbidden();
        $this->actingAs($u)->get('/sales/meetings')->assertForbidden();
    }

    public function test_sales_menu_and_access()
    {
        $u = $this->loginAs('sales');
        $res = $this->actingAs($u)->get('/sales/meetings')->assertOk();
        $html = $res->getContent();

        foreach (['/sales/follow-ups', '/customers', '/trash'] as $link) {
            $this->assertStringContainsString($link, $html, "$link should be visible for sales");
        }
        foreach (['/admin/invoices', '/leads"', '/monitoring'] as $link) {
            $this->assertStringNotContainsString($link, $html, "$link should be hidden for sales");
        }

        $this->actingAs($u)->get('/dashboard')->assertForbidden();
        $this->actingAs($u)->get('/monitoring')->assertForbidden();
        $this->actingAs($u)->get('/customers')->assertOk();
        $this->actingAs($u)->get('/trash')->assertOk();
        // Sales read-only pada Project: lihat boleh, mutasi 403
        $this->actingAs($u)->get('/projects')->assertOk();
        $this->actingAs($u)->get('/teknisi/dashboard')->assertForbidden();
        $this->actingAs($u)->get('/admin/invoices')->assertForbidden();
    }

    public function test_admin_menu_and_access()
    {
        $u = $this->loginAs('admin');
        $res = $this->actingAs($u)->get('/admin/invoices')->assertOk();
        $html = $res->getContent();

        foreach (['/admin/invoices', '/admin/pos', '/admin/payments', '/trash', '/customers'] as $link) {
            $this->assertStringContainsString($link, $html, "$link should be visible for admin");
        }
        foreach (['/leads"', '/projects"', '/sales/meetings'] as $link) {
            $this->assertStringNotContainsString($link, $html, "$link should be hidden for admin");
        }

        $this->actingAs($u)->get('/dashboard')->assertForbidden();
        $this->actingAs($u)->get('/monitoring')->assertForbidden();
        $this->actingAs($u)->get('/customers')->assertOk();
        $this->actingAs($u)->get('/trash')->assertOk();
        $this->actingAs($u)->get('/projects')->assertForbidden();
        $this->actingAs($u)->get('/leads')->assertForbidden();
    }

    public function test_marketing_menu_and_access()
    {
        $u = $this->loginAs('marketing');
        $res = $this->actingAs($u)->get('/leads')->assertOk();
        $html = $res->getContent();

        foreach (['/partners', '/customers', '/trash'] as $link) {
            $this->assertStringContainsString($link, $html, "$link should be visible for marketing");
        }
        foreach (['/admin/invoices', '/projects"', '/sales/meetings', '/monitoring'] as $link) {
            $this->assertStringNotContainsString($link, $html, "$link should be hidden for marketing");
        }

        $this->actingAs($u)->get('/dashboard')->assertRedirect(route('marketing.dashboard'));
        $this->actingAs($u)->get('/monitoring')->assertForbidden();
        $this->actingAs($u)->get('/customers')->assertOk();
        $this->actingAs($u)->get('/trash')->assertOk();
        $this->actingAs($u)->get('/admin/invoices')->assertForbidden();
        $this->actingAs($u)->get('/projects')->assertForbidden();
    }

    public function test_manager_still_sees_all()
    {
        $u = $this->loginAs('manager');
        $this->actingAs($u)->get('/projects')->assertOk();
        $this->actingAs($u)->get('/leads')->assertOk();
        $this->actingAs($u)->get('/customers')->assertOk();
        $this->actingAs($u)->get('/monitoring')->assertOk();
    }

    public function test_super_admin_sees_all()
    {
        $u = $this->loginAs('super-admin');
        $this->actingAs($u)->get('/projects')->assertOk();
        $this->actingAs($u)->get('/admin-panel')->assertOk();
        $this->actingAs($u)->get('/trash')->assertOk();
    }

    public function test_super_admin_bypasses_all_gates()
    {
        $u = $this->loginAs('super-admin');
        foreach (['view-teknisi', 'view-sales', 'view-admin', 'view-marketing', 'manage-monitoring', 'apa-aja-yang-tidak-ada'] as $p) {
            $this->assertTrue($u->can($p), $p);
        }
    }
}
