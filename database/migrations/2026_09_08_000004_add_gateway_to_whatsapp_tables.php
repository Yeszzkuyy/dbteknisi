<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->string('gateway_instance')->nullable()->after('account_code');
            $table->string('gateway_token')->nullable()->after('gateway_instance');
            $table->string('gateway_status')->nullable()->after('gateway_token');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->string('status')->nullable()->after('direction');
            $table->string('gateway_message_id')->nullable()->unique()->after('wa_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->dropColumn(['gateway_instance', 'gateway_token', 'gateway_status']);
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn(['status', 'gateway_message_id']);
        });
    }
};