<?php

namespace App\Services;

use App\Ai\Agents\LeadNeedsSummarizer;
use App\Ai\Agents\WhatsappSalesBot;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsappBot
{
    /**
     * Balasan cadangan bila AI gagal, agar customer tidak dibiarkan tanpa respons.
     */
    private const FALLBACK_REPLY = 'Terima kasih sudah menghubungi 3DY Group. Pesan Anda sudah kami terima, tim sales akan segera membalas.';

    public function __construct(private WhatsappGateway $gateway)
    {
    }

    /**
     * Ringkas kebutuhan customer dari transkrip chat (untuk prefill form Lead).
     */
    public function summarizeNeeds(WhatsappAccount $account, string $sender): ?string
    {
        $transcript = $this->transcript($account, $sender, 20);

        if ($transcript === '') {
            return null;
        }

        try {
            $text = trim((string) (new LeadNeedsSummarizer)->prompt($transcript));
        } catch (Throwable $e) {
            Log::error('WhatsappBot summarize gagal: '.$e->getMessage());

            return null;
        }

        return $text !== '' ? mb_substr($text, 0, 500) : null;
    }

    /**
     * Balas pesan customer yang belum dijawab untuk semua akun yang bot-nya aktif.
     * Human takeover: percakapan yang baru dibalas manusia dalam 30 menit dilewati.
     */
    public function replyPending(int $maxPerAccount = 5): int
    {
        $processed = 0;

        $accounts = WhatsappAccount::where('is_active', true)
            ->where('bot_enabled', true)
            ->get()
            ->filter(fn ($a) => $this->gateway->configured($a));

        foreach ($accounts as $account) {
            foreach ($this->pendingSenders($account)->take($maxPerAccount) as $sender) {
                try {
                    if ($this->replyTo($account, $sender)) {
                        $processed++;
                    }
                } catch (Throwable $e) {
                    Log::error('WhatsappBot reply gagal: '.$e->getMessage(), [
                        'account' => $account->id,
                        'sender' => $sender,
                    ]);
                }
            }
        }

        return $processed;
    }

    /**
     * Nomor pengirim yang menunggu balasan: pesan masuk terakhir lebih baru
     * dari balasan terakhir, masih dalam 24 jam, dan tidak sedang ditangani manusia.
     *
     * @return Collection<int, string>
     */
    public function pendingSenders(WhatsappAccount $account): Collection
    {
        $rows = WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->orderBy('id')
            ->get()
            ->groupBy('sender_number');

        return $rows->filter(function ($messages) {
            $last = $messages->last();
            $lastInbound = $messages->where('direction', 'inbound')->last();

            if (! $lastInbound || $last->direction !== 'inbound') {
                return false;
            }

            if ($lastInbound->created_at->lt(now()->subDay())) {
                return false;
            }

            // Human takeover: ada balasan manual (bukan bot) dalam 30 menit terakhir.
            return ! $messages->where('direction', 'outbound')
                ->where('is_bot', false)
                ->last()?->created_at?->gt(now()->subMinutes(30));
        })->keys()->map(fn ($sender) => (string) $sender)->values();
    }

    public function replyTo(WhatsappAccount $account, string $sender): bool
    {
        $reply = $this->generateReply($account, $sender);

        if (blank($reply)) {
            return false;
        }

        $message = WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'sender_number' => $sender,
            'sender_name' => WhatsappMessage::where('whatsapp_account_id', $account->id)
                ->where('sender_number', $sender)->first()?->sender_name,
            'message_body' => $reply,
            'direction' => 'outbound',
            'status' => 'queued',
            'is_bot' => true,
        ]);

        $gatewayId = $this->gateway->sendText($account, $sender, $reply);

        $message->update([
            'gateway_message_id' => $gatewayId,
            'status' => $gatewayId ? 'sent' : 'failed',
        ]);

        return $gatewayId !== null;
    }

    public function generateReply(WhatsappAccount $account, string $sender): ?string
    {
        $transcript = $this->transcript($account, $sender, 15);

        if ($transcript === '') {
            return null;
        }

        try {
            $text = trim((string) (new WhatsappSalesBot)->prompt($transcript));
        } catch (Throwable $e) {
            Log::error('WhatsappBot generateReply gagal: '.$e->getMessage());

            return self::FALLBACK_REPLY;
        }

        return $text !== '' ? mb_substr($text, 0, 1000) : self::FALLBACK_REPLY;
    }

    private function transcript(WhatsappAccount $account, string $sender, int $limit): string
    {
        return WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('sender_number', $sender)
            ->latest('id')
            ->limit($limit)
            ->get()
            ->sortBy('id')
            ->map(function (WhatsappMessage $m) {
                $who = $m->direction === 'inbound' ? 'Customer' : 'Kami';
                $text = trim((string) $m->message_body);

                return $text !== '' ? "{$who}: {$text}" : null;
            })
            ->filter()
            ->implode("\n");
    }
}
