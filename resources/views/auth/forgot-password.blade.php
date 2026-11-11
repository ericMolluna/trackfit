<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Recuperar contraseña - TrackFit</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-passwords.css') }}"
    >
</head>

<body>
    <main class="pw-page">
        <section class="pw-card" aria-labelledby="forgot-title">
            <p class="pw-brand">Track<span>Fit</span></p>

            <span class="pw-tag">Recupera el acceso</span>

            <h1 class="pw-title" id="forgot-title">
                ¿Olvidaste tu contraseña?
            </h1>

            <p class="pw-description">
                Introduce el correo de tu cuenta y te enviaremos
                un enlace para elegir una nueva contraseña.
            </p>

            @if (session('status'))
                <div class="pw-notice" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="pw-notice pw-errors" id="forgot-errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <label class="pw-label" for="email">
                    Correo electrónico
                </label>

                <input
                    class="pw-input"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="tu@email.com"
                    autocomplete="email"
                    required
                    autofocus
                    @if ($errors->has('email'))
                        aria-invalid="true"
                        aria-describedby="forgot-errors"
                    @endif
                >

                <button class="pw-button" type="submit">
                    Enviar enlace de recuperación
                </button>
            </form>

            <p class="pw-help">
                Si el mensaje no aparece, revisa spam o correo no deseado.
                Puede tardar unos minutos.
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