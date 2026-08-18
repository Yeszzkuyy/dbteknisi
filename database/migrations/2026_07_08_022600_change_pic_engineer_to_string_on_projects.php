<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['pic_engineer_id']);
            
            // Hapus kolom pic_engineer_id
            $table->dropColumn('pic_engineer_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Tambah kolom pic_engineer (string)
            $table->string('pic_engineer')->nullable()->after('account_manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('pic_engineer');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('pic_engineer_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};