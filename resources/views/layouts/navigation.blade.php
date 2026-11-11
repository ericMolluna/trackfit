<style>
    /* NAVEGACIÓN TRACKFIT */

    .tf-navbar.tn-navbar {
        position: sticky;
        top: 0;
        z-index: 100;
        width: 100%;
        border-bottom: 1px solid #6940a0;
        background: #08080b;
        color: #fff;
    }

    .tn-navbar,
    .tn-navbar * {
        box-sizing: border-box;
    }

    .tn-navbar .tn-container {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        align-items: center;
        gap: 24px;
        width: min(100% - 48px, 1280px);
        margin: 0 auto;
        padding: 14px 0;
    }

    /* LOGO */

    .tn-navbar .tn-logo {
        display: inline-flex;
        align-items: center;
        justify-self: start;
        min-width: 0;
        max-width: 160px;
        border-radius: 8px;
        text-decoration: none;
    }

    .tn-navbar .tn-logo img {
        display: block;
        width: auto;
        max-width: 100%;
        height: 60px;
        object-fit: contain;
    }

    /* ENLACES PRINCIPALES */

    .tn-navbar .tn-links {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 0;
    }

    .tn-navbar .tn-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 14px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: transparent;
        color: #cfc5da;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.4;
        white-space: nowrap;
        text-decoration: none;
        transition:
            color 0.2s,
            border-color 0.2s,
            background 0.2s;
    }

    .tn-navbar .tn-link:hover {
        background: #21152d;
        color: #fff;
    }

    .tn-navbar .tn-link[aria-current="page"] {
        border-color: #68428b;
        background: #261733;
        color: #dfc8ff;
    }

    /* PERFIL Y CIERRE DE SESIÓN */

    .tn-navbar .tn-account {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        justify-self: end;
        gap: 10px;
        min-width: 0;
        max-width: 100%;
    }

    .tn-navbar .tn-profile {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 0;
        max-width: 220px;
        min-height: 42px;
        padding: 10px 13px;
        border: 1px solid #6e4792;
        border-radius: 11px;
        background: #21152d;
        color: #f0e7fb;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
        text-decoration: none;
        transition:
            border-color 0.2s,
            background 0.2s;
    }

    .tn-navbar .tn-profile:hover,
    .tn-navbar .tn-profile[aria-current="page"] {
        border-color: #b794f6;
        background: #342044;
    }

    .tn-navbar .tn-name {
        display: block;
        min-width: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .tn-navbar .tn-profile-label {
        flex-shrink: 0;
        color: #cdb0f1;
    }

    .tn-navbar .tn-logout-form {
        flex-shrink: 0;
        margin: 0;
    }

    .tn-navbar .tn-logout {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        margin: 0;
        padding: 10px 12px;
        border: 1px solid #51405f;
        border-radius: 11px;
        background: transparent;
        color: #cfc2d9;
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
        text-transform: none;
        letter-spacing: normal;
        cursor: pointer;
        transition:
            border-color 0.2s,
            background 0.2s,
            color 0.2s;
    }

    .tn-navbar .tn-logout:hover {
        border-color: #995269;
        background: #2c1721;
        color: #fecdd3;
    }

    .tn-navbar a:focus-visible,
    .tn-navbar button:focus-visible {
        outline: 2px solid #c4b5fd;
        outline-offset: 3px;
    }

    /* TABLET Y MÓVIL: DOS FILAS */

    @media (max-width: 1000px) {
        .tn-navbar .tn-container {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            width: calc(100% - 32px);
            padding: 12px 0 10px;
        }

        .tn-navbar .tn-logo {
            grid-column: 1;
            grid-row: 1;
            max-width: 140px;
        }

        .tn-navbar .tn-logo img {
            height: 48px;
        }

        .tn-navbar .tn-account {
            grid-column: 2;
            grid-row: 1;
        }

        .tn-navbar .tn-links {
            grid-column: 1 / -1;
            grid-row: 2;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            padding-top: 10px;
            border-top: 1px solid #2d2038;
        }

        .tn-navbar .tn-link {
            width: 100%;
            padding: 10px 8px;
        }
    }

    @media (max-width: 480px) {
        .tn-navbar .tn-container {
            width: calc(100% - 24px);
            column-gap: 10px;
        }

        .tn-navbar .tn-logo {
            max-width: 105px;
        }

        .tn-navbar .tn-logo img {
            height: 40px;
        }

        .tn-navbar .tn-account {
            gap: 6px;
        }

        .tn-navbar .tn-profile {
            max-width: 155px;
            padding: 10px;
            font-size: 12px;
        }

        .tn-navbar .tn-logout {
            padding: 10px 9px;
            font-size: 11px;
        }

        .tn-navbar .tn-link {
            font-size: 13px;
        }

        .tn-navbar .tn-logout-desktop {
            display: none;
        }
    }

    @media (min-width: 481px) {
        .tn-navbar .tn-logout-mobile {
            display: none;
        }
    }

    /* PANTALLAS MUY ESTRECHAS */

    @media (max-width: 360px) {
        .tn-navbar .tn-logo {
            max-width: 85px;
        }

        .tn-navbar .tn-profile {
            max-width: 125px;
        }

        .tn-navbar .tn-profile-label {
            display: none;
        }
    }
</style>

<nav class="tf-navbar tn-navbar" aria-label="Navegación principal">
    <div class="tn-container">

        {{-- LOGO --}}
        <a
            class="tn-logo"
            href="{{ route('dashboard') }}"
            aria-label="TrackFit: ir al inicio"
        >
            <img
                src="{{ asset('img/trackfit.png') }}"
                alt="TrackFit"
            >
        </a>

        {{-- NAVEGACIÓN PRINCIPAL --}}
        <div class="tn-links">
            <a
                class="tn-link"
                href="{{ route('rutina') }}"
                @if (
                    request()->routeIs('rutina')
                    || request()->routeIs('rutina.*')
                    || request()->routeIs('routines.*')
                    || request()->routeIs('workout.*')
                )
                    aria-current="page"
                @endif
            >
                Rutina
            </a>

            <a
                class="tn-link"
                href="{{ route('progress.index') }}"
                @if (request()->routeIs('progress.*'))
                    aria-current="page"
                @endif
            >
                Progreso
            </a>

            <a
                class="tn-link"
                href="{{ route('nutrition.today') }}"
                @if (request()->routeIs('nutrition.*'))
                    aria-current="page"
                @endif
            >
                Nutrición
            </a>
        </div>

        {{-- PERFIL Y CIERRE DE SESIÓN --}}
        <div class="tn-account">
            @auth
                <a
                    class="tn-profile"
                    href="{{ route('profile.edit') }}"
                    aria-label="Abrir mi perfil"
                    title="{{ auth()->user()->name }} · Mi perfil"
                    @if (request()->routeIs('profile.edit'))
                        aria-current="page"
                    @endif
                >
                    <span class="tn-name">
                        {{ auth()->user()->name }}
                    </span>

                    <span class="tn-profile-label" aria-hidden="true">
                        · Perfil
                    </span>
                </a>

                <form
                    class="tn-logout-form"
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        class="tn-logout"
                        type="submit"
                        aria-label="Cerrar sesión"
                    >
                        <span class="tn-logout-desktop">
                            Cerrar sesión
                        </span>

                        <span class="tn-logout-mobile">
                            Salir
                        </span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>