<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AccountManager;
use App\Models\WorkType;
use App\Models\Customer;
use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed User Bawaan (Manager & Teknisi)
        User::create([
            'name' => 'Yeski',
            'email' => 'yehezkielmayogileons@gmail.com',
            'password' => Hash::make('Yeszkuyy.105'),
            'role' => 'teknisi',
        ]);

        User::create([
            'name' => 'Teknisi Budi',
            'email' => 'budi@teknisi.com',
            'password' => Hash::make('password'),
            'role' => 'teknisi',
        ]);

        // 2. Seed Data Master Account Manager (AM)
        AccountManager::create(['name' => 'AM Ahmad Dhani']);
        AccountManager::create(['name' => 'AM Siti Nurhaliza']);
        AccountManager::create(['name' => 'AM Giring Ganesha']);

        // 3. Seed Data Master Jenis Pekerjaan (Work Type)
        WorkType::create(['name' => 'Survey Lapangan']);
        WorkType::create(['name' => 'Instalasi & Aktivasi']);
        WorkType::create(['name' => 'Proof of Concept (POC)']);
        WorkType::create(['name' => 'Maintenance & Troubleshooting']);

        // 4. Seed Data Contoh Klien (Customer)
        Customer::create([
            'name' => 'PT Telekomunikasi Maju Jaya',
            'address' => 'Jl. Jendral Sudirman No. 45, Jakarta',
            'phone' => '021-5551234',
            'email' => 'info@telemaju.com',
        ]);

        // 5. Seed Kategori Dokumen
        DocumentCategory::create(['name' => 'Scan BAST']);
        DocumentCategory::create(['name' => 'Quotation / BOQ']);
        DocumentCategory::create(['name' => 'Report Survey']);
        DocumentCategory::create(['name' => 'Report Instalasi']);
        DocumentCategory::create(['name' => 'Dokumen Pendukung']);
    }
}