<?php

namespace App\Notifications;

use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsappInboundNotification extends Notification
{
    use Queueable, SendsWebPush;

    public function __construct(
        public int $accountId,
        public string $accountName,
        public string $sender,
        public string $preview,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->withWebPush($notifiable, ['database']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'whatsapp',
            'account_id' => $this->accountId,
            'sender' => $this->sender,
            'customer' => trim($this->accountName.' • '.$this->sender, ' •'),
            'preview' => $this->preview,
            'url' => route('whatsapp-center.index'),
        ];
    }

    protected function pushContent(): array
    {
        return [
            'title' => 'Pesan WhatsApp baru',
            'body' => trim($this->accountName . ' • ' . $this->sender, ' •') . ': ' . $this->preview,
            'url' => route('whatsapp-center.index'),
        ];
    }
}
