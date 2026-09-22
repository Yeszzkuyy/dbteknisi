<?php

namespace App\Notifications;

use App\Models\TechnicianSchedule;
use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/** Dikirim ke teknisi saat jadwal baru di-assign kepadanya. */
class TechnicianScheduleAssignedNotification extends Notification
{
    use Queueable, SendsWebPush;

    public function __construct(public TechnicianSchedule $schedule) {}

    public function via(object $notifiable): array
    {
        return $this->withWebPush($notifiable, ['database']);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'schedule',
            'schedule_id' => $this->schedule->id,
            'customer' => $this->schedule->title,
            'preview' => $this->schedule->start_at->format('d M Y H:i'),
            'url' => route('teknisi.jadwal'),
        ];
    }

    protected function pushContent(): array
    {
        return [
            'title' => 'Jadwal teknisi baru',
            'body' => $this->schedule->title . ' — ' . $this->schedule->start_at->format('d M Y H:i'),
            'url' => route('teknisi.jadwal'),
        ];
    }
}
