<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    /**
     * Channel mengikuti preferensi notifikasi pengguna
     * (Profil > Setting > Notifikasi).
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->preference('notify_system', true)) {
            $channels[] = 'database';
        }
        if ($notifiable->preference('notify_email', false)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'customer' => $this->lead->customer?->name ?? 'Lead baru',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Lead Baru Diterima')
            ->line('Lead baru masuk dan membutuhkan penanganan:')
            ->line('Customer: ' . ($this->lead->customer?->name ?? 'Lead baru'))
            ->action('Kelola Lead', url(route('manage-sales.edit', $this->lead->id)))
            ->line('Terima kasih.');
    }
}