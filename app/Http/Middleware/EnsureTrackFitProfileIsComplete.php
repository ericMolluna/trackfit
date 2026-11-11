<?php

namespace App\Http\Middleware;

use App\Models\NutritionProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrackFitProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si por algún motivo no hay usuario autenticado,
        // dejamos que el middleware auth se encargue.
        if (!$user) {
            return $next($request);
        }

        $hasProfile = NutritionProfile::where('user_id', $user->id)->exists();

        /*
         * Si todavía no ha configurado su perfil,
         * lo enviamos al onboarding inicial.
         *
         * Evitamos redirigir si ya está dentro del setup
         * para no crear un bucle infinito.
         */
        if (!$hasProfile && !$request->routeIs('nutrition.setup*')) {
            return redirect()
                ->route('nutrition.setup')
                ->with(
                    'info',
                    'Completa tus datos para personalizar TrackFit.'
                );
        }

        return $next($request);
    }
}