<?php

namespace App\Notifications\Concerns;

use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Tambahkan channel Web Push (VAPID) ke notifikasi.
 *
 * Kelas memakai trait ini cukup mengimplementasikan pushContent()
 * ['title', 'body', 'url']. Channel aktif bila preferensi
 * notify_push user menyala (default: true).
 */
trait SendsWebPush
{
    protected function withWebPush(object $notifiable, array $channels): array
    {
        if ($notifiable->preference('notify_push', true)) {
            $channels[] = WebPushChannel::class;
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
