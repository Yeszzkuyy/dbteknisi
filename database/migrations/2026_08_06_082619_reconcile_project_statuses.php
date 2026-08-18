<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Samakan nama status yang mismatch dengan enum kolom status lama
        DB::table('project_statuses')->where('name', 'Progress')->update(['name' => 'On Progress']);
        DB::table('project_statuses')->where('name', 'Cancel')->update(['name' => 'Cancelled']);

        // 2. Backfill project_status_id yang null, cocokkan by name
        DB::table('projects')->whereNull('project_status_id')->get()
            ->each(function ($project) {
                $status = DB::table('project_statuses')->where('name', $project->status)->first();
                if ($status) {
                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['project_status_id' => $status->id]);
                }
            });
    }

    public function down(): void
    {
        DB::table('project_statuses')->where('name', 'On Progress')->update(['name' => 'Progress']);
        DB::table('project_statuses')->where('name', 'Cancelled')->update(['name' => 'Cancel']);
    }
};
