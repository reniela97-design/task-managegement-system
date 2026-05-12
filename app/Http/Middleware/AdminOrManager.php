<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->hasRole('Administrator') && !auth()->user()->hasRole('Manager'))) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}