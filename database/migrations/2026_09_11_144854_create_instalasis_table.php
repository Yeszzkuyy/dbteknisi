<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instalasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('location')->nullable();
            $table->string('technician_pic')->nullable();
            $table->date('schedule_date')->nullable();
            $table->string('job_status')->default('');
            $table->json('checklist')->nullable();
            $table->json('documentation')->nullable();
            $table->text('installation_report')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instalasis');
    }
};
