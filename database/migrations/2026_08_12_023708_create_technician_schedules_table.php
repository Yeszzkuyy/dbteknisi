<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('technician_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('status')->default('scheduled');
            $table->unsignedInteger('reminder_minutes')->nullable();
            $table->string('google_event_id')->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->string('google_sync_status')->default('not_connected');
            $table->text('google_sync_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_at', 'end_at']);
        });

        Schema::create('google_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('calendar_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_credentials');
        Schema::dropIfExists('technician_schedules');
    }
};
