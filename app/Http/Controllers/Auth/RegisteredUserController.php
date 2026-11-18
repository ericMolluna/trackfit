<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PasswordRules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        // El correo se guarda siempre sin espacios y en minúsculas.
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

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
                    // Si el correo ya pertenece a una cuenta de Google,
                    // mostramos un aviso específico en lugar del genérico.
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $linkedWithGoogle = User::query()
                            ->where('email', mb_strtolower((string) $value))
                            ->where('provider', 'google')
                            ->exists();

                        if ($linkedWithGoogle) {
                            $fail('Esta cuenta ya está vinculada con Google.');
                        }
                    },
                    'unique:' . User::class,
                ],
                'password' => [
                    'required',
                    'confirmed',
                    // Requisitos de contraseña segura (app/Support/PasswordRules.php):
                    // mínimo 8 caracteres, mayúscula, minúscula y número.
                    PasswordRules::rule(),
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

            ] + PasswordRules::messages()
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            // Si el correo de verificación falla, no bloqueamos el registro:
            // el usuario puede reenviarlo desde la pantalla de verificación.
            report($e);
        }

        Auth::login($user);

        // Recién creada: vamos directos a la pantalla de verificación
        // (sin el aviso de "cuenta no verificada").
        return redirect(route('verification.notice', absolute: false));
    }
}