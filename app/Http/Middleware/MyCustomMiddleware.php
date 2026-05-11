<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MyCustomMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check kung logged in ug kung ang iyang role naa sa listahan sa $roles
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}