<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Bersihkan data lama
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();

        // === 1. Create Permissions (pola manage-{divisi} & view-{divisi}) ===
        $divisi = ['marketing', 'sales', 'admin', 'teknisi', 'monitoring'];
        $permissions = [];

        foreach ($divisi as $d) {
            $permissions[] = "manage-{$d}";
            $permissions[] = "view-{$d}";
        }

        // Izin lintas divisi: semua role boleh lihat Customer & Trash tanpa membuka menu divisi lain
        $permissions[] = 'view-customer';
        $permissions[] = 'view-trash';

        // Monitoring anggota divisi (khusus lead divisi)
        $permissions[] = 'monitor-marketing';

        foreach ($permissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        // === 2. Create Roles & Assign Permissions ===
        // Tiap role: divisinya sendiri + Dashboard, Customer, Trash.
        // Monitoring hanya manager & super-admin.
        $common = ['view-customer', 'view-trash'];

        $marketing = Role::create(['name' => 'marketing', 'guard_name' => 'web']);
        $marketing->givePermissionTo([
            'manage-marketing', 'view-marketing', ...$common,
        ]);

        // Lead divisi marketing: akses penuh marketing + monitoring tim
        $marketingLead = Role::create(['name' => 'marketing-lead', 'guard_name' => 'web']);
        $marketingLead->givePermissionTo([
            'manage-marketing', 'view-marketing', 'monitor-marketing', ...$common,
        ]);

        $sales = Role::create(['name' => 'sales', 'guard_name' => 'web']);
        $sales->givePermissionTo([
            'manage-sales', 'view-sales', ...$common,
        ]);

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'manage-admin', 'view-admin', ...$common,
        ]);

        $teknisi = Role::create(['name' => 'teknisi', 'guard_name' => 'web']);
        $teknisi->givePermissionTo([
            'manage-teknisi', 'view-teknisi', ...$common,
        ]);

        $manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view-marketing', 'view-sales', 'view-admin', 'view-teknisi', 'view-monitoring',
        ]);

        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // === 3. Migrate Existing Users (jangan hapus user, hanya ganti role) ===
        // Pakai withTrashed() karena beberapa user mungkin sudah di-soft-delete
        foreach (User::withTrashed()->get() as $user) {
            $user->syncRoles([]);

            // User id=1 (Yeski) selalu jadi super-admin
            if ($user->id === 1) {
                $user->assignRole('super-admin');
                continue;
            }

            $oldRole = $user->getOriginal('role') ?? $user->role;

            if (in_array($oldRole, ['teknisi', 'engineer'])) {
                $user->assignRole('teknisi');
            } elseif ($oldRole === 'admin') {
                $user->assignRole('admin');
            } elseif ($oldRole === 'sales') {
                $user->assignRole('sales');
            } elseif ($oldRole === 'marketing') {
                $user->assignRole('marketing');
            } elseif ($oldRole === 'manager') {
                $user->assignRole('manager');
            }
        }

        // === 4. Buat 1 akun Super Admin baru (placeholder) ===
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@dbteknisi.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('gantiPassword123'),
            ]
        );
        $superAdminUser->assignRole('super-admin');

        // === 5. Kembalikan super-admin ke user yang punya kolom role=super-admin ===
        // (seeder ini menghapus semua assignment role di atas, jadi pulihkan di sini)
        User::where('role', 'super-admin')->get()
            ->each(fn (User $u) => $u->assignRole('super-admin'));
    }
}
