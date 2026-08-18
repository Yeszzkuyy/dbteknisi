<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // STEP 1: Tambahkan kolom dulu
            $table->foreignId('project_status_id')
                ->nullable()
                ->after('status')
                ->constrained('project_statuses')
                ->nullOnDelete();
        });

        // STEP 2: Baru update data (DI LUAR Schema::table)
        // Ambil default status 'Open'
        $defaultStatus = DB::table('project_statuses')
            ->where('name', 'Open')
            ->first();

        if ($defaultStatus) {
            DB::table('projects')
                ->whereNull('project_status_id')
                ->update(['project_status_id' => $defaultStatus->id]);
        }

        // Update berdasarkan mapping status string → id
        $statuses = DB::table('project_statuses')->get();
        foreach ($statuses as $status) {
            DB::table('projects')
                ->where('status', $status->name)
                ->update(['project_status_id' => $status->id]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_status_id']);
            $table->dropColumn('project_status_id');
        });
    }
};