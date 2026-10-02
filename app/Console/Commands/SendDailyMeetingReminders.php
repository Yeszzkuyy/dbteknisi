<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\Meeting;
use App\Models\MeetingDraft;
use App\Models\User;
use App\Notifications\DailyMeetingReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyMeetingReminders extends Command
{
    protected $signature = 'meetings:remind-daily';

    protected $description = 'Ingatkan sales yang beraktivitas hari ini tapi belum mencatat meeting.';

    public function handle(): int
    {
        $sent = 0;

        foreach (User::role('sales')->get() as $sales) {
            if ($sales->can('manage-sales-leads') || $sales->can('manage-marketing')) {
                continue;
            }

            $hasActivity = FollowUp::where('created_by', $sales->id)
                ->whereDate('created_at', today())
                ->exists()
                || Meeting::where('created_by', $sales->id)
                    ->whereDate('created_at', today())
                    ->exists();

            if (! $hasActivity) {
                continue;
            }

            $alreadyLogged = Meeting::where('created_by', $sales->id)
                    ->whereDate('meeting_date', today())
                    ->exists()
                || MeetingDraft::pending()->ownedBy($sales->id)
                    ->whereDate('created_at', today())
                    ->exists();

            if ($alreadyLogged) {
                continue;
            }

            try {
                $sales->notify(new DailyMeetingReminderNotification);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Reminder daily meeting gagal: '.$e->getMessage());
            }
        }

        $this->info("Reminder terkirim: {$sent}.");

        return self::SUCCESS;
    }
}
