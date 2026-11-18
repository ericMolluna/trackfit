<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualizar la información del perfil.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // El correo de una cuenta de Google o GitHub lo gestiona el proveedor.
        if (filled($request->user()->provider)) {
            unset($data['email']);
        }

        $request->user()->fill($data);

        $emailChanged = $request->user()->isDirty('email');

        if ($emailChanged) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Si cambia el correo, hay que verificar el nuevo: enviamos el enlace.
        if ($emailChanged) {
            try {
                $request->user()->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Eliminar la cuenta después de validar la contraseña.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Las cuentas de Google o GitHub no tienen contraseña:
        // se confirma escribiendo el correo de la cuenta.
        if (filled($request->user()->provider)) {
            $request->validateWithBag(
                'userDeletion',
                ['confirm_email' => ['required', 'string']],
                ['confirm_email.required' => 'Escribe tu correo para confirmar.']
            );

            if (mb_strtolower(trim($request->input('confirm_email'))) !== mb_strtolower($request->user()->email)) {
                return back()->withErrors(
                    ['confirm_email' => 'El correo no coincide con el de tu cuenta.'],
                    'userDeletion'
                );
            }
        } else {
            $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'string',
                    'current_password',
                ],
            ],
            [
                'password.required' => 'Introduce tu contraseña para confirmar.',
                'password.string' => 'Introduce una contraseña válida.',
                'password.current_password' => 'La contraseña no es correcta. Inténtalo de nuevo.',
            ]
        );
        }

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}