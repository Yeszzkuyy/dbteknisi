<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kepemilikan customer per akun WA company (isolasi antar-company).
     * - Kolom owner + klaim otomatis untuk nomor yang riwayat chat-nya hanya di 1 akun.
     * - Normalisasi nomor lama agar pencocokan lintas tabel konsisten.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('whatsapp_account_id')
                ->nullable()
                ->after('whatsapp')
                ->constrained('whatsapp_accounts')
                ->nullOnDelete();
        });

        DB::table('customers')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $numbers = array_values(array_unique(array_filter([
                    \App\Services\WhatsappGateway::normalizeNumber((string) ($row->whatsapp ?? '')),
                    \App\Services\WhatsappGateway::normalizeNumber((string) ($row->phone ?? '')),
                ])));

                $updates = [];
                if (($row->whatsapp ?? '') !== '' && \App\Services\WhatsappGateway::normalizeNumber((string) $row->whatsapp) !== $row->whatsapp) {
                    $updates['whatsapp'] = \App\Services\WhatsappGateway::normalizeNumber((string) $row->whatsapp);
                }
                if (($row->phone ?? '') !== '' && \App\Services\WhatsappGateway::normalizeNumber((string) $row->phone) !== $row->phone) {
                    $updates['phone'] = \App\Services\WhatsappGateway::normalizeNumber((string) $row->phone);
                }

                if ($numbers !== []) {
                    $accountIds = DB::table('whatsapp_messages')
                        ->whereIn('sender_number', $numbers)
                        ->distinct()->pluck('whatsapp_account_id')
                        ->merge(
                            DB::table('whatsapp_conversations')
                                ->whereIn('sender_number', $numbers)
                                ->distinct()->pluck('whatsapp_account_id')
                        )
                        ->unique()->values();

                    if ($accountIds->count() === 1) {
                        $updates['whatsapp_account_id'] = $accountIds->first();
                    }
                }

                if ($updates !== []) {
                    DB::table('customers')->where('id', $row->id)->update($updates);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('whatsapp_account_id');
        });
    }
};
