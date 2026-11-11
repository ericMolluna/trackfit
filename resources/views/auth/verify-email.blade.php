<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Verifica tu correo - TrackFit</title>

    <style>
        :root {
            color-scheme: dark;
            --background: #08080b;
            --card: #111015;
            --text: #f7f5fc;
            --muted: #b9b2c5;
            --purple: #7c3aed;
            --purple-light: #c4b5fd;
            --border: #44305f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(
                    ellipse at top,
                    rgba(124, 58, 237, 0.16),
                    transparent 60%
                ),
                var(--background);
            color: var(--text);
        }

        .verify-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100svh;
            padding: 32px 18px;
        }

        .verify-card {
            width: 100%;
            max-width: 520px;
            padding: 36px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--card);
            box-shadow: 0 20px 70px rgba(0, 0, 0, 0.35);
        }

        .brand {
            margin: 0 0 32px;
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .brand span {
            color: #a78bfa;
        }

        .verify-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            margin-bottom: 24px;
            border: 1px solid #67429a;
            border-radius: 22px;
            background: #251737;
            color: var(--purple-light);
        }

        .verify-icon svg {
            width: 38px;
            height: 38px;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--purple-light);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 14px;
            font-size: clamp(26px, 6vw, 32px);
            line-height: 1.2;
            letter-spacing: -0.8px;
        }

        .description {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .email-box {
            margin: 20px 0;
            padding: 15px 16px;
            border: 1px solid #3c2c51;
            border-radius: 12px;
            background: #1b1524;
        }

        .email-label {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
        }

        .email-address {
            margin: 0;
            color: #ede9fe;
            font-size: 15px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .steps {
            display: grid;
            gap: 14px;
            margin: 24px 0;
            padding: 0;
            list-style: none;
        }

        .steps li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #d5cede;
            font-size: 14px;
            line-height: 1.6;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 26px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #2a1b3e;
            color: #d4c4ff;
            font-size: 12px;
            font-weight: 700;
        }

        .status {
            margin: 20px 0;
            padding: 14px 16px;
            border: 1px solid #35634d;
            border-radius: 12px;
            background: #13271e;
            color: #b7f3cf;
            font-size: 14px;
            line-height: 1.6;
        }

        .error {
            margin: 20px 0;
            padding: 14px 16px;
            border: 1px solid #753c4a;
            border-radius: 12px;
            background: #301820;
            color: #fecdd3;
            font-size: 14px;
            line-height: 1.6;
        }

        .actions {
            display: grid;
            gap: 12px;
            margin-top: 26px;
        }

        .actions form {
            margin: 0;
        }

        .button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 48px;
            padding: 13px 18px;
            border: 1px solid transparent;
            border-radius: 12px;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition:
                background 0.2s,
                border-color 0.2s;
        }

        .button-primary {
            background: var(--purple);
            color: #fff;
        }

        .button-primary:hover {
            background: #6d28d9;
        }

        .button-secondary {
            border-color: #49345f;
            background: transparent;
            color: #ddd1f2;
        }

        .button-secondary:hover {
            border-color: #9671c0;
            background: #201629;
        }

        .help {
            margin: 22px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
            text-align: center;
        }

        .footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid #2e2539;
        }

        .footer p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .logout {
            padding: 4px 0;
            border: 0;
            background: transparent;
            color: var(--purple-light);
            font: inherit;
            font-size: 13px;
            cursor: pointer;
        }

        .logout:hover {
            text-decoration: underline;
        }

        a:focus-visible,
        button:focus-visible {
            outline: 2px solid var(--purple-light);
            outline-offset: 4px;
        }

        @media (max-width: 480px) {
            .verify-card {
                padding: 26px 22px;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <main class="verify-page">
        <section class="verify-card" aria-labelledby="verify-title">
            <p class="brand">Track<span>Fit</span></p>

            <div class="verify-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="5" width="18" height="14" rx="3"></rect>
                    <path d="m4 7 8 6 8-6"></path>
                </svg>
            </div>

            <p class="eyebrow">Un último paso</p>

            <h1 id="verify-title">Revisa tu correo</h1>

            <p class="description">
                Tu cuenta ya está creada. Ahora confirma tu correo
                para empezar a utilizar TrackFit.
            </p>

            <div class="email-box">
                <span class="email-label">
                    Dirección de tu cuenta
                </span>

                <p class="email-address">
                    {{ auth()->user()->email }}
                </p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="status" role="status">
                    Hemos enviado otro enlace de verificación.
                    Revisa tu bandeja de entrada y la carpeta de spam.
                </div>
            @endif

            @if ($errors->any())
                <div class="error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <ol class="steps">
                <li>
                    <span class="step-number" aria-hidden="true">1</span>
                    <span>
                        Abre el correo que te hemos enviado desde TrackFit.
                    </span>
                </li>

                <li>
                    <span class="step-number" aria-hidden="true">2</span>
                    <span>
                        Pulsa «Verificar mi correo» dentro del mensaje.
                    </span>
                </li>

                <li>
                    <span class="step-number" aria-hidden="true">3</span>
                    <span>
                        Si esta pestaña sigue abierta después de verificar,
                        pulsa «Ya he verificado mi correo».
                    </span>
                </li>
            </ol>

            <div class="actions">
                <a
                    class="button button-primary"
                    href="{{ route('dashboard') }}"
                >
                    Ya he verificado mi correo
                </a>

                <form
                    method="POST"
                    action="{{ route('verification.send') }}"
                >
                    @csrf

                    <button
                        class="button button-secondary"
                        type="submit"
                    >
                        Reenviar correo de verificación
                    </button>
                </form>
            </div>

            <p class="help">
                ¿No encuentras el mensaje? Revisa spam o correo no deseado.
                Puede tardar unos minutos en aparecer.
            </p>

            <div class="footer">
                <p>¿Quieres utilizar otra cuenta?</p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="logout" type="submit">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>