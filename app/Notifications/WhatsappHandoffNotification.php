<?php

namespace App\Notifications;

use App\Notifications\Concerns\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsappHandoffNotification extends Notification
{
    use Queueable, SendsWebPush;

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
        return $this->withWebPush($notifiable, ['database']);
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

    protected function pushContent(): array
    {
        $reason = $this->reason === 'limit' ? 'Bot mencapai batas balasan' : 'Bot menunggu ambil alih';

        return [
            'title' => 'Butuh handoff WhatsApp',
            'body' => $reason . ' — ' . trim($this->accountName . ' • ' . $this->sender, ' •'),
            'url' => route('whatsapp-center.index'),
        ];
    }
}
