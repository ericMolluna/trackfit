<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar servicios.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(
            function (object $notifiable, string $url): MailMessage {
                return (new MailMessage)
                    ->subject('¡Bienvenido a TrackFit! Verifica tu correo')
                    ->view('emails.verify-email', [
                        'name' => $notifiable->name,
                        'verificationUrl' => $url,
                        'expiresIn' => config('auth.verification.expire', 60),
                    ]);
            }
        );

        ResetPassword::toMailUsing(
            function (object $notifiable, string $token): MailMessage {
                $url = route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);

                $broker = config('auth.defaults.passwords');

                return (new MailMessage)
                    ->subject('Recupera tu contraseña de TrackFit')
                    ->view('emails.reset-password', [
                        'name' => $notifiable->name,
                        'resetUrl' => $url,
                        'expiresIn' => config(
                            "auth.passwords.{$broker}.expire",
                            60
                        ),
                    ]);
            }
        );
    }
}