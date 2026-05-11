<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // I-redirect ang mga guests sa 'welcome' route
        $middleware->redirectGuestsTo(fn (Request $request) => route('welcome'));

        // --- MAO NI IMONG I-ADD NGA LINE ---
        $middleware->alias([
            'role' => \App\Http\Middleware\MyCustomMiddleware::class,
        ]);
        // ------------------------------------
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();