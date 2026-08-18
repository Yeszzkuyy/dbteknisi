<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Seeder;

class CustomerDataMigrationSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::withTrashed()->get();

        foreach ($customers as $customer) {
            // company = name (karena name saat ini berisi nama perusahaan)
            $customer->company = $customer->name;

            // Tentukan status dari project terkait
            $projects = Project::withTrashed()
                ->where('customer_id', $customer->id)
                ->get();

            if ($projects->isEmpty()) {
                $customer->status = 'lead';
            } elseif ($projects->filter(fn ($p) => $p->status?->name === 'Done')->isNotEmpty()) {
                $customer->status = 'selesai';
            } else {
                $customer->status = 'instalasi';
            }

            $customer->save();
        }
    }
}
