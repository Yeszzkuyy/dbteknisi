<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-marketing') || $user->can('manage-marketing');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view-marketing') || $user->can('manage-marketing');
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