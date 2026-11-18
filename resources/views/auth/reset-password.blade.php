<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nueva contraseña - TrackFit</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-passwords.css') }}"
    >
</head>

<body>
    <main class="pw-page">
        <section class="pw-card" aria-labelledby="reset-title">
            <p class="pw-brand">
                Track<span>Fit</span>
            </p>

            <span class="pw-tag">
                Recupera tu cuenta
            </span>

            <h1 class="pw-title" id="reset-title">
                Elige tu nueva contraseña
            </h1>

            <p class="pw-description">
                Introduce una nueva contraseña y repítela para confirmar.
                Después podrás iniciar sesión con ella.
            </p>

            @if ($errors->any())
                <div
                    class="pw-notice pw-errors"
                    id="reset-errors"
                    role="alert"
                >
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input
                    type="hidden"
                    name="token"
                    value="{{ $token ?? request()->route('token') }}"
                >

                <label class="pw-label" for="email">
                    Correo electrónico
                </label>

                <input
                    class="pw-input"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', request()->query('email')) }}"
                    autocomplete="email"
                    required
                    @if ($errors->has('email'))
                        aria-invalid="true"
                        aria-describedby="reset-errors"
                    @endif
                >

                <label class="pw-label" for="password">
                    Nueva contraseña
                </label>

                <input
                    class="pw-input"
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="Introduce tu nueva contraseña"
                    required
                    autofocus
                    @if ($errors->has('password'))
                        aria-invalid="true"
                        aria-describedby="reset-errors"
                    @endif
                >

                @include('auth.partials.password-rules', ['for' => 'password'])

                <label class="pw-label" for="password_confirmation">
                    Repite la nueva contraseña
                </label>

                <input
                    class="pw-input"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Repite tu nueva contraseña"
                    required
                >

                <button class="pw-button" type="submit">
                    Guardar nueva contraseña
                </button>
            </form>

            <p class="pw-help">
                Elige una contraseña que no utilices en otras cuentas.
                No compartas tu contraseña ni el enlace de recuperación.
            </p>

            <div class="pw-footer">
                <a class="pw-link" href="{{ route('login') }}">
                    Volver a iniciar sesión
                </a>
            </div>
        </section>
    </main>
</body>
</html>