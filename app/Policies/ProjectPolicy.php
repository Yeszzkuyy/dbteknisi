<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Semua role (termasuk Guest) boleh melihat daftar & detail project.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin');
    }
}