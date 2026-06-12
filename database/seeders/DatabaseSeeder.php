<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkType;

class WorkTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Demo Produk',
            'Survey',
            'POC',
            'Instalasi',
            'Troubleshooting',
            'Maintenance',
        ];

        foreach ($types as $type) {
            WorkType::create([
                'name' => $type
            ]);
        }
    }
}
