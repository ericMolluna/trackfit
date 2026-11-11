<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Render (y otros hostings) sirven la app detrás de un proxy HTTPS.
        // Sin esto, Laravel genera enlaces http:// y el navegador bloquea el CSS.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'trackfit.profile' => \App\Http\Middleware\EnsureTrackFitProfileIsComplete::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();