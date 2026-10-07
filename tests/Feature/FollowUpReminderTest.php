<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\FollowUpOverdueNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpReminderTest extends TestCase
{
    use RefreshDatabase;

    private function salesUser(): User
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('sales');

        return $user;
    }

    public function test_overdue_followup_notifies_assigned_sales(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::create(['name' => 'PT Telat']);
        $lead = Lead::create([
            'customer_id' => $customer->id,
            'pt_group' => 'NTI',
            'segment' => 'vendor',
            'status' => 'warm',
            'assigned_to' => $sales->id,
        ]);
        $followUp = FollowUp::create([
            'customer_id' => $customer->id,
            'lead_id' => $lead->id,
            'description' => 'Follow up penawaran',
            'follow_up_date' => now()->subDay()->toDateString(),
            'created_by' => $sales->id,
        ]);

        $this->artisan('followups:remind')->assertSuccessful();

        $this->assertNotNull($followUp->fresh()->reminder_sent_at);
        $this->assertTrue(
            $sales->notifications()->where('type', FollowUpOverdueNotification::class)->exists()
        );

        // Idempoten: jalan kedua tidak mengirim lagi.
        $this->artisan('followups:remind')->assertSuccessful();
        $this->assertSame(1, $sales->notifications()->where('type', FollowUpOverdueNotification::class)->count());
    }

    public function test_future_followup_gets_no_reminder(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::create(['name' => 'PT Besok']);
        FollowUp::create([
            'customer_id' => $customer->id,
            'description' => 'Follow up besok',
            'follow_up_date' => now()->addDay()->toDateString(),
            'created_by' => $sales->id,
        ]);

        $this->artisan('followups:remind')->assertSuccessful();

        $this->assertSame(0, $sales->notifications()->count());
    }
}
