<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'BAST',
            'Quotation',
            'Report Survey',
            'Report Instalasi',
            'Dokumen Pendukung',
        ];

        foreach ($categories as $category) {
            DocumentCategory::firstOrCreate(['name' => $category]);
        }
    }
}