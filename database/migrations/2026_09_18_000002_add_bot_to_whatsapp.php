<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->boolean('bot_enabled')->default(true)->after('is_active');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->dropColumn('bot_enabled');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }
};
