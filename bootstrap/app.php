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

        // Cabeceras de seguridad en todas las páginas y cierre de las otras
        // sesiones abiertas cuando el usuario cambia su contraseña.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->alias([
            'trackfit.profile' => \App\Http\Middleware\EnsureTrackFitProfileIsComplete::class,
            // Aviso de "cuenta no verificada" al entrar por URL sin verificar.
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Si el formulario lleva mucho rato abierto la sesión caduca (error 419),
        // y si se repite muchas veces seguidas salta el límite (error 429).
        // En vez de una página de error en inglés, volvemos al formulario con un aviso.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            // 429 = demasiados intentos seguidos (límite "throttle" de las rutas).
            $messages = [
                419 => 'La página ha caducado por seguridad. Vuelve a intentarlo.',
                429 => 'Has hecho demasiados intentos seguidos. Espera un minuto y vuelve a probar.',
            ];

            if (! isset($messages[$e->getStatusCode()]) || $request->expectsJson()) {
                return null;
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', 'current_password', '_token'))
                ->withErrors(['session' => $messages[$e->getStatusCode()]]);
        });
    })->create();