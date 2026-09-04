<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Project;
use App\Observers\CustomerObserver;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super admin melewati semua cek permission/role.
        Gate::before(fn ($user, $ability) => $user->hasRole('super-admin') ? true : null);

        Customer::observe(CustomerObserver::class);
        Project::observe(ProjectObserver::class);

        // Pencarian seragam: case-insensitive + partial match + auto-trim.
        EloquentBuilder::macro('whereLike', function ($columns, $search) {
            $term = '%'.mb_strtolower(trim((string) $search)).'%';
            $columns = (array) $columns;

            return $this->where(function ($q) use ($columns, $term) {
                $q->whereRaw('LOWER('.$columns[0].') LIKE ?', [$term]);
                foreach (array_slice($columns, 1) as $column) {
                    $q->orWhereRaw('LOWER('.$column.') LIKE ?', [$term]);
                }
            });
        });
    }
}