@php
    $provider = auth()->user()->provider;
    $isSocial = ! empty($provider);

    $providerName = match ($provider) {
        'google' => 'Google',
        'github' => 'GitHub',
        default => 'tu proveedor de acceso',
    };

    $passwordFields = [
        [
            'name' => 'current_password',
            'label' => 'Contraseña actual',
            'autocomplete' => 'current-password',
        ],
        [
            'name' => 'password',
            'label' => 'Nueva contraseña',
            'autocomplete' => 'new-password',
        ],
        [
            'name' => 'password_confirmation',
            'label' => 'Repite la nueva contraseña',
            'autocomplete' => 'new-password',
        ],
    ];
@endphp

<section aria-labelledby="profile-security-title">
    <header>
        <h2 class="pf-section-title" id="profile-security-title">
            Seguridad de la cuenta
        </h2>

        <p class="pf-section-description">
            @if ($isSocial)
                Esta cuenta está vinculada a {{ $providerName }}.
            @else
                Cambia tu contraseña para proteger el acceso a TrackFit.
            @endif
        </p>
    </header>

    @if ($isSocial)
        <div class="pf-form">
            <p class="pf-notice">
                El cambio de contraseña desde este formulario no está
                disponible para cuentas vinculadas a {{ $providerName }}.
                Puedes seguir utilizando ese método para iniciar sesión.
            </p>
        </div>
    @else
        <form
            method="POST"
            action="{{ route('password.update') }}"
            class="pf-form"
        >
            @csrf
            @method('PUT')

            @if (session('status') === 'password-updated')
                <p class="pf-notice pf-success" role="status">
                    Tu contraseña se ha actualizado correctamente.
                </p>
            @endif

            @foreach ($passwordFields as $field)
                @php
                    $fieldName = $field['name'];
                    $fieldId = 'profile-password-' . $fieldName;
                    $fieldErrors = $errors->updatePassword->get($fieldName);
                @endphp

                <div class="pf-field" x-data="{ visible: false }">
                    <label class="pf-label" for="{{ $fieldId }}">
                        {{ $field['label'] }}
                    </label>

                    <div class="pf-password-wrap">
                        <input
                            class="pf-input"
                            id="{{ $fieldId }}"
                            name="{{ $fieldName }}"
                            type="password"
                            x-bind:type="visible ? 'text' : 'password'"
                            autocomplete="{{ $field['autocomplete'] }}"
                            required
                            @if (count($fieldErrors))
                                aria-invalid="true"
                                aria-describedby="{{ $fieldId }}-errors"
                            @endif
                        >

                        <button
                            type="button"
                            class="pf-toggle"
                            x-on:click="visible = ! visible"
                            x-bind:aria-pressed="visible"
                            aria-controls="{{ $fieldId }}"
                            aria-label="Mostrar u ocultar: {{ $field['label'] }}"
                        >
                            <span x-text="visible ? 'Ocultar' : 'Mostrar'">
                                Mostrar
                            </span>
                        </button>
                    </div>

                    @if ($fieldName === 'password')
                        @include('auth.partials.password-rules', ['for' => $fieldId])
                    @endif

                    @if (count($fieldErrors))
                        <div id="{{ $fieldId }}-errors">
                            @foreach ($fieldErrors as $error)
                                <p class="pf-error">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <p class="pf-help">
                Utiliza una contraseña larga y diferente de las que
                tengas en otras cuentas.
            </p>

            <div class="pf-actions">
                <button class="pf-button" type="submit">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    @endif
</section>