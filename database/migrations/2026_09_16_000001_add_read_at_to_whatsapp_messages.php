<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('status');
            $table->index(['whatsapp_account_id', 'sender_number', 'read_at']);
        });

        // Preserve the previous unread rule for messages that were already answered.
        DB::table('whatsapp_messages')
            ->select(['id', 'whatsapp_account_id', 'sender_number', 'direction'])
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($message) => $message->whatsapp_account_id . ':' . $message->sender_number)
            ->each(function ($messages) {
                $lastOutboundId = $messages->where('direction', 'outbound')->max('id');

                if (!$lastOutboundId) {
                    return;
                }

                DB::table('whatsapp_messages')
                    ->where('whatsapp_account_id', $messages->first()->whatsapp_account_id)
                    ->where('sender_number', $messages->first()->sender_number)
                    ->where('direction', 'inbound')
                    ->where('id', '<', $lastOutboundId)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropIndex(['whatsapp_account_id', 'sender_number', 'read_at']);
            $table->dropColumn('read_at');
        });
    }
};
