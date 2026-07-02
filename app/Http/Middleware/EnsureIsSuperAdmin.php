<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->id !== 1) {
            return back()->with('error', 'Halaman ini hanya bisa diakses oleh Super Admin.');
        }

        return $next($request);
    }
}
