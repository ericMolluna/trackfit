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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Eliminar la cuenta después de validar la contraseña.
     */
    public function destroy(Request $request): RedirectResponse
    {
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

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}