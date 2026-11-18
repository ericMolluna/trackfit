<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Mostrar el formulario de recuperación.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar el enlace de recuperación.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate(
            [
                'email' => ['required', 'string', 'email'],
            ],
            [
                'email.required' => 'Introduce tu correo electrónico.',
                'email.string' => 'Introduce un correo electrónico válido.',
                'email.email' => 'Introduce un correo electrónico válido.',
            ]
        );

        $socialUser = \App\Models\User::where('email', $request->input('email'))
            ->whereNotNull('provider')
            ->first();

        if ($socialUser) {
            $name = $socialUser->provider === 'github' ? 'GitHub' : 'Google';

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Esta cuenta se creó con {$name} y no tiene contraseña. Inicia sesión con el botón «Continuar con {$name}»."]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'Te hemos enviado un enlace para cambiar tu contraseña. Revisa tu bandeja de entrada y la carpeta de spam.'
            );
        }

        $message = match ($status) {
            Password::RESET_THROTTLED =>
                'Ya has solicitado un enlace hace poco. Espera un minuto antes de solicitar otro.',

            Password::INVALID_USER =>
                'No encontramos ninguna cuenta con ese correo electrónico.',

            default =>
                'No hemos podido enviar el enlace. Inténtalo de nuevo más tarde.',
        };

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $message,
            ]);
    }
}