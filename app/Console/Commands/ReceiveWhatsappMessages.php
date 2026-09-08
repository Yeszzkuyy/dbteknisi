<?php

namespace App\Console\Commands;

use App\Http\Controllers\WhatsAppCenterController;
use App\Models\WhatsappAccount;
use App\Services\WhatsappGateway;
use Illuminate\Console\Command;

class ReceiveWhatsappMessages extends Command
{
    protected $signature = 'whatsapp:receive';

    protected $description = 'Tarik pesan masuk & status dari Green API (polling receiveNotification) untuk semua akun';

    public function handle(WhatsappGateway $gateway, WhatsAppCenterController $controller): int
    {
        $accounts = WhatsappAccount::where('is_active', true)->get()
            ->filter(fn ($a) => $gateway->configured($a));

        if ($accounts->isEmpty()) {
            $this->warn('Tidak ada akun WhatsApp dengan kredensial gateway terisi.');

            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($accounts as $account) {
            for ($i = 0; $i < 50; $i++) {
                $notification = $gateway->receive($account);

                if (!$notification) {
                    break;
                }

                $controller->handleNotification($notification['payload']);
                $gateway->acknowledge($account, $notification['receiptId']);
                $processed++;
            }
        }

        $this->info("Diproses {$processed} notifikasi.");

        return self::SUCCESS;
    }
}