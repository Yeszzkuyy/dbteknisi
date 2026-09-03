<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function loginAs(string $role): User
    {
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function storeLead(array $overrides = []): void
    {
        $customer = Customer::create(['name' => 'PT Notifikasi']);

        $this->post(route('leads.store'), array_merge([
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'pt_group' => 'NTI',
            'segment' => 'end_user',
            'kebutuhan' => 'CCTV',
            'incoming_date' => now()->toDateString(),
        ], $overrides))->assertRedirect(route('leads.index'));
    }

    public function test_unassigned_new_lead_notifies_management_users_only(): void
    {
        Notification::fake();
        $this->actingAs($this->loginAs('marketing'));

        $managementUser = User::factory()->create();
        $managementUser->assignRole('management');

        $marketingUser = User::factory()->create();
        $marketingUser->assignRole('marketing');

        $this->storeLead();

        Notification::assertSentTo($managementUser, NewLeadNotification::class);
        Notification::assertNotSentTo($marketingUser, NewLeadNotification::class);
    }

    public function test_assigned_new_lead_does_not_notify_management(): void
    {
        Notification::fake();
        $this->actingAs($this->loginAs('marketing'));

        $managementUser = User::factory()->create();
        $managementUser->assignRole('management');

        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->storeLead(['assigned_to' => $sales->id]);

        Notification::assertNotSentTo($managementUser, NewLeadNotification::class);
    }

    public function test_management_can_mark_all_notifications_read(): void
    {
        $management = $this->loginAs('management');
        $lead = Lead::create([
            'customer_id' => Customer::create(['name' => 'PT Notif Lagi'])->id,
            'pt_group' => 'NTI',
            'segment' => 'end_user',
            'status' => 'new',
            'incoming_date' => now()->toDateString(),
        ]);

        $management->notify(new NewLeadNotification($lead));
        $this->assertSame(1, $management->unreadNotifications()->count());

        $this->actingAs($management)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $management->fresh()->unreadNotifications()->count());
    }
}