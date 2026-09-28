<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'view-marketing', 'manage-marketing',
            'view-sales', 'manage-sales',
            'manage-teknisi', 'manage-admin',
        ]);
    }

    public function view(User $user, Lead $lead): bool
    {
        if ($user->can('view-marketing') || $user->can('manage-marketing')) {
            return true;
        }

        // Sales hanya boleh melihat lead yang di-assign kepadanya.
        if ($user->can('view-sales') || $user->can('manage-sales')) {
            return (int) $lead->assigned_to === (int) $user->id;
        }

        // Fallback teknisi/admin agar bisa convert bila sales lupa.
        return $user->can('manage-teknisi') || $user->can('manage-admin');
    }

    public function create(User $user): bool
    {
        return $user->can('manage-marketing');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('manage-marketing');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('manage-marketing');
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('manage-marketing');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('manage-marketing');
    }
}