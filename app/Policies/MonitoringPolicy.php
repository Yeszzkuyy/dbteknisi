<?php

namespace App\Policies;

use App\Models\User;

class MonitoringPolicy
{
    public function viewMonitoring(User $user): bool
    {
        return $user->can('view-monitoring') || $user->can('manage-monitoring');
    }

    public function manageMonitoring(User $user): bool
    {
        return $user->can('manage-monitoring');
    }
}