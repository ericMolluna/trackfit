<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as BaseEnsureEmailIsVerified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

/**
 * Igual que el middleware "verified" de Laravel, pero si alguien sin verificar
 * intenta abrir una página privada (por ejemplo, escribiendo la URL),
 * le avisamos de que su cuenta todavía no está verificada.
 */
class EnsureEmailIsVerified extends BaseEnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $request->user();

        if (! $user || ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())) {
            if ($request->expectsJson()) {
                abort(403, 'Esta cuenta no está verificada.');
            }

            return Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'))
                ->with('unverified', true);
        }

        return $next($request);
    }
}
