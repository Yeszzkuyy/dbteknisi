<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->index('lead_id');
        });

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });
    }
};
