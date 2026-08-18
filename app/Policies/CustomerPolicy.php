<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Sementara semua user bisa lihat
    }

    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->hasRole('Super Admin');
    }
}