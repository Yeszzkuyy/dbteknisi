<?php

namespace App\Console\Commands;

use App\Services\WhatsappBot;
use Illuminate\Console\Command;

class WhatsappBotReply extends Command
{
    protected $signature = 'whatsapp:bot';

    protected $description = 'Balas otomatis pesan WhatsApp customer yang belum dijawab (bot)';

    public function handle(WhatsappBot $bot): int
    {
        $count = $bot->replyPending();

        $this->info("Bot membalas {$count} percakapan.");

        return self::SUCCESS;
    }
}
