<x-app-layout>
    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-dashboard.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/trackfit-profile.css') }}"
    >

    @php
        $provider = $user->provider;

        $accessMethod = match ($provider) {
            'google' => 'Acceso con Google',
            'github' => 'Acceso con GitHub',
            null, '' => 'Acceso con correo y contraseña',
            default => 'Acceso externo',
        };

        $requiresVerification =
            $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail;
    @endphp

    <div class="tf-profile">
        <div class="pf-shell">
            <header class="pf-heading">
                <h1>Mi perfil</h1>
                <p>Gestiona tu información personal y la seguridad de tu cuenta.</p>
            </header>

            <section class="pf-account" aria-label="Resumen de tu cuenta">
                <div class="pf-avatar" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>

                <div class="pf-account-details">
                    <h2 class="pf-account-name">
                        {{ $user->name }}
                    </h2>

                    <p class="pf-account-email">
                        {{ $user->email }}
                    </p>

                    <div class="pf-badges">
                        @if ($requiresVerification)
                            @if ($user->hasVerifiedEmail())
                                <span class="pf-badge pf-badge-verified">
                                    Correo verificado ✓
                                </span>
                            @else
                                <span class="pf-badge pf-badge-pending">
                                    Correo pendiente de verificar
                                </span>
                            @endif
                        @endif

                        <span class="pf-badge">
                            {{ $accessMethod }}
                        </span>
                    </div>
                </div>
            </section>

            <div class="pf-sections">
                <div class="pf-card">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="pf-card">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="pf-card pf-card-danger">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>