<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    public function deleted(Customer $customer): void
    {
        $customer->projects()->get()->each(fn ($project) => $project->delete());
        $customer->contacts()->get()->each(fn ($contact) => $contact->delete());
    }

    public function restored(Customer $customer): void
    {
        // ✅ Ganti onlyTrashed → withTrashed
        $customer->projects()->withTrashed()->get()->each(fn ($project) => $project->restore());
        $customer->contacts()->withTrashed()->get()->each(fn ($contact) => $contact->restore());
    }
}