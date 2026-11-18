<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Inicio de sesión con Google y GitHub.
 *
 * Cada cuenta tiene un único método de acceso:
 * - Una cuenta creada con Google solo puede entrar con Google
 *   (el login con correo y contraseña la rechaza).
 * - Una cuenta creada con correo y contraseña no se puede abrir con Google,
 *   para que nadie pueda quedarse con ella cambiando el método de acceso.
 */
class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'github'];

    private const NAMES = ['google' => 'Google', 'github' => 'GitHub'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        if (! $this->configured($provider)) {
            return $this->fail('El acceso con '.self::NAMES[$provider].' no está disponible en este momento.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        $name = self::NAMES[$provider];

        if (request()->has('error') || ! $this->configured($provider)) {
            return $this->fail("No se ha podido iniciar sesión con {$name}. Inténtalo de nuevo.");
        }

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            report($e);

            return $this->fail("No se ha podido iniciar sesión con {$name}. Inténtalo de nuevo.");
        }

        $email = Str::lower(trim((string) $social->getEmail()));
        $providerId = (string) $social->getId();
        if ($providerId === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->fail("{$name} no ha compartido tu correo electrónico.");
        }

        if ($provider === 'google' && ($social->getRaw()['email_verified'] ?? null) !== true) {
            return $this->fail('Google no ha confirmado este correo.');
        }

        // 1) Cuenta ya vinculada a este proveedor.
        $user = User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($user && Str::lower($user->email) !== $email) {
            return $this->fail('El correo del proveedor no coincide con el de tu cuenta.');
        }

        // 2) Cuenta con el mismo correo.
        if (! $user) {
            $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if ($existing) {
                return $this->fail($existing->provider
                        ? 'Este correo ya está registrado con '.(self::NAMES[$existing->provider] ?? 'otro método').'. Inicia sesión con ese método.'
                        : 'Este correo ya está registrado con contraseña. Inicia sesión con tu correo y contraseña.');
            }
        }

        // Google confirma el correo; para GitHub se mantiene la verificación de TrackFit.
        if (! $user) {
            $user = new User([
                'name' => $social->getName() ?: ($social->getNickname() ?: Str::before($email, '@')),
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $providerId,
            ]);
            $user->password = null;
            $user->email_verified_at = $provider === 'google' ? now() : null;
            $user->save();
            if ($provider !== 'google') {
                try {
                    event(new Registered($user));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        } elseif ($provider === 'google' && ! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function configured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"));
    }

    private function fail(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['social' => $message]);
    }
}
