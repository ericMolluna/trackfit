<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

/**
 * Reglas de contraseña segura de TrackFit, en un solo sitio.
 *
 * Se usan al crear la cuenta, al restablecer la contraseña
 * y al cambiarla desde el perfil.
 */
class PasswordRules
{
    /**
     * Mínimo 8 caracteres, una mayúscula, una minúscula y un número.
     */
    public static function rule(): Password
    {
        return Password::min(8)->mixedCase()->numbers();
    }

    /**
     * Mensajes en español. $field es el nombre del campo (normalmente "password").
     */
    public static function messages(string $field = 'password'): array
    {
        return [
            "{$field}.required" => 'Introduce una contraseña.',
            "{$field}.confirmed" => 'Las contraseñas no coinciden.',
            "{$field}.min" => 'Contraseña poco segura: debe tener al menos :min caracteres.',
            "{$field}.password.mixed" => 'Contraseña poco segura: necesita al menos una mayúscula y una minúscula.',
            "{$field}.password.numbers" => 'Contraseña poco segura: necesita al menos un número.',
        ];
    }
}
