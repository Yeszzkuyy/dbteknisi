<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_meeting_and_followup_with_soft_deleted_customer_still_resolve(): void
    {
        $customer = Customer::create(['name' => 'PT Bina']);
        $meeting = Meeting::create(['customer_id' => $customer->id, 'meeting_date' => now()]);
        $followUp = FollowUp::create([
            'customer_id' => $customer->id,
            'meeting_id' => $meeting->id,
            'description' => 'Follow up lanjutan',
            'follow_up_date' => now(),
        ]);

        $customer->delete();

        $this->assertSame('PT Bina', $meeting->fresh()->customer->name);
        $this->assertSame('PT Bina', $followUp->fresh()->customer->name);
    }
}