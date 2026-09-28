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

        // Ingat pemegang super-admin sebelum assignment dihapus
        // (guard: di DB fresh, role belum ada — lewati tanpa throw)
        $prevSuperAdmins = Role::where('name', 'super-admin')->exists()
            ? User::role('super-admin')->pluck('id')->all()
            : [];

        // Bersihkan data lama
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();

        // === 1. Create Permissions (pola manage-{divisi} & view-{divisi}) ===
        $divisi = ['marketing', 'sales', 'admin', 'technician', 'monitoring'];
        $permissions = [];

        foreach ($divisi as $d) {
            $permissions[] = "manage-{$d}";
            $permissions[] = "view-{$d}";
        }

        // Izin lintas divisi: semua role boleh lihat Customer & Trash tanpa membuka menu divisi lain
        $permissions[] = 'view-customer';
        $permissions[] = 'view-trash';

        // Monitoring anggota divisi (lead & manage tiap divisi)
        $permissions[] = 'monitor-marketing';
        $permissions[] = 'monitor-technical';

        // Hub Management: melihat & meng-assign lead + membuka placeholder Manage
        $permissions[] = 'manage-sales-leads';

        foreach ($permissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        // === 2. Create Roles & Assign Permissions ===
        $common = ['view-customer', 'view-trash'];

        $mk = fn (string $name, array $perms) => tap(
            Role::create(['name' => $name, 'guard_name' => 'web']),
            fn ($r) => $r->givePermissionTo($perms)
        );

        // Divisi
        $mk('marketing', ['manage-marketing', 'view-marketing', ...$common]);
        $mk('sales', ['manage-sales', 'view-sales', ...$common]);
        $mk('admin', ['manage-admin', 'view-admin', ...$common]);
        $mk('technician', ['manage-technician', 'view-technician', ...$common]);

        // Lead teknisi: 1 tingkat di atas technician biasa
        $mk('lead-technician', ['manage-technician', 'view-technician', 'monitor-technical', ...$common]);

        // Hub Management (satu permission bersama manage-sales-leads)
        $mk('management', ['manage-sales-leads', ...$common]);
        $mk('manage-marketing', ['manage-sales-leads', 'view-marketing', 'monitor-marketing', ...$common]);
        $mk('manage-technical', ['manage-sales-leads', 'view-technician', 'monitor-technical', ...$common]);
        $mk('manage-admin', ['manage-sales-leads', 'view-admin', ...$common]);

        // CEO: semua view, tanpa manage (read-only; enforcement menyusul)
        $mk('ceo', ['view-marketing', 'view-sales', 'view-admin', 'view-technician', 'view-monitoring', ...$common]);

        $mk('super-admin', Permission::pluck('name')->all());

        // === 3. Migrate Existing Users (jangan hapus user, hanya ganti role) ===
        $map = [
            'teknisi' => 'technician',
            'engineer' => 'technician',
            'marketing-lead' => 'manage-marketing',
            'manager' => 'ceo',
        ];

        foreach (User::withTrashed()->get() as $user) {
            $user->syncRoles([]);

            // Yeski selalu jadi super-admin
            if ($user->email === 'yehezkielmayogi.ptnti@gmail.com' || $user->id === 1) {
                $user->assignRole('super-admin');
                continue;
            }

            $oldRole = $user->getOriginal('role') ?? $user->role;
            $target = $map[$oldRole] ?? $oldRole;

            if ($target && Role::where('name', $target)->exists()) {
                $user->assignRole($target);
                // Selaraskan kolom role legacy agar query where('role', ...) tetap akurat
                if ($user->role !== $target) {
                    $user->forceFill(['role' => $target])->save();
                }
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
        User::where('role', 'super-admin')->get()
            ->each(fn (User $u) => $u->assignRole('super-admin'));

        // === 6. Kembalikan super-admin yang hilang akibat wipe (kolom role=guest tapi tadinya super-admin) ===
        User::whereIn('id', $prevSuperAdmins)->get()
            ->each(fn (User $u) => $u->hasRole('super-admin') ?: $u->assignRole('super-admin'));
    }
}
