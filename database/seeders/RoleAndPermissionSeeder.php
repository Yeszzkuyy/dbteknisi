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

        foreach ($permissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        // === 2. Create Roles & Assign Permissions ===
        $marketing = Role::create(['name' => 'marketing', 'guard_name' => 'web']);
        $marketing->givePermissionTo([
            'manage-marketing', 'view-marketing',
            'view-sales', 'view-teknisi', 'view-admin', 'view-monitoring',
        ]);

        $sales = Role::create(['name' => 'sales', 'guard_name' => 'web']);
        $sales->givePermissionTo([
            'manage-sales', 'view-sales',
            'view-marketing', 'view-teknisi', 'view-admin', 'view-monitoring',
        ]);

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'manage-admin', 'view-admin',
            'view-marketing', 'view-sales', 'view-teknisi', 'view-monitoring',
        ]);

        $teknisi = Role::create(['name' => 'teknisi', 'guard_name' => 'web']);
        $teknisi->givePermissionTo([
            'manage-teknisi', 'view-teknisi',
            'view-marketing', 'view-sales', 'view-admin', 'view-monitoring',
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
    }
}
