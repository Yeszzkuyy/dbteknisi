<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_documents', function (Blueprint $table) {
            $table->id();

            $table->string('original_name');
            $table->string('category')->index();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('store_id');
            $table->string('provider_file_id')->nullable();
            $table->string('provider_document_id')->nullable();

            $table->string('mime');
            $table->unsignedBigInteger('size');

            $table->string('status')->default('indexing')->index();

            $table->timestamps();

            $table->index(['status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_documents');
    }
};