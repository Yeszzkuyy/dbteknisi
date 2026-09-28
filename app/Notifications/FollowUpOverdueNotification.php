<?php

namespace App\Notifications;

use App\Models\FollowUp;
use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/** Pengingat follow-up yang tanggalnya sudah lewat dan belum ditindaklanjuti. */
class FollowUpOverdueNotification extends Notification
{
    use Queueable, SendsWebPush;

    public function __construct(public FollowUp $followUp) {}

    public function via(object $notifiable): array
    {
        if ($this->muted($notifiable)) {
            return [];
        }

        return $this->withWebPush($notifiable, ['database']);
    }

    public function toDatabase(object $notifiable): array
    {
        $customer = $this->followUp->customer?->name ?? __('Follow up');

        return [
            'type' => 'followup',
            'title' => __('Follow up jatuh tempo'),
            'follow_up_id' => $this->followUp->id,
            'customer' => $customer,
            'preview' => Str::limit($this->followUp->description, 80),
            'url' => route('sales.follow-ups.show', $this->followUp),
        ];
    }

    protected function pushContent(): array
    {
        return [
            'title' => __('Follow up jatuh tempo'),
            'body' => ($this->followUp->customer?->name ?? '').' — '.Str::limit($this->followUp->description, 80),
            'url' => route('sales.follow-ups.show', $this->followUp),
        ];
    }
}
