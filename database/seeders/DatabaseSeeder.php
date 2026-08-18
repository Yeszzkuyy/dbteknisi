<?php

namespace Database\Seeders;

use App\Models\AccountManager;
use App\Models\WorkType;
use App\Models\Customer;
use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Role & Permission + migrasi user existing
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Seed Data Master
        AccountManager::firstOrCreate(['name' => 'AM Ahmad Dhani']);
        AccountManager::firstOrCreate(['name' => 'AM Siti Nurhaliza']);
        AccountManager::firstOrCreate(['name' => 'AM Giring Ganesha']);

        WorkType::firstOrCreate(['name' => 'Survey Lapangan']);
        WorkType::firstOrCreate(['name' => 'Instalasi & Aktivasi']);
        WorkType::firstOrCreate(['name' => 'Proof of Concept (POC)']);
        WorkType::firstOrCreate(['name' => 'Maintenance & Troubleshooting']);

        Customer::firstOrCreate(
            ['email' => 'info@telemaju.com'],
            [
                'name' => 'PT Telekomunikasi Maju Jaya',
                'address' => 'Jl. Jendral Sudirman No. 45, Jakarta',
                'phone' => '021-5551234',
            ]
        );

        DocumentCategory::firstOrCreate(['name' => 'Scan BAST']);
        DocumentCategory::firstOrCreate(['name' => 'Quotation / BOQ']);
        DocumentCategory::firstOrCreate(['name' => 'Report Survey']);
        DocumentCategory::firstOrCreate(['name' => 'Report Instalasi']);
        DocumentCategory::firstOrCreate(['name' => 'Dokumen Pendukung']);
    }
}
