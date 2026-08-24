<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('segment')->nullable()->after('customer_id');
            $table->string('source')->nullable()->change();
            $table->renameColumn('expected_close_date', 'incoming_date');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('opportunity_value');
            $table->text('kebutuhan')->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('opportunity_value', 15, 2)->nullable();
            $table->dropColumn('kebutuhan');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('segment');
            $table->enum('source', ['website', 'referral', 'cold_call', 'email', 'social_media', 'event', 'other'])->nullable()->change();
            $table->renameColumn('incoming_date', 'expected_close_date');
        });
    }
};
