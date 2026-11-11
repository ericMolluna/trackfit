<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TrackFit - Iniciar sesión</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-auth.css') }}"
    >

    <style>
        .tf-login .tf-input {
            width: 100%;
            padding: 12px 16px;
            box-sizing: border-box;
        }

        .tf-login .tf-input--invalid {
            border-color: #fb7185;
        }

        .tf-login .tf-input--invalid:focus {
            border-color: #fb7185;
            box-shadow: 0 0 0 3px rgba(251, 113, 133, 0.14);
        }

        .tf-login-notice {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 22px 0;
            padding: 17px;
            border: 1px solid #80404f;
            border-radius: 14px;
            background: #2b161f;
            text-align: left;
        }

        .tf-login-notice-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #512332;
            color: #fecdd3;
            font-size: 16px;
            font-weight: 700;
        }

        .tf-login-notice-content {
            min-width: 0;
        }

        .tf-login-notice-title {
            margin: 0 0 7px;
            color: #ffe4e6;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.4;
        }

        .tf-login-notice-text {
            margin: 0;
            color: #e4bdc8;
            font-size: 13px;
            line-height: 1.7;
        }

        .tf-login-notice-action {
            display: inline-block;
            margin-top: 12px;
            color: #e2ccff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .tf-login-notice-action:hover {
            color: #fff;
        }

        .tf-login .auth-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        .tf-login-remember {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--muted, #b8b8c7);
            font-size: 13px;
            cursor: pointer;
        }

        .tf-login-remember input {
            margin: 0;
            accent-color: #7c3aed;
        }

        .tf-login-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: var(--muted, #b8b8c7);
            font-size: 13px;
        }

        .tf-login a:focus-visible,
        .tf-login button:focus-visible {
            outline: 2px solid #c4b5fd;
            outline-offset: 4px;
        }
    </style>
</head>

<body>
    <main class="tf-auth tf-login">
        <section class="tf-card">
            <h1 class="tf-title">TrackFit</h1>

            <p class="tf-sub">
                Inicia sesión para continuar
            </p>

            @php
                $emailNotRegistered = in_array(
                    'Este correo no está registrado en TrackFit.',
                    $errors->get('email'),
                    true
                );

                $wrongPassword = in_array(
                    'La contraseña no es correcta. Inténtalo de nuevo.',
                    $errors->get('password'),
                    true
                );

                $rateLimited = $errors->has('login');
            @endphp

            @if ($errors->any())
                <div
                    class="tf-login-notice"
                    id="login-error"
                    role="alert"
                >
                    <span
                        class="tf-login-notice-icon"
                        aria-hidden="true"
                    >
                        !
                    </span>

                    <div class="tf-login-notice-content">
                        @if ($rateLimited)
                            <h2 class="tf-login-notice-title">
                                Demasiados intentos
                            </h2>

                            <p class="tf-login-notice-text">
                                {{ $errors->first('login') }}
                            </p>

                        @elseif ($emailNotRegistered)
                            <h2 class="tf-login-notice-title">
                                Este correo no está registrado
                            </h2>

                            <p class="tf-login-notice-text">
                                Revisa que lo hayas escrito bien.
                                Si todavía no tienes cuenta en TrackFit,
                                puedes crear una.
                            </p>

                            @if (Route::has('register'))
                                <a
                                    class="tf-login-notice-action"
                                    href="{{ route('register') }}"
                                >
                                    Crear una cuenta
                                </a>
                            @endif

                        @elseif ($wrongPassword)
                            <h2 class="tf-login-notice-title">
                                La contraseña no es correcta
                            </h2>

                            <p class="tf-login-notice-text">
                                Inténtalo de nuevo o recupera tu contraseña
                                si no la recuerdas.
                            </p>

                            @if (Route::has('password.request'))
                                <a
                                    class="tf-login-notice-action"
                                    href="{{ route('password.request') }}"
                                >
                                    Recuperar contraseña
                                </a>
                            @endif

                        @else
                            <h2 class="tf-login-notice-title">
                                Revisa los datos
                            </h2>

                            @foreach ($errors->all() as $error)
                                <p class="tf-login-notice-text">
                                    {{ $error }}
                                </p>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label class="tf-label" for="email">
                    Email
                </label>

                <input
                    class="tf-input {{ $errors->has('email') ? 'tf-input--invalid' : '' }}"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    autofocus
                    @if ($errors->has('email'))
                        aria-invalid="true"
                        aria-describedby="login-error"
                    @endif
                >

                <label class="tf-label" for="password">
                    Contraseña
                </label>

                <input
                    class="tf-input {{ $errors->has('password') ? 'tf-input--invalid' : '' }}"
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    @if ($errors->has('password'))
                        aria-invalid="true"
                        aria-describedby="login-error"
                    @endif
                >

                <div class="auth-row">
                    <label class="tf-login-remember">
                        <input
                            type="checkbox"
                            name="remember"
                            @checked(old('remember'))
                        >

                        Recuérdame
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            class="auth-link"
                            href="{{ route('password.request') }}"
                        >
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button class="auth-btn" type="submit">
                    Entrar
                </button>

                @if (Route::has('register'))
                    <div class="tf-login-footer">
                        <span>¿No tienes cuenta?</span>

                        <a
                            class="auth-mini-btn"
                            href="{{ route('register') }}"
                        >
                            Crear cuenta
                        </a>
                    </div>
                @endif
            </form>
        </section>
    </main>
</body>
</html>