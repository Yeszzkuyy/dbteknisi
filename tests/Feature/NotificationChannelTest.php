<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationChannelTest extends TestCase
{
    use RefreshDatabase;

    private function notification(): NewLeadNotification
    {
        return new NewLeadNotification(new Lead());
    }

    public function test_default_channels_are_database_only(): void
    {
        $user = User::factory()->create();

        $this->assertSame(['database'], $this->notification()->via($user));
    }

    public function test_email_channel_is_used_when_enabled(): void
    {
        $user = User::factory()->create();
        $user->preferences = ['notify_system' => false, 'notify_email' => true];
        $user->save();

        $this->assertSame(['mail'], $this->notification()->via($user));
    }

    public function test_no_channels_when_all_disabled(): void
    {
        $user = User::factory()->create();
        $user->preferences = ['notify_system' => false, 'notify_email' => false];
        $user->save();

        $this->assertSame([], $this->notification()->via($user));
    }
}