<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;

use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            ['name' => 'Budi Marketing', 'email' => 'marketing@dbteknisi.com', 'role' => 'marketing'],
            ['name' => 'Sari Sales', 'email' => 'sales@dbteknisi.com', 'role' => 'sales'],
            ['name' => 'Andi Admin', 'email' => 'admin@dbteknisi.com', 'role' => 'admin'],
            ['name' => 'Rudi Teknisi', 'email' => 'teknisi@dbteknisi.com', 'role' => 'teknisi'],
            ['name' => 'Dewi Teknisi', 'email' => 'teknisi2@dbteknisi.com', 'role' => 'teknisi'],
            ['name' => 'Pak Manager', 'email' => 'manager@dbteknisi.com', 'role' => 'manager'],
            ['name' => 'Bu Yanita', 'email' => 'yanita@dbteknisi.com', 'role' => 'management'],
            ['name' => 'Bu Ayu', 'email' => 'ayu@dbteknisi.com', 'role' => 'management'],
        ];

        $userIds = [];
        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => $password, 'role' => $u['role'], 'email_verified_at' => now()]
            );
            $user->assignRole($u['role']);
            $userIds[$u['role']][] = $user->id;
        }

        $customers = [
            ['name' => 'PT Telekomunikasi Maju Jaya', 'email' => 'info@telemaju.com', 'phone' => '021-5551234', 'address' => 'Jl. Jendral Sudirman No. 45, Jakarta', 'company' => 'PT Telekomunikasi Maju Jaya', 'status' => 'deal'],
            ['name' => 'CV Nusantara Net', 'email' => 'contact@nusantanet.co.id', 'phone' => '022-7654321', 'address' => 'Jl. Asia Afrika No. 10, Bandung', 'company' => 'CV Nusantara Net', 'status' => 'deal'],
            ['name' => 'PT Sinar Data Indah', 'email' => 'admin@sinardata.id', 'phone' => '031-8899776', 'address' => 'Jl. Pemuda No. 27, Surabaya', 'company' => 'PT Sinar Data Indah', 'status' => 'deal'],
            ['name' => 'Universitas Teknologi Cerdas', 'email' => 'it@utc.ac.id', 'phone' => '0274-565656', 'address' => 'Jl. Kaliurang KM 5, Yogyakarta', 'company' => 'Universitas Teknologi Cerdas', 'status' => 'deal'],
            ['name' => 'RS Harapan Sehat', 'email' => 'ga@harapansehat.com', 'phone' => '021-7788990', 'address' => 'Jl. Fatmawati No. 12, Jakarta', 'company' => 'RS Harapan Sehat', 'status' => 'lead'],
            ['name' => 'PT Agro Makmur Lestari', 'email' => 'it@agromakmur.co.id', 'phone' => '061-4567890', 'address' => 'Jl. Gatot Subroto No. 8, Medan', 'company' => 'PT Agro Makmur Lestari', 'status' => 'deal'],
        ];

        $customerIds = [];
        foreach ($customers as $c) {
            $customer = Customer::firstOrCreate(['email' => $c['email']], $c);
            $customerIds[] = $customer->id;
        }

        $statuses = ProjectStatus::pluck('id', 'name');
        $workTypes = \App\Models\WorkType::pluck('id')->values();
        $ams = \App\Models\AccountManager::pluck('id')->values();

        $projects = [
            ['name' => 'Instalasi Fiber Optic Kantor Pusat', 'code' => 'PRJ-2026-001', 'quo' => 'QUO-2026-001', 'customer' => 0, 'status' => 'On Progress', 'progress' => 60],
            ['name' => 'Upgrade Jaringan WiFi Kampus', 'code' => 'PRJ-2026-002', 'quo' => 'QUO-2026-002', 'customer' => 3, 'status' => 'Open', 'progress' => 10],
            ['name' => 'Maintenance Perangkat Server RS', 'code' => 'PRJ-2026-003', 'quo' => 'QUO-2026-003', 'customer' => 4, 'status' => 'Maintenance', 'progress' => 100],
            ['name' => 'POC SD-WAN Cabang Surabaya', 'code' => 'PRJ-2026-004', 'quo' => 'QUO-2026-004', 'customer' => 2, 'status' => 'On Progress', 'progress' => 35],
            ['name' => 'Instalasi CCTV Gudang Medan', 'code' => 'PRJ-2026-005', 'quo' => 'QUO-2026-005', 'customer' => 5, 'status' => 'Done', 'progress' => 100],
            ['name' => 'Redesign Topologi Jaringan Bandung', 'code' => 'PRJ-2026-006', 'quo' => 'QUO-2026-006', 'customer' => 1, 'status' => 'Hold', 'progress' => 20],
            ['name' => 'Aktivasi Internet Link Backup', 'code' => 'PRJ-2026-007', 'quo' => 'QUO-2026-007', 'customer' => 0, 'status' => 'Warranty', 'progress' => 100],
        ];

        // Observer Project mencatat aktivitas dengan Auth::id()
        \Illuminate\Support\Facades\Auth::loginUsingId($userIds['admin'][0]);

        foreach ($projects as $i => $p) {
            $project = Project::firstOrCreate(
                ['project_code' => $p['code']],
                [
                    'project_name' => $p['name'],
                    'quotation_number' => $p['quo'],
                    'customer_id' => $customerIds[$p['customer']],
                    'account_manager_id' => $ams[$i % $ams->count()],
                    'work_type_id' => $workTypes[$i % $workTypes->count()],
                    'project_status_id' => $statuses[$p['status']],
                    'progress' => $p['progress'],
                    'pic_engineer' => User::find($userIds['teknisi'][$i % count($userIds['teknisi'])])->name,
                    'sales_user_id' => $userIds['sales'][0],
                    'teknisi_user_id' => $userIds['teknisi'][$i % count($userIds['teknisi'])],
                    'start_date' => now()->subDays(30 - $i * 3),
                    'end_date' => now()->addDays(10 + $i * 5),
                    'description' => 'Data contoh hasil seeding.',
                ]
            );
        }
    }
}
