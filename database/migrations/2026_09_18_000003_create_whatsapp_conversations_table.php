<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->string('sender_number');
            $table->string('mode')->default('bot'); // bot | human
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('bot_turns')->default(0);
            $table->text('needs_summary')->nullable();
            $table->timestamp('taken_over_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();

            $table->unique(['whatsapp_account_id', 'sender_number'], 'wa_conversations_unique');
        });

        // Seed percakapan lama: kalau balasan keluar terakhir dari manusia
        // (bukan bot), tandai mode human agar bot tidak menyerobot.
        $pairs = DB::table('whatsapp_messages')
            ->select('whatsapp_account_id', 'sender_number')
            ->distinct()
            ->get();

        foreach ($pairs as $pair) {
            $lastOutbound = DB::table('whatsapp_messages')
                ->where('whatsapp_account_id', $pair->whatsapp_account_id)
                ->where('sender_number', $pair->sender_number)
                ->where('direction', 'outbound')
                ->orderByDesc('id')
                ->first();

            DB::table('whatsapp_conversations')->insert([
                'whatsapp_account_id' => $pair->whatsapp_account_id,
                'sender_number' => $pair->sender_number,
                'mode' => ($lastOutbound && ! $lastOutbound->is_bot) ? 'human' : 'bot',
                'bot_turns' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
