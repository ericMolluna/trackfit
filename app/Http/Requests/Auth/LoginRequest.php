<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Autorizar la petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpiar el correo antes de validar: sin espacios y en minúsculas,
     * así " Eric@Gmail.com " y "eric@gmail.com" son la misma cuenta.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Mensajes de validación en español.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Introduce tu correo electrónico.',
            'email.string' => 'Introduce un correo electrónico válido.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'password.required' => 'Introduce tu contraseña.',
            'password.string' => 'Introduce una contraseña válida.',
        ];
    }

    /**
     * Intentar iniciar sesión.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Las cuentas creadas con Google o GitHub no tienen contraseña propia:
        // solo pueden entrar con su proveedor.
        $socialUser = User::where('email', $this->input('email'))
            ->whereNotNull('provider')
            ->first();

        if ($socialUser) {
            RateLimiter::hit($this->throttleKey());

            $name = $socialUser->provider === 'github' ? 'GitHub' : 'Google';

            throw ValidationException::withMessages([
                'social' => "Esta cuenta se creó con {$name}. Inicia sesión con el botón «Continuar con {$name}».",
            ]);
        }

        $authenticated = Auth::attempt(
            $this->only('email', 'password'),
            $this->boolean('remember')
        );

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            $emailExists = User::where(
                'email',
                $this->input('email')
            )->exists();

            if (! $emailExists) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo no está registrado en TrackFit.',
                ]);
            }

            throw ValidationException::withMessages([
                'password' => 'La contraseña no es correcta. Inténtalo de nuevo.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Comprobar el límite de intentos.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => "Has realizado demasiados intentos. Vuelve a intentarlo en {$seconds} segundos.",
        ]);
    }

    /**
     * Clave del límite de intentos: correo e IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('email')) . '|' . $this->ip()
        );
    }
}