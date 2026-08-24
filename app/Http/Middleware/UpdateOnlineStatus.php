<?php

namespace App\Http\Middleware;

use App\Support\OnlineStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            OnlineStatus::touch($user->id);
        }

        return $next($request);
    }
}
