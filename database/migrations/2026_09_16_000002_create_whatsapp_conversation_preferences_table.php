<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversation_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->string('sender_number');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'whatsapp_account_id', 'sender_number'], 'wa_conversation_preferences_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversation_preferences');
    }
};
