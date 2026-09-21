<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsappHandoffNotification extends Notification
{
    use Queueable;

    /**
     * @param  'limit'|'waiting'  $reason
     */
    public function __construct(
        public int $accountId,
        public string $accountName,
        public string $sender,
        public string $reason,
        public string $preview = '',
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'whatsapp',
            'reason' => $this->reason,
            'account_id' => $this->accountId,
            'sender' => $this->sender,
            'customer' => trim($this->accountName.' • '.$this->sender, ' •'),
            'preview' => $this->preview,
            'url' => route('whatsapp-center.index'),
        ];
    }
}
