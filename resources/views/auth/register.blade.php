<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TrackFit - Crear cuenta</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-auth.css') }}"
    >

    <style>
        .tf-register .tf-input {
            width: 100%;
            padding: 12px 16px;
            box-sizing: border-box;
        }

        .tf-register .tf-input--invalid {
            border-color: #fb7185;
        }

        .tf-field-error {
            margin: 8px 0 0;
            color: #fda4af;
            font-size: 13px;
            line-height: 1.5;
        }

        .tf-register-notice {
            margin: 20px 0;
            padding: 18px;
            border: 1px solid #7150a8;
            border-radius: 14px;
            background: #20152f;
        }

        .tf-register-notice h2 {
            margin: 0 0 8px;
            color: #f0e8ff;
            font-size: 16px;
            line-height: 1.4;
        }

        .tf-register-notice p {
            margin: 0;
            color: #d2c6e4;
            font-size: 14px;
            line-height: 1.6;
        }

        .tf-register-login {
            display: inline-block;
            margin-top: 14px;
            padding: 10px 16px;
            border-radius: 10px;
            background: #7c3aed;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .tf-register-login:hover {
            background: #6d28d9;
        }

        .tf-register-footer {
            margin-top: 18px;
            color: var(--muted, #b8b8c7);
            font-size: 13px;
        }

        .tf-register a:focus-visible {
            outline: 2px solid #c4b5fd;
            outline-offset: 4px;
        }

    </style>
</head>

<body>
    <main class="tf-auth tf-register">
        <section class="tf-card">
            <h1 class="tf-title">Crear cuenta</h1>

            <p class="tf-sub">
                Empieza a registrar tus entrenamientos y tu alimentación
            </p>

            @php
                $emailLinkedWithGoogle = in_array(
                    'Esta cuenta ya está vinculada con Google.',
                    $errors->get('email'),
                    true
                );

                $emailAlreadyRegistered = $emailLinkedWithGoogle || in_array(
                    'Este correo ya está registrado.',
                    $errors->get('email'),
                    true
                );
            @endphp

            @error('session')
                <div class="tf-register-notice" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @enderror

            @if ($emailAlreadyRegistered)
                <div
                    class="tf-register-notice"
                    role="alert"
                    id="registered-email-notice"
                >
                    @if ($emailLinkedWithGoogle)
                        <h2>Esta cuenta ya está vinculada con Google</h2>

                        <p>Entra con el botón «Continuar con Google».</p>

                        <a
                            class="tf-register-login"
                            href="{{ route('social.redirect', 'google') }}"
                        >
                            Continuar con Google
                        </a>
                    @else
                        <h2>Este correo ya está registrado</h2>

                        <p>¿Quieres iniciar sesión?</p>

                        <a
                            class="tf-register-login"
                            href="{{ route('login') }}"
                        >
                            Iniciar sesión
                        </a>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <label class="tf-label" for="name">
                    Nombre
                </label>

                <input
                    class="tf-input {{ $errors->has('name') ? 'tf-input--invalid' : '' }}"
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    maxlength="255"
                    required
                    autofocus
                    @if ($errors->has('name'))
                        aria-invalid="true"
                        aria-describedby="name-error"
                    @endif
                >

                @error('name')
                    <p class="tf-field-error" id="name-error">
                        {{ $message }}
                    </p>
                @enderror

                <label class="tf-label" for="email">
                    Email
                </label>

                <input
                    class="tf-input {{ $errors->has('email') ? 'tf-input--invalid' : '' }}"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    maxlength="255"
                    required
                    @if ($errors->has('email'))
                        aria-invalid="true"
                    @endif
                    @if ($emailAlreadyRegistered)
                        aria-describedby="registered-email-notice"
                    @elseif ($errors->has('email'))
                        aria-describedby="email-error"
                    @endif
                >

                @if (! $emailAlreadyRegistered)
                    @error('email')
                        <p class="tf-field-error" id="email-error">
                            {{ $message }}
                        </p>
                    @enderror
                @endif

                <label class="tf-label" for="password">
                    Contraseña
                </label>

                <input
                    class="tf-input {{ $errors->has('password') ? 'tf-input--invalid' : '' }}"
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    required
                    @if ($errors->has('password'))
                        aria-invalid="true"
                    @endif
                    aria-describedby="password-rules-password{{ $errors->has('password') ? ' password-error' : '' }}"
                >

                {{-- Lista de requisitos (resources/views/auth/partials/password-rules.blade.php) --}}
                @include('auth.partials.password-rules', ['for' => 'password'])

                @if ($errors->has('password'))
                    <div id="password-error">
                        @foreach ($errors->get('password') as $message)
                            <p class="tf-field-error">{{ $message }}</p>
                        @endforeach
                    </div>
                @endif

                <label class="tf-label" for="password_confirmation">
                    Repite contraseña
                </label>

                <input
                    class="tf-input"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                >

                <button class="auth-btn" type="submit">
                    Crear cuenta
                </button>

                <div class="tf-social-divider">o</div>

                @include('auth.partials.google-button')

                <p class="tf-register-footer">
                    ¿Ya tienes cuenta?

                    <a class="auth-link" href="{{ route('login') }}">
                        Iniciar sesión
                    </a>
                </p>
            </form>
        </section>
    </main>

</body>
</html>