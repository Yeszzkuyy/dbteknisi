<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-monitoring');
    }

    public function index()
    {
        $users = User::with('roles')->paginate(15);
        $roles = Role::with('permissions')->get();
        
        // Sample audit log data since we don't have the model yet
        $auditLogs = collect([]);
        
        return view('admin-panel.index', compact('users', 'roles', 'auditLogs'));
    }

    // ========== USER MANAGEMENT ==========
    
    public function createUser()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view('admin-panel.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->filled('roles')) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('admin-panel.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function editUser(User $user)
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view('admin-panel.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => 'array',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin-panel.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('admin-panel.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // ========== ROLE MANAGEMENT ==========
    
    public function createRole()
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            $prefix = explode('-', $p->name)[0];
            return $prefix === 'manage' || $prefix === 'view' ? $prefix : 'other';
        });
        return view('admin-panel.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->filled('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('admin-panel.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function editRole(Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403, 'Role super-admin tidak bisa diedit.');
        }
        
        $permissions = Permission::all()->groupBy(function ($p) {
            $prefix = explode('-', $p->name)[0];
            return $prefix === 'manage' || $prefix === 'view' ? $prefix : 'other';
        });
        return view('admin-panel.roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403, 'Role super-admin tidak bisa diedit.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin-panel.index')
            ->with('success', 'Role berhasil diupdate.');
    }

    public function destroyRole(Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403, 'Role super-admin tidak bisa dihapus.');
        }
        
        $role->delete();
        return redirect()->route('admin-panel.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    // ========== AUDIT LOG ==========
    
    public function auditLog()
    {
        // Placeholder for audit log - would need AuditLog model
        return view('admin-panel.audit-log');
    }
}