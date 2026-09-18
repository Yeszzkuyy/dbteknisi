<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalisasi nomor pengirim lama agar format konsisten (mis. 0812... -> 62812...).
     * Tanpa ini, satu kontak bisa terpecah menjadi beberapa percakapan.
     */
    public function up(): void
    {
        DB::table('whatsapp_messages')
            ->select('id', 'sender_number')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $normalized = \App\Services\WhatsappGateway::normalizeNumber((string) $row->sender_number);

                    if ($normalized !== $row->sender_number) {
                        DB::table('whatsapp_messages')
                            ->where('id', $row->id)
                            ->update(['sender_number' => $normalized]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Tidak dapat dibalik dengan aman (data lama tidak menyimpan format asal).
    }
};
