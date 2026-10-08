<?php

namespace App\Notifications;

use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/** Pengingat sore: sales punya aktivitas hari ini tapi belum mencatat meeting. */
class DailyMeetingReminderNotification extends Notification
{
    use Queueable, SendsWebPush;

    public function via(object $notifiable): array
    {
        if ($this->muted($notifiable)) {
            return [];
        }

        return $this->withWebPush($notifiable, ['database']);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'meeting-reminder',
            'title' => __('Daily meeting update'),
            'customer' => $notifiable->name,
            'preview' => __('You have activity today but no meeting logged yet.'),
            'url' => route('sales.follow-ups.index'),
        ];
    }

    protected function pushContent(): array
    {
        return [
            'title' => __('Daily meeting update'),
            'body' => __('You have activity today but no meeting logged yet.'),
            'url' => route('sales.follow-ups.index'),
        ];
    }
}
