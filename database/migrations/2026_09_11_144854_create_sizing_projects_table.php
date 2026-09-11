<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sizing_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('sales_pic')->nullable();
            $table->text('customer_needs')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->text('topology')->nullable();
            $table->text('technical_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizing_projects');
    }
};
