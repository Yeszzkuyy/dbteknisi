<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('sales_user_id')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->foreignId('teknisi_user_id')->nullable()->after('sales_user_id')->constrained('users')->nullOnDelete();
            $table->index(['sales_user_id', 'teknisi_user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['sales_user_id', 'teknisi_user_id']);
            $table->dropConstrainedForeignId('sales_user_id');
            $table->dropConstrainedForeignId('teknisi_user_id');
        });
    }
};