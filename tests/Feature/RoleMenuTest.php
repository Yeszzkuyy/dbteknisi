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
     * Header grup sidebar: label berupa link navigasi + tombol chevron
     * toggle buka/tutup (@click="open = !open", aria-controls ke menu).
     * Lipatan juga tetap mengikuti halaman aktif via JS.
     */
    public function test_sidebar_group_headers_have_link_and_toggle()
    {
        $u = $this->loginAs('super-admin');
        $html = $this->actingAs($u)->get('/dashboard')->assertOk()->getContent();

        // Potong hanya navigasi sidebar (dropdown avatar/notif boleh punya toggle sendiri).
        $navHtml = str_contains($html, 'id="sidebar-navigation"')
            ? substr($html, strpos($html, 'id="sidebar-navigation"'))
            : $html;
        $navHtml = substr($navHtml, 0, strpos($navHtml, '</nav>') ?: null);

        foreach ( [
            'sidebar-management-menu' => 'manage-sales',
            'sidebar-technician-menu' => 'teknisi/dashboard',
            'sidebar-marketing-menu' => 'marketing/dashboard',
            'sidebar-sales-menu' => 'sales/my-leads',
            'sidebar-admin-menu' => 'admin/invoices',
            'sidebar-admin-panel-menu' => 'admin-panel',
        ] as $controls => $path) {
            $this->assertMatchesRegularExpression(
                '/<button[^>]*@click="open = !open"[^>]*aria-controls="' . $controls . '"/',
                $navHtml,
                "header $controls harus punya tombol toggle"
            );
            $this->assertStringContainsString($path, $navHtml);
        }

        $this->assertStringNotContainsString('@click="open = !open"', $navHtml);
    }

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
        $this->actingAs($u)->get('/dashboard')->assertOk();
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

        $this->actingAs($u)->get('/dashboard')->assertOk();
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

        $this->actingAs($u)->get('/dashboard')->assertOk();
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

        $this->actingAs($u)->get('/dashboard')->assertOk();
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
