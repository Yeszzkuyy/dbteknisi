<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Project;
use App\Observers\CustomerObserver;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bangun URL mengikuti skema & host permintaan saat ini (http/https keduanya
        // berfungsi). Bila dibiarkan, asset/route memakai APP_URL yang bisa berbeda
        // skema/host dari request sehingga muncul masalah mixed-content.
        $this->bindRequestScheme();

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

    /**
     * Set root URL ke skema + host dari request aktual (http atau https),
     * sehingga seluruh asset/route ikut skema tersebut. Mengikuti header
     * Forwarded/X-Forwarded-Proto agar benar di belakang proxy/ngrok.
     */
    private function bindRequestScheme(): void
    {
        $this->app->afterResolving(\Illuminate\Contracts\Http\Kernel::class, function () {
            $request = request();
            $secure = $request->isSecure();
            $scheme = $secure ? 'https' : 'http';
            $host = $request->getHost();

            URL::forceRootUrl($scheme.'://'.$host);

            // Cookie session: hanya tandai secure saat koneksi https, supaya login
            // tetap jalan baik via http maupun https.
            config(['session.secure' => $secure]);
        });
    }
}