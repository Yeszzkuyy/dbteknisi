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
    private const META_ACCOUNT_CODES = ['wa_wani'];

    public function isMeta(WhatsappAccount $account): bool
    {
        return in_array($account->account_code, self::META_ACCOUNT_CODES, true);
    }

    public function sendText(WhatsappAccount $account, string $number, string $text): ?string
    {
        if (!$this->configured($account)) {
            return null;
        }

        if ($this->isMeta($account)) {
            return $this->sendTextMeta($account, $number, $text);
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
     * Ambil status koneksi instance dari Green API (getStateInstance).
     * Mengembalikan stateInstance (mis. 'authorized') atau null bila gagal.
     */
    public function getState(WhatsappAccount $account): ?string
    {
        if (!$this->configured($account)) {
            return null;
        }

        if ($this->isMeta($account)) {
            return $this->getStateMeta($account);
        }

        $response = Http::timeout(20)->get(sprintf(
            '%s/waInstance%s/getStateInstance/%s',
            rtrim(config('whatsapp.base_url'), '/'),
            $account->gateway_instance,
            $account->gateway_token
        ));

        if ($response->failed()) {
            return null;
        }

        return $response->json('stateInstance');
    }

    private function sendTextMeta(WhatsappAccount $account, string $number, string $text): ?string
    {
        $response = Http::timeout(20)
            ->withToken($account->gateway_token)
            ->post(
                sprintf(
                    '%s/%s/%s/messages',
                    rtrim(config('whatsapp.meta.graph_base_url'), '/'),
                    config('whatsapp.meta.api_version'),
                    $account->gateway_instance
                ),
                [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $number,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $text,
                    ],
                ]
            );

        if ($response->failed()) {
            report(new \RuntimeException('Meta sendMessage gagal: ' . $response->body()));
            return null;
        }

        return data_get($response->json(), 'messages.0.id');
    }

    private function getStateMeta(WhatsappAccount $account): ?string
    {
        $response = Http::timeout(20)
            ->withToken($account->gateway_token)
            ->get(sprintf(
                '%s/%s/%s',
                rtrim(config('whatsapp.meta.graph_base_url'), '/'),
                config('whatsapp.meta.api_version'),
                $account->gateway_instance
            ));

        return $response->successful() ? 'authorized' : null;
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