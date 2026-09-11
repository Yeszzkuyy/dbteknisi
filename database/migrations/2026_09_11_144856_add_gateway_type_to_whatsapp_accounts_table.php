<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->string('gateway_type')->default('green_api')->after('account_code');
        });

        DB::table('whatsapp_accounts')
            ->where('account_code', 'wa_wani')
            ->update(['gateway_type' => 'meta']);
    }

    public function down(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->dropColumn('gateway_type');
        });
    }
};