<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanEdit
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->role->canEdit()) {
            return back()->with('error', 'Akun Prakerin hanya bisa melihat data, tidak bisa mengubah.');
        }

        return $next($request);
    }
}
