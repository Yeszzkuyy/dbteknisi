<?php

namespace App\Notifications\Concerns;

use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Channel database/push/email mengikuti preferensi pengguna
 * (Profil > Setting > Notifikasi).
 *
 * notify_email adalah master switch: bila dimatikan, tidak ada
 * notifikasi sama sekali (lihat muted()). Kelas memakai trait ini
 * cukup mengimplementasikan pushContent() ['title', 'body', 'url'].
 * Channel aktif bila preferensi notify_push / notify_email user
 * menyala (default: true).
 */
trait SendsWebPush
{
    /** Email mati = tidak ada notifikasi sama sekali. */
    protected function muted(object $notifiable): bool
    {
        return $notifiable->preference('notify_email', true) === false;
    }

    protected function withWebPush(object $notifiable, array $channels): array
    {
        if ($notifiable->preference('notify_push', true)) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    protected function withMail(object $notifiable, array $channels): array
    {
        if ($notifiable->preference('notify_email', true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /** @return array{title: string, body: string, url: string} */
    abstract protected function pushContent(): array;

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $content = $this->pushContent();

        return (new WebPushMessage)
            ->title($content['title'])
            ->body($content['body'])
            ->icon(url('/favicon.png'))
            ->badge(url('/favicon.png'))
            ->data(['url' => $content['url']])
            ->action('Lihat', 'open');
    }
}
