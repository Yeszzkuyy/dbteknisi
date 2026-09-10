<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the authenticated user's language preference for the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->preference('locale', 'id');

        if (is_string($locale) && in_array($locale, ['id', 'en'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
