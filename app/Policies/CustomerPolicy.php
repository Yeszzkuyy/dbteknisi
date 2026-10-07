<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view-customer', 'view-sales', 'manage-sales']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasAnyPermission(['view-customer', 'view-sales', 'manage-sales']);
    }

    public function create(User $user): bool
    {
        return $user->can('manage-sales');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can('manage-sales');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('manage-sales');
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $this->ownsTrash($user, $customer);
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $this->ownsTrash($user, $customer);
    }

    /**
     * Owner data ATAU super-admin. deleted_by tidak memberi hak.
     */
    private function ownsTrash(User $user, Customer $customer): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $owner = $customer->trashOwnerId();

        return $owner !== null && (int) $owner === (int) $user->id;
    }
}
