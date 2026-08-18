<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dan tambahkan deleted_at ke project_tasks
        if (!Schema::hasColumn('project_tasks', 'deleted_at')) {
            Schema::table('project_tasks', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Cek dan tambahkan deleted_at ke project_supports
        if (!Schema::hasColumn('project_supports', 'deleted_at')) {
            Schema::table('project_supports', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Cek dan tambahkan deleted_at ke project_documents
        if (!Schema::hasColumn('project_documents', 'deleted_at')) {
            Schema::table('project_documents', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('project_supports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};