<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad que se añaden a todas las páginas.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Nadie puede meter TrackFit dentro de un iframe (evita "clickjacking").
        $response->headers->set('X-Frame-Options', 'DENY');
        // El navegador no "adivina" tipos de archivo.
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // No enviamos la URL completa a otras webs.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // La app no usa cámara, micrófono ni ubicación.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // En el servidor (HTTPS) obligamos a usar siempre HTTPS.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Las páginas privadas no se guardan en caché: al cerrar sesión,
        // el botón "atrás" no enseña datos del usuario.
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
