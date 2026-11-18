<?php

namespace App\Models\Concerns;

/**
 * Cifra el identificador del modelo en las URL.
 *
 * En lugar de /rutinas/5/editar la URL queda como /rutinas/Xk3...9/editar.
 * El valor está cifrado y firmado (AES-256-GCM con la APP_KEY): si alguien
 * lo modifica a mano, no se puede descifrar y la página responde 404.
 */
trait HasEncryptedRouteKey
{
    public function getRouteKey(): string
    {
        return static::encodeRouteKey((int) $this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = static::decodeRouteKey((string) $value);

        return $id ? $this->where($this->getKeyName(), $id)->first() : null;
    }

    public static function encodeRouteKey(int $id): string
    {
        $iv = random_bytes(12);
        $cipher = openssl_encrypt(
            static::class.'|'.$id,
            'aes-256-gcm',
            static::routeKeySecret(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return rtrim(strtr(base64_encode($iv.$tag.$cipher), '+/', '-_'), '=');
    }

    public static function decodeRouteKey(?string $value): ?int
    {
        if (! is_string($value) || $value === '' || strlen($value) > 200) {
            return null;
        }

        $raw = base64_decode(strtr($value, '-_', '+/'), true);

        if ($raw === false || strlen($raw) < 29 || rtrim(strtr(base64_encode($raw), '+/', '-_'), '=') !== $value) {
            return null;
        }

        $plain = openssl_decrypt(
            substr($raw, 28),
            'aes-256-gcm',
            static::routeKeySecret(),
            OPENSSL_RAW_DATA,
            substr($raw, 0, 12),
            substr($raw, 12, 16)
        );

        if ($plain === false || ! str_starts_with($plain, static::class.'|')) {
            return null;
        }

        $id = substr($plain, strlen(static::class) + 1);

        return ctype_digit($id) ? (int) $id : null;
    }

    protected static function routeKeySecret(): string
    {
        return hash('sha256', 'trackfit-url|'.config('app.key'), true);
    }
}
