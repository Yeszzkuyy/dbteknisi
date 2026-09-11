<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categories = ['Dokumentasi', 'Lainnya'];
        foreach ($categories as $name) {
            DB::table('document_categories')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('document_categories')->whereIn('name', ['Dokumentasi', 'Lainnya'])->delete();
    }
};
