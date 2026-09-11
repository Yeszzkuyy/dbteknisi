<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->text('sales_request')->nullable();
            $table->date('survey_date')->nullable();
            $table->string('location')->nullable();
            $table->string('pic')->nullable();
            $table->text('survey_data')->nullable();
            $table->json('documentation')->nullable();
            $table->text('survey_report')->nullable();
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
