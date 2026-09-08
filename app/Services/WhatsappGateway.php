<?php

namespace App\Services;

use App\Models\WhatsappAccount;
use Illuminate\Support\Facades\Http;

class WhatsappGateway
{
    public function configured(WhatsappAccount $account): bool
    {
        return filled($account->gateway_instance) && filled($account->gateway_token);
    }

    /**
     * Kirim pesan teks via Green API. Mengembalikan idMessage dari gateway,
     * atau null bila akun belum dikonfigurasi / gagal terkirim.
     */
    public function sendText(WhatsappAccount $account, string $number, string $text): ?string
    {
        if (!$this->configured($account)) {
            return null;
        }

        $response = Http::timeout(20)
            ->post(
                sprintf(
                    '%s/waInstance%s/sendMessage/%s',
                    rtrim(config('whatsapp.base_url'), '/'),
                    $account->gateway_instance,
                    $account->gateway_token
                ),
                [
                    'chatId' => $this->chatId($number),
                    'message' => $text,
                ]
            );

        if ($response->failed()) {
            report(new \RuntimeException('Green API sendMessage gagal: ' . $response->body()));
            return null;
        }

        return data_get($response->json(), 'idMessage');
    }

    /**
     * Ambil satu notifikasi antrean dari Green API (polling).
     * Mengembalikan ['receiptId' => ..., 'payload' => [...]] atau null bila kosong.
     */
    public function receive(WhatsappAccount $account): ?array
    {
        if (!$this->configured($account)) {
            return null;
        }

        $response = Http::timeout(20)->get(sprintf(
            '%s/waInstance%s/receiveNotification/%s',
            rtrim(config('whatsapp.base_url'), '/'),
            $account->gateway_instance,
            $account->gateway_token
        ));

        if ($response->failed() || !$response->json()) {
            return null;
        }

        $receiptId = data_get($response->json(), 'receiptId');

        if (!$receiptId) {
            return null;
        }

        return [
            'receiptId' => $receiptId,
            'payload' => data_get($response->json(), 'body', []),
        ];
    }

    /**
     * Konfirmasi notifikasi sudah diproses (deleteNotification).
     */
    public function acknowledge(WhatsappAccount $account, int|string $receiptId): void
    {
        if (!$this->configured($account)) {
            return;
        }

        Http::timeout(20)->delete(sprintf(
            '%s/waInstance%s/deleteNotification/%s/%s',
            rtrim(config('whatsapp.base_url'), '/'),
            $account->gateway_instance,
            $account->gateway_token,
            $receiptId
        ));
    }

    private function chatId(string $number): string
    {
        return preg_replace('/\D/', '', $number) . '@c.us';
    }
}