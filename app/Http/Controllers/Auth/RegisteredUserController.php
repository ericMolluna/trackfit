<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar el formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Crear la cuenta y lanzar la verificación de correo.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'bail',
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:' . User::class,
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
                ],
            ],
            [
                'name.required' => 'Introduce tu nombre.',
                'name.string' => 'Introduce un nombre válido.',
                'name.max' => 'El nombre no puede superar los 255 caracteres.',

                'email.required' => 'Introduce tu correo electrónico.',
                'email.string' => 'Introduce un correo válido.',
                'email.lowercase' => 'Escribe el correo en minúsculas.',
                'email.email' => 'Introduce un correo electrónico válido.',
                'email.max' => 'El correo no puede superar los 255 caracteres.',
                'email.unique' => 'Este correo ya está registrado.',

                'password.required' => 'Introduce una contraseña.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}