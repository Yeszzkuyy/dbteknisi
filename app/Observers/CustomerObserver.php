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
        $customer->projects()->onlyTrashed()->get()->each(fn ($project) => $project->restore());
        $customer->contacts()->onlyTrashed()->get()->each(fn ($contact) => $contact->restore());
    }
}