<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom baru (nullable dulu, biar data lama nggak error constraint)
        Schema::table('project_documents', function (Blueprint $table) {
            $table->foreignId('document_category_id')
                  ->nullable()
                  ->constrained('document_categories')
                  ->nullOnDelete();
        });
    
        // 2. Petakan data lama ke kategori baru
        DB::table('project_documents')->where('document_type', 'Photo')
            ->update(['document_category_id' => 5]); // Dokumen Pendukung
    
        DB::table('project_documents')->where('document_type', 'PO')
            ->update(['document_category_id' => 2]); // Quotation
    
        DB::table('project_documents')->where('document_type', 'Survey Report')
            ->update(['document_category_id' => 3]); // Report Survey
    
        // 3. Drop kolom lama
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });
    }
    
    public function down(): void
    {
        // Kembalikan kolom lama
        Schema::table('project_documents', function (Blueprint $table) {
            $table->string('document_type')->nullable();
        });
    
        // Petakan balik dari category_id ke teks (best effort)
        DB::table('project_documents')->where('document_category_id', 5)
            ->update(['document_type' => 'Photo']);
    
        DB::table('project_documents')->where('document_category_id', 2)
            ->update(['document_type' => 'PO']);
    
        DB::table('project_documents')->where('document_category_id', 3)
            ->update(['document_type' => 'Survey Report']);
    
        // Drop kolom baru
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn('document_category_id');
        });
    }
};
