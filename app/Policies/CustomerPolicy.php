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
        return $user->can('manage-admin');
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->can('manage-admin');
    }
}
