<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Semua user dengan akses ke divisi teknisi/sales boleh melihat daftar & detail project
     * (sesama teknisi boleh melihat project yang bukan miliknya, sesuai keputusan bisnis).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view-teknisi', 'manage-teknisi', 'view-sales']);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasAnyPermission(['view-teknisi', 'manage-teknisi', 'view-sales']);
    }

    public function create(User $user): bool
    {
        return $user->can('manage-teknisi');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('manage-teknisi');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('manage-teknisi');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->can('manage-admin');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->can('manage-admin');
    }
}
